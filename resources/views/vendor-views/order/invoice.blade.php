@php use Carbon\Carbon; @endphp

<div class="content container-fluid initial-38 new-invoice">
    <div class="row justify-content-center mx-auto" id="printableArea">
        <div class="col-md-12">
            <!--<div class="text-center">-->
            <!--    <input type="button" class="btn text-white btn--primary non-printable print-Div"-->
            <!--        value="{{ translate('messages.Proceed_If_thermal_printer_is_ready.') }}" />-->
            <!--    <a href="{{ url()->previous() }}" class="btn btn-danger non-printable">{{ translate('messages.back') }}</a>-->
            <!--</div>-->
            <!--<hr class="non-printable">-->

            <div class="initial-38-1">
                <div class="pt-3">
                    <img src="{{ dynamicAsset('/public/assets/admin/img/restaurant-invoice.png') }}"
                        class="initial-38-2" alt="">
                </div>
                <div class="text-center pt-3 mb-3">
                    <h3>{{ $order->restaurant->name }}</h3>
                    <h5 class="text-break initial-30-4">
                        {{ $order->restaurant->address }}
                    </h5>
                    {{-- <h5 class="text-muted">{{ Carbon\Carbon::parse($order['created_at'])->locale(app()->getLocale())->translatedFormat('d/M/Y ' . config('timeformat')) }}</h5> --}}
                    <h5>
                        <span>{{ translate('phone') }}</span> <span>:</span>
                        <span>{{ $order->restaurant->phone }}</span>
                    </h5>
                    {{-- @if ($order->restaurant->gst_status)
                    <h5 class="initial-38-4 initial-38-3 fz-12px text-center">
                        <span>{{ translate('Gst_No') }}</span> <span>:</span> <span>{{ $order->restaurant->gst_code }}</span>
                    </h5>
                    @endif --}}
                </div>

                {{-- <h5 class="d-flex justify-content-between gap-2">
                    <span>{{ translate('Order_Type') }}</span>
                    <span>{{ $order->order_type == 'delivery' ? translate('Home_Delivery') : translate($order->order_type) }}</span>
                </h5> --}}
                <hr style="border: black dashed 1.3px; margin: .1rem 0;">
                <div class="text-center">
                    <h4>Collection</h4>
                    <span class="text-muted"><b>{{ translate('Order_Number') }}</b> #{{ $order['id'] }}</span><br>
                    <span class="text-muted"><b>Due At:</b>
                        {{ \Carbon\Carbon::parse($order['schedule_at'])->format('d M Y h:i A') }}</span><br>
                </div>
                <hr style="border: black dashed 1.3px; margin: .1rem 0;">
                <div class="text-center">
                    <span class="text-muted">
                        @if ($order['payment_status'] == 'unpaid')
                            Order Not Paid
                        @else
                            Order has been Paid
                        @endif
                    </span>
                </div>
                <hr style="border: black dashed 1.3px; margin: .1rem 0;">

                <table class="table table-borderless table-align-middle mt-1 mb-1">
                    <tbody>
                        @php($sub_total = 0)
                        @php($total_tax = 0)
                        @php($total_dis_on_pro = 0)
                        @php($add_ons_cost = 0)
                        @foreach ($order->details as $detail)
                            @if ($detail->food_id || $detail->campaign == null)
                                <tr>
                                    <td class="text-break">
                                        {{ $detail['quantity'] }}x
                                        {{ json_decode($detail->food_details, true)['name'] }}
                                        @if (count(json_decode($detail['variation'], true)) > 0)
                                            <strong>{{ translate('messages.variation') }} :</strong>
                                            @foreach (json_decode($detail['variation'], true) as $variation)
                                                @if (isset($variation['name']) && isset($variation['values']))
                                                    @foreach ($variation['values'] as $value)
                                                        <span class="d-block text-capitalize" style="font-size:12px;">
                                                            {{ $value['label'] }}
                                                        </span>
                                                    @endforeach
                                                @endif
                                            @endforeach
                                        @else
                                            <div class="font-size-sm text-body">
                                                <span>{{ translate('messages.Price') }} :</span>
                                                <span class="font-weight-bold">
                                                    {{ \App\CentralLogics\Helpers::format_currency($detail->price) }}
                                                </span>
                                            </div>
                                        @endif

                                        @foreach (json_decode($detail['add_ons'], true) as $key2 => $addon)
                                            @if ($key2 == 0)
                                                <strong><u>{{ translate('messages.addons') }} :</u></strong>
                                            @endif
                                            <div class="font-size-sm text-body">
                                                <span>{{ $addon['name'] }} :</span>
                                                <span class="font-weight-bold">
                                                    {{ $addon['quantity'] }} x
                                                    {{ \App\CentralLogics\Helpers::format_currency($addon['price']) }}
                                                </span>
                                            </div>
                                            @php($add_ons_cost += $addon['price'] * $addon['quantity'])
                                        @endforeach
                                    </td>
                                    <td class="text-right w-28p">
                                        @php($amount = $detail['price'] * $detail['quantity'])
                                        {{ \App\CentralLogics\Helpers::format_currency($amount) }}
                                    </td>
                                </tr>
                                @php($sub_total += $amount)
                                @php($total_tax += $detail['tax_amount'] * $detail['quantity'])
                            @elseif($detail->campaign)
                                <tr>
                                    <td class="">
                                        {{ $detail['quantity'] }}x {{ $detail->campaign['title'] }}
                                    </td>
                                    <td class="w-28p">
                                        @php($amount = $detail['price'] * $detail['quantity'])
                                        {{ \App\CentralLogics\Helpers::format_currency($amount) }}
                                    </td>
                                </tr>
                                @php($sub_total += $amount)
                                @php($total_tax += $detail['tax_amount'] * $detail['quantity'])
                            @endif
                        @endforeach
                    </tbody>
                </table>

                <hr style="border: black dashed 1.3px; margin: .1rem 0;">
                <div class="initial-38-9 px-3">
                    <dl class="row text-right">
                        @if ($add_ons_cost > 0)
                            <dt class="col-6 text-left text-muted">{{ translate('Addon_Cost') }}</dt>
                            <dd class="col-6">{{ \App\CentralLogics\Helpers::format_currency($add_ons_cost) }}</dd>
                        @endif

                        <dt class="col-6 text-left fw-500">{{ translate('messages.subtotal') }} @if ($order->tax_status == 'included')
                                (TAX Included)
                            @endif
                        </dt>
                        <dd class="col-6 fw-500">
                            {{ \App\CentralLogics\Helpers::format_currency($sub_total + $add_ons_cost) }}</dd>

                        @if ($order['restaurant_discount_amount'] > 0)
                            <dt class="col-6 text-left text-muted">{{ translate('messages.discount') }}</dt>
                            <dd class="col-6">-
                                {{ \App\CentralLogics\Helpers::format_currency($order['restaurant_discount_amount']) }}
                            </dd>
                        @endif

                        @if ($order['coupon_discount_amount'] > 0)
                            <dt class="col-6 text-left text-muted">{{ translate('messages.coupon_discount') }}</dt>
                            <dd class="col-6">-
                                {{ \App\CentralLogics\Helpers::format_currency($order['coupon_discount_amount']) }}
                            </dd>
                        @endif

                        @if ($order['ref_bonus_amount'] > 0)
                            <dt class="col-6 text-left text-muted">{{ translate('messages.Referral_Discount') }}:</dt>
                            <dd class="col-6">-
                                {{ \App\CentralLogics\Helpers::format_currency($order['ref_bonus_amount']) }}</dd>
                        @endif

                        @if ($order['total_tax_amount'] > 0 && ($order->tax_status == 'excluded' || $order->tax_status == null))
                            <dt class="col-6 text-left text-muted">{{ translate('messages.vat/tax') }}</dt>
                            <dd class="col-6">
                                {{ \App\CentralLogics\Helpers::format_currency($order['total_tax_amount']) }}</dd>
                        @endif

                        @if ($order['delivery_charge'] > 0)
                            <dt class="col-6 text-left text-muted">{{ translate('messages.delivery_charge') }}</dt>
                            <dd class="col-6">
                                {{ \App\CentralLogics\Helpers::format_currency($order['delivery_charge']) }}</dd>
                        @endif

                        <dt class="col-6 text-left fw-500 fz-20px">{{ translate('messages.total') }}</dt>
                        <dd class="col-6 fz-20px fw-500">
                            {{ \App\CentralLogics\Helpers::format_currency($order['order_amount']) }}</dd>
                    </dl>
                </div>

                <hr style="border: black dashed 1.3px; margin: .1rem 0;">
                @if ($order['order_note'])
                    <span><strong>Customer Notes:</strong> {{ $order['order_note'] }}</span>
                    <hr style="border: black dashed 1.3px; margin: .1rem 0;">
                @endif

                <div class="p-3">
                    @if ($order->delivery_address)
                        <h5 class="d-flex justify-content-between gap-2">
                            <span class="text-muted">{{ translate('Customer_Name') }}</span>
                            <span>{{ json_decode($order->delivery_address, true)['contact_person_name'] }}</span>
                        </h5>
                        <h5 class="d-flex justify-content-between gap-2">
                            <span class="text-muted">{{ translate('messages.phone') }}</span>
                            <span>{{ json_decode($order->delivery_address, true)['contact_person_number'] }}</span>
                        </h5>
                        @if (!in_array($order->order_type, ['dine_in', 'take_away']))
                            <h5 class="d-flex justify-content-between gap-2 text-break">
                                <span class="text-muted">{{ translate('messages.delivery_Address') }}</span>
                                <span>{{ json_decode($order->delivery_address, true)['address'] }}</span>
                            </h5>
                        @endif
                    @else
                        <h5 class="d-flex justify-content-between gap-2">
                            {{ translate('Customer_Name') }} :
                            <span>{{ translate('messages.walk_in_customer') }}</span>
                        </h5>
                    @endif
                </div>

                <h5 class="text-center pt-1 mb-0">
                    <span class="d-block fw-500">{{ translate('messages.THANK_YOU') }}</span>
                </h5>
                <div class="text-center">Powered By G Tech Nexa Limited</div>

                {{-- ────────────── ACTION BUTTONS (non-printable) ────────────── --}}
                <div class="text-center non-printable mt-3">
                    @if ($order->order_status === 'pending')
                        {{-- New Orders: Confirm & Print + Cancel --}}
                        <a href="{{ route('vendor.order.status', ['id' => $order->id, 'order_status' => 'confirmed']) }}"
                            class="btn btn-sm btn-success print-Div">
                            {{ translate('Confirm & Print') }}
                        </a>

                        @if(Carbon::now()->lte(Carbon::parse($order->created_at)->addMinutes(30)))

<a href="#"
   class="btn btn-sm btn-danger cancelled-status"
   data-toggle="modal"
   data-target="#cancelModal"
   data-url="{{ route('vendor.order.status', ['id'=>$order->id,'order_status'=>'canceled']) }}">
  {{ translate('Cancel') }}
</a>
@endif


                       
                    @elseif($order->order_status === 'confirmed')
                        {{-- Accepted Orders: Print + Cancel --}}
                        <button type="button" class="btn btn-sm btn-primary print-Div">Print</button>
                                                @if(Carbon::now()->lte(Carbon::parse($order->created_at)->addMinutes(30)))

              <a href="#"
   class="btn btn-sm btn-danger cancelled-status"
   data-toggle="modal"
   data-target="#cancelModal"
   data-url="{{ route('vendor.order.status', ['id'=>$order->id,'order_status'=>'canceled']) }}">
  {{ translate('Cancel') }}
</a>
@endif
                    @elseif($order->order_status === 'canceled')
                        {{-- Canceled Orders: Print + Refund --}}
                        <button type="button" class="btn btn-sm btn-primary print-Div">Print</button>
                        @if (in_array($order->payment_method, ['card', 'digital_payment']))
                            <a href="javascript:" class="btn btn-sm btn-warning order-status-change-alert"
                                data-url="{{ route('vendor.order.status', ['id' => $order->id, 'order_status' => 'refunded']) }}"
                                data-message="{{ translate('Change status to refunded ?') }}">
                                Refund
                            </a>
                        @endif
                    @else
                        {{-- Fallback: just Print --}}
                        <button type="button" class="btn btn-sm btn-primary print-Div">Print</button>
                    @endif
                </div>

            </div>
        </div>
    </div>
</div>


