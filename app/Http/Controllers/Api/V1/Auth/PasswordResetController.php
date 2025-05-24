<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Models\BusinessSetting;
use App\Models\User;
use Carbon\CarbonInterval;
use Illuminate\Http\Request;
use App\CentralLogics\Helpers;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Http;
use App\Mail\PasswordResetMail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

class PasswordResetController extends Controller
{
    public function reset_password_request(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        $firebase_otp_verification = BusinessSetting::where('key', 'firebase_otp_verification')->first()->value ?? 0;

        $customer = User::where(['email' => $request['email']])->first();

        if (!isset($customer)) {
            return response()->json(['errors' => [
                ['code' => 'not-found', 'message' => translate('Email does not exist')]
            ]], 404);
        }

        // if ($firebase_otp_verification) {
            // return response()->json(['message' => translate('Verification email sent')], 200);
        // }

        $otp_interval_time = 60; // seconds
        $password_verification_data = DB::table('password_resets')->where('email', $customer['email'])->first();
        
        if (isset($password_verification_data) && Carbon::parse($password_verification_data->created_at)->DiffInSeconds() < $otp_interval_time) {
            $time = $otp_interval_time - Carbon::parse($password_verification_data->created_at)->DiffInSeconds();
            $errors = [];
            array_push($errors, [
                'code' => 'otp',
                'message' => translate('Please try again after ') . $time . ' ' . translate('seconds')
            ]);
            return response()->json(['errors' => $errors], 405);
        }

        $token = rand(100000, 999999);
        if (env('APP_MODE') == 'test') {
            $token = '123456';
        }

        DB::table('password_resets')->updateOrInsert(
            ['email' => $customer['email']],
            [
                'token' => $token,
                'created_at' => now(),
            ]
        );

        try {
            // Mail::to($customer['email'])->send(new PasswordResetRequestMail($token , "saqib"));
            Mail::to($customer['email'])->send(new PasswordResetMail($token));
            return response()->json(['message' => translate('OTP sent to your email: ') . $customer['email']], 200);
        } catch (\Exception $e) {
            return response()->json([
                'errors' => [
                    ['code' => 'email-failed', 'message' => translate('Failed to send email')]
                ]
            ], 405);
        }
    }

    public function verify_token(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'reset_token' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        $user = User::where('email', $request->email)->first();
        if (!isset($user)) {
            return response()->json(['errors' => [
                ['code' => 'not-found', 'message' => translate('Email not registered')]
            ]], 404);
        }

        if (env('APP_MODE') == 'test') {
            if ($request['reset_token'] == "123456") {
                return response()->json(['message' => translate("OTP verified")], 200);
            }
            return response()->json(['errors' => [
                ['code' => 'invalid', 'message' => translate('Invalid OTP code')]
            ]], 400);
        }

        $data = DB::table('password_resets')->where([
            'token' => $request['reset_token'],
            'email' => $user->email
        ])->first();

        if (isset($data)) {
            return response()->json(['message' => translate('OTP verification successful')], 200);
        }

        $max_otp_hit = 5;
        $max_otp_hit_time = 60; // seconds
        $temp_block_time = 600; // seconds
        $verification_data = DB::table('password_resets')->where('email', $user->email)->first();

        if (isset($verification_data)) {
            if (isset($verification_data->temp_block_time) && 
                Carbon::parse($verification_data->temp_block_time)->DiffInSeconds() <= $temp_block_time) {
                $time = $temp_block_time - Carbon::parse($verification_data->temp_block_time)->DiffInSeconds();
                $errors = [];
                array_push($errors, [
                    'code' => 'otp_block_time', 
                    'message' => translate('Please try again after ') . 
                                CarbonInterval::seconds($time)->cascade()->forHumans()
                ]);
                return response()->json(['errors' => $errors], 405);
            }

            if ($verification_data->is_temp_blocked == 1 && 
                Carbon::parse($verification_data->created_at)->DiffInSeconds() >= $max_otp_hit_time) {
                DB::table('password_resets')->updateOrInsert(
                    ['email' => $user->email],
                    [
                        'otp_hit_count' => 0,
                        'is_temp_blocked' => 0,
                        'temp_block_time' => null,
                        'created_at' => now(),
                    ]
                );
            }

            if ($verification_data->otp_hit_count >= $max_otp_hit && 
                Carbon::parse($verification_data->created_at)->DiffInSeconds() < $max_otp_hit_time && 
                $verification_data->is_temp_blocked == 0) {
                DB::table('password_resets')->updateOrInsert(
                    ['email' => $user->email],
                    [
                        'is_temp_blocked' => 1,
                        'temp_block_time' => now(),
                        'created_at' => now(),
                    ]
                );
                $errors = [];
                array_push($errors, [
                    'code' => 'otp_temp_blocked', 
                    'message' => translate('Too many attempts')
                ]);
                return response()->json(['errors' => $errors], 405);
            }
        }

        DB::table('password_resets')->updateOrInsert(
            ['email' => $user->email],
            [
                'otp_hit_count' => DB::raw('otp_hit_count + 1'),
                'created_at' => now(),
                'temp_block_time' => null,
            ]
        );

        return response()->json(['errors' => [
            ['code' => 'invalid', 'message' => translate('Incorrect OTP code')]
        ]], 400);
    }

    public function reset_password_submit(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
            'reset_token' => 'required',
            'password' => ['required', Password::min(8)],
            'confirm_password' => 'required|same:password',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        if (env('APP_MODE') == 'test') {
            if ($request['reset_token'] == "123456") {
                DB::table('users')->where(['email' => $request['email']])->update([
                    'password' => bcrypt($request['confirm_password'])
                ]);
                return response()->json(['message' => translate('Password updated successfully')], 200);
            }
            return response()->json([
                'message' => translate('OTP verification failed')
            ], 404);
        }

        $user = User::where(['email' => $request->email])->first();
        $data = DB::table('password_resets')->where([
            'token' => $request['reset_token'], 
            'email' => $user->email
        ])->first();

        if (isset($data)) {
            if ($request['password'] == $request['confirm_password']) {
                $user->password = bcrypt($request['confirm_password']);
                $user->save();
                DB::table('password_resets')->where(['token' => $request['reset_token']])->delete();
                return response()->json(['message' => translate('Password updated successfully')], 200);
            }
            return response()->json(['errors' => [
                ['code' => 'mismatch', 'message' => translate('Passwords do not match')]
            ]], 401);
        }
        return response()->json(['errors' => [
            ['code' => 'invalid', 'message' => translate('Invalid OTP code')]
        ]], 400);
    }

    public function firebase_auth_verify(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'sessionInfo' => 'required',
            'email' => 'required|email',
            'code' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        $webApiKey = BusinessSetting::where('key', 'firebase_web_api_key')->first()->value ?? '';

        $response = Http::post('https://identitytoolkit.googleapis.com/v1/accounts:signInWithEmail?key=' . $webApiKey, [
            'sessionInfo' => $request->sessionInfo,
            'email' => $request->email,
            'code' => $request->code,
        ]);

        $responseData = $response->json();

        if (isset($responseData['error'])) {
            $errors = [];
            $errors[] = ['code' => "403", 'message' => $responseData['error']['message']];
            return response()->json(['errors' => $errors], 403);
        }

        $user = User::where(['email' => $request->email])->first();

        if (isset($user)) {
            if ($request['is_reset_token'] == 1) {
                DB::table('password_resets')->updateOrInsert(
                    ['email' => $user->email],
                    [
                        'token' => $request->code,
                        'created_at' => now(),
                    ]
                );
                return response()->json(['message' => translate("OTP verified")], 200);
            } else {
                if ($user->is_email_verified) {
                    return response()->json([
                        'message' => translate('Email already verified')
                    ], 200);
                }
                $user->is_email_verified = 1;
                $user->save();

                return response()->json([
                    'message' => translate('Email verified successfully'),
                    'otp' => 'inactive'
                ], 200);
            }
        }

        return response()->json([
            'message' => translate('User not found')
        ], 404);
    }
}