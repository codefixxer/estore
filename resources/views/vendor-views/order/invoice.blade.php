@php use Carbon\Carbon; @endphp
<style>
.btn-cancel-custom {
   background-color: #FF0800;
    color: white !important;
    border-color: #FF0800;
}

.btn-cancel-custom:hover {
    background-color: #FF0800;
    color: white !important;
    border-color: #FF0800;
}

.btn-print-custom {
 background-color: #007FFF;
    color: white !important;
    border-color: #007FFF;
}

.btn-print-custom:hover {
    background-color: #007FFF;
    color: white !important;
    border-color: #007FFF;
}


.btn-green-custom {
      background-color: #28a428 !important; /* slightly darker on hover */
    border-color: #28a428 !important;
    color: white !important
}
.btn-green-custom:hover {
    background-color: #28a428 !important; /* slightly darker on hover */
    border-color: #28a428 !important;
        color: white !important

}


/* Dish name font size smaller */
    .invoice-dish-name {
        font-size: 12px;
        /* 2px smaller than default */
        display: inline-block;
        vertical-align: middle;
        margin-left: 5px;
        /* space from dots */
    }

    /* Variation label font size smaller */
    .invoice-variation-label {
        font-size: 11px;
        /* 3px smaller than default */
        font-weight: bold;
    }

    /* Variation value font size smaller and on next line */
    .invoice-variation-value {
        font-size: 11px;
        margin-left: 15px;
        display: block;
    }

    /* Amount and Euro symbol in one line and smaller */
    .invoice-amount {
        font-size: 12px;
        display: inline-block;
        vertical-align: middle;
    }

    .invoice-euro-symbol {
        font-size: 12px;
        display: inline-block;
        vertical-align: middle;
        margin-left: 3px;
    }

    /* Reduce font size for Total label and amount */
    .invoice-total-label {
        font-size: 16px;
        font-weight: 600;
    }

    .invoice-total-amount {
        font-size: 16px;
        font-weight: 600;
    }

    .invoice-item-name {
        font-size: 14px;
        /* Default size for item name */
    }

    .invoice-variation-label {
        font-size: 11px;
        /* Chhoti font size for Variation label */
        font-weight: bold;
        margin-left: 15px;
        display: block;
        margin-top: 2px;
    }

    .invoice-variation-value {
        font-size: 11px;
        /* Variation values ki chhoti size */
        margin-left: 25px;
        display: block;
        font-style: normal;
        margin-top: 0;
    }
</style>

<div class="content container-fluid initial-38 new-invoice">
    <div class="row justify-content-center mx-auto" id="printableArea">
        <div class="col-md-12">


            <div class="initial-38-1">
                <div class="pt-3">
                    <img src="{{ dynamicAsset('/public/assets/admin/img/restaurant-invoice.png') }}" class="initial-38-2"
                        alt="">
                </div>
                <div class="text-center pt-3 mb-3">
                    <h3 class="">{{ $order->restaurant->name }}</h3>
                    <h5 class="text-break initial-30-4">
                        {{ $order->restaurant->address }}
                    </h5>

                    <h5>
                        <span>{{ translate('phone') }}</span> <span>:</span>
                        <span>{{ $order->restaurant->phone }}</span>
                    </h5>

                </div>

                <hr style="border: black dashed 1.3px; margin-top: .1rem;
    margin-bottom: .1rem;">
                <div class="text-center">
                    <div class="text-center">
                        <h4>
                            @if ($order->order_type === 'take_away')
                                {{ translate('Collection') }}
                            @elseif($order->order_type === 'delivery')
                                {{ translate('Delivery') }}
                            @else
                                {{ translate(ucfirst(str_replace('_', ' ', $order->order_type))) }}
                            @endif
                        </h4>
                        <span class="text-muted">
                            <b>{{ translate('Order_Number') }}</b> #{{ $order['id'] }}
                        </span><br>
                        <span class="text-muted">
                            <b>Due At:</b> {{ \Carbon\Carbon::parse($order['schedule_at'])->format('d M Y h:i A') }}
                        </span><br>
                    </div>
                </div>

                <hr style="border: black dashed 1.3px; margin-top: .1rem;
    margin-bottom: .1rem;">
                <div class="text-center">
                    <span class="text-muted">
                        @if ($order['payment_status'] == 'unpaid')
                            Order Not Paid
                        @else
                            Order has been Paid
                        @endif
                    </span>
                </div>
                <hr style="border: black dashed 1.3px; margin-top: .1rem;
    margin-bottom: .1rem;">
                <table class="table table-borderless table-align-middle mt-1 mb-1">

<tbody>
    @php($sub_total = 0)
    @php($total_tax = 0)
    @php($total_dis_on_pro = 0)
    @php($add_ons_cost = 0)

    @foreach ($order->details as $detail)
        @if ($detail->food_id || $detail->campaign == null)
            <tr>
                <td class="text-break" style="vertical-align: top;">
                    <span class="invoice-item-name" style="font-size:14px; display: inline-block;">
                        {{ $detail['quantity'] }}x
                        {{ json_decode($detail->food_details, true)['name'] }}
                    </span>

                    @if (count(json_decode($detail['variation'], true)) > 0)
                        <span class="invoice-variation-label" 
                              style="font-size:11px; font-weight:bold; margin-left: 10px; text-align:left; display: block;">
                            {{ translate('messages.variation') }}:
                      
                            @foreach (json_decode($detail['variation'], true) as $variation)
                                @if (isset($variation['name']) && isset($variation['values']))
                                    @foreach ($variation['values'] as $value)
                                        {{ $value['label'] }}&nbsp;
                                    @endforeach
                                @else
                                    @if (isset(json_decode($detail['variation'], true)[0]))
                                        @foreach (json_decode($detail['variation'], true)[0] as $key1 => $variation)
                                            {{ $variation }}&nbsp;
                                        @endforeach
                                    @endif
                                    @break
                                @endif
                            @endforeach
                        </span>
                    @else
                        <div style="margin-left: 15px;">
                            <span>{{ translate('messages.Price') }} : </span>
                            <strong>{{ \App\CentralLogics\Helpers::format_currency($detail->price) }}</strong>
                        </div>
                    @endif

                    @foreach (json_decode($detail['add_ons'], true) as $key2 => $addon)
                        @if ($key2 == 0)
                            <strong><u style="margin-left: 15px;">{{ translate('messages.addons') }} :</u></strong>
                        @endif
                        <div class="text-break" style="margin-left: 15px;">
                            <span>{{ $addon['name'] }} : </span>
                            <span class="font-weight-bold">
                                {{ $addon['quantity'] }} x
                                {{ \App\CentralLogics\Helpers::format_currency($addon['price']) }}
                            </span>
                        </div>
                        @php($add_ons_cost += $addon['price'] * $addon['quantity'])
                    @endforeach
                </td>

                <td class="text-right w-28p" style="vertical-align: top;">
                    @php($amount = $detail['price'] * $detail['quantity'])
                    <span class="invoice-amount" style="font-size:14px; display: inline-block;">
                        {{ \App\CentralLogics\Helpers::format_currency($amount) }}
                    </span>
                    <span class="invoice-euro-symbol" style="font-size:14px; display: inline-block;">€</span>
                </td>
            </tr>

            @php($sub_total += $amount)
            @php($total_tax += $detail['tax_amount'] * $detail['quantity'])
        @elseif($detail->campaign)
            <tr>
                <td style="vertical-align: top;">
                    {{ $detail['quantity'] }}
                </td>
                <td>
                    {{ $detail->campaign['title'] }} <br>

                    @if (count(json_decode($detail['variation'], true)) > 0)
                        <span class="invoice-variation-label" 
                              style="font-size:11px; font-weight:bold; margin-left: 10px; text-align:left; display: block;">
                            {{ translate('messages.variation') }}:
                       
                            @foreach (json_decode($detail['variation'], true) as $variation)
                                @if (isset($variation['name']) && isset($variation['values']))
                                    <strong>{{ $variation['name'] }} - </strong>
                                    @foreach ($variation['values'] as $value)
                                        {{ $value['label'] }}&nbsp;
                                    @endforeach
                                @else
                                    @if (isset(json_decode($detail['variation'], true)[0]))
                                        @foreach (json_decode($detail['variation'], true)[0] as $key1 => $variation)
                                            {{ $variation }}&nbsp;
                                        @endforeach
                                    @endif
                                    @break
                                @endif
                            @endforeach
                        </span>
                    @else
                        <div>
                            <span>{{ translate('messages.Price') }} : </span>
                            <strong>{{ \App\CentralLogics\Helpers::format_currency($detail->price) }}</strong>
                        </div>
                    @endif

                    @foreach (json_decode($detail['add_ons'], true) as $key2 => $addon)
                        @if ($key2 == 0)
                            <strong><u>{{ translate('messages.addons') }} :</u></strong>
                        @endif
                        <div>
                            <span>{{ $addon['name'] }} : </span>
                            <span class="font-weight-bold">
                                {{ $addon['quantity'] }} x
                                {{ \App\CentralLogics\Helpers::format_currency($addon['price']) }}
                            </span>
                        </div>
                        @php($add_ons_cost += $addon['price'] * $addon['quantity'])
                    @endforeach
                </td>
                <td class="w-28p" style="vertical-align: top;">
                    @php($amount = $detail['price'] * $detail['quantity'])
                    {{ \App\CentralLogics\Helpers::format_currency($amount) }}
                </td>
            </tr>

            @php($sub_total += $amount)
            @php($total_tax += $detail['tax_amount'] * $detail['quantity'])
        @endif
    @endforeach
</tbody>



                    <hr style="border: black dashed 1.3px; margin-top: .1rem; margin-bottom: .1rem;">



                </table>



                <hr style="border: black dashed 1.3px; margin-top: .1rem;
    margin-bottom: .1rem;">
                <div class="initial-38-9">
                    <div class="px-3">
                        <div>
                            <dl class="row text-right">
                                @if ($add_ons_cost > 0)
                                    <dt class="col-6 text-left text-muted">{{ translate('Addon_Cost') }}</dt>
                                    <dd class="col-6">
                                        {{ \App\CentralLogics\Helpers::format_currency($add_ons_cost) }}
                                    </dd>
                                @endif

                                <dt class="col-6 text-left fw-500">{{ translate('messages.subtotal') }}
                                    @if ($order->tax_status == 'included')
                                        ({{ translate('messages.TAX_Included') }})
                                    @endif
                                </dt>
                                <dd class="col-6 fw-500">
                                    {{ \App\CentralLogics\Helpers::format_currency($sub_total + $add_ons_cost) }}
                                </dd>

                                @if ($order['restaurant_discount_amount'] > 0)
                                    <dt class="col-6 text-left text-muted">{{ translate('messages.discount') }}</dt>
                                    <dd class="col-6">
                                        -
                                        {{ \App\CentralLogics\Helpers::format_currency($order['restaurant_discount_amount']) }}
                                    </dd>
                                @endif

                                @if ($order['coupon_discount_amount'] > 0)
                                    <dt class="col-6 text-left text-muted">{{ translate('messages.coupon_discount') }}
                                    </dt>
                                    <dd class="col-6">
                                        -
                                        {{ \App\CentralLogics\Helpers::format_currency($order['coupon_discount_amount']) }}
                                    </dd>
                                @endif

                                @if ($order['ref_bonus_amount'] > 0)
                                    <dt class="col-6 text-left text-muted">
                                        {{ translate('messages.Referral_Discount') }}:</dt>
                                    <dd class="col-6">
                                        - {{ \App\CentralLogics\Helpers::format_currency($order['ref_bonus_amount']) }}
                                    </dd>
                                @endif

                                @if ($order['total_tax_amount'] > 0)
                                    @if ($order->tax_status == 'excluded' || $order->tax_status == null)
                                        <dt class="col-6 text-left text-muted">{{ translate('messages.vat/tax') }}</dt>
                                        <dd class="col-6">
                                            {{ \App\CentralLogics\Helpers::format_currency($order['total_tax_amount']) }}
                                        </dd>
                                    @endif
                                @endif

                                @if ($order['dm_tips'] > 0)
                                    <dt class="col-6 text-left text-muted">
                                        {{ translate('messages.delivery_man_tips') }}</dt>
                                    <dd class="col-6">
                                        {{ \App\CentralLogics\Helpers::format_currency($order['dm_tips']) }}
                                    </dd>
                                @endif

                                @if ($order['delivery_charge'] > 0)
                                    <dt class="col-6 text-left text-muted">{{ translate('messages.delivery_charge') }}
                                    </dt>
                                    <dd class="col-6">
                                        {{ \App\CentralLogics\Helpers::format_currency($order['delivery_charge']) }}
                                        @if (\App\CentralLogics\Helpers::get_business_data('additional_charge_status') == 1 || $order['additional_charge'] > 0)
                                            @php($additional_charge_status = 1)
                                        @else
                                            @php($additional_charge_status = 0)
                                            <hr>
                                        @endif
                                    </dd>
                                @endif

                                @if ($order['additional_charge'] > 0)
                                    <dt class="col-6 text-left text-muted">
                                        {{ \App\CentralLogics\Helpers::get_business_data('additional_charge_name') ?? translate('messages.additional_charge') }}:
                                    </dt>
                                    <dd class="col-6">
                                        +
                                        {{ \App\CentralLogics\Helpers::format_currency($order['additional_charge']) }}
                                    </dd>
                                @endif

                                @if ($order['extra_packaging_amount'] > 0)
                                    <dt class="col-6 text-left text-muted">
                                        {{ translate('messages.Extra_Packaging_Amount') }}:</dt>
                                    <dd class="col-6">
                                        +
                                        {{ \App\CentralLogics\Helpers::format_currency($order['extra_packaging_amount']) }}
                                    </dd>
                                @endif

                                <dt class="col-6 text-left fw-500 invoice-total-label">
                                    {{ translate('messages.total') }}</dt>
                                <dd class="col-6 fw-500 invoice-total-amount">
                                    {{ \App\CentralLogics\Helpers::format_currency($order['order_amount']) }}
                                </dd>

                                @if ($order?->payments)
                                    @foreach ($order?->payments as $payment)
                                        @if ($payment->payment_status == 'paid')
                                            @if ($payment->payment_method == 'cash_on_delivery')
                                                <dt class="col-6 text-left">Paid Cash at Counter </dt>
                                            @else
                                                <dt class="col-6 text-left">Paid with Card</dt>
                                            @endif
                                        @else
                                            <dt class="col-6 text-left">
                                                {{ translate('Due_Amount') }}
                                                ({{ $payment->payment_method == 'cash_on_delivery' ? translate('messages.COD') : translate($payment->payment_method) }})
                                                :
                                            </dt>
                                        @endif
                                        <dd class="col-6 ">
                                            {{ \App\CentralLogics\Helpers::format_currency($payment->amount) }}
                                        </dd>
                                    @endforeach
                                @endif
                            </dl>
                        </div>
                    </div>
                </div>

                <div class="d-flex flex-row justify-content-start">
                    <span class="text-capitalize d-flex"><span>
                            @if (translate(str_replace('_', ' ', $order['payment_method'])) == 'Cash on delivery')
                                Paid Cash at Counter
                            @else
                                Paid with Card
                            @endif
                        </span> </span>
                    @if ($order->adjusment > $order->order_amount)
                        <span>{{ translate('messages.amount') }}: {{ $order->adjusment }}</span>
                        <span>{{ translate('messages.change') }}:
                            {{ $order->adjusment - $order->order_amount }}</span>
                    @endif

                </div>

                <hr style="border: black dashed 1.3px; margin-top: .1rem;
    margin-bottom: .1rem;">
                @if ($order['order_note'] == null)
                    <span><strong> Notes:</strong> {{ $order['order_note'] }}</span>
                    <hr style="border: black dashed 1.3px; margin-top: .1rem;">
                @endif

                <div class="p-3">


                    @if ($order?->delivery_address)
                        <h5 class="d-flex justify-content-between gap-2">
                            <span class="text-muted">{{ translate('Name') }}</span>
                            <span>
                                {{ isset($order->delivery_address) ? json_decode($order->delivery_address, true)['contact_person_name'] : '' }}
                            </span>
                        </h5>
                        <h5 class="d-flex justify-content-between gap-2">
                            <span class="text-muted">{{ translate('messages.phone') }}</span>
                            <span>
                                {{ isset($order->delivery_address) ? json_decode($order->delivery_address, true)['contact_person_number'] : '' }}
                            </span>
                        </h5>

                        @if (!in_array($order->order_type, ['dine_in', 'take_away']))
                            <h5 class="d-flex justify-content-between gap-2 text-break">
                                <span
                                    class="text-muted text-nowrap">{{ translate('messages.delivery_Address') }}</span>
                                <span class="text-right">
                                    {{ isset($order->delivery_address) ? json_decode($order->delivery_address, true)['address'] : '' }}
                                </span>
                            </h5>
                            <div class="d-flex gap-2 align-items-center justify-content-end" style="font-size: 10px">
                                @if (isset($order->delivery_address) && isset(json_decode($order->delivery_address, true)['road']))
                                    <div class="d-flex gap-1">
                                        <span class="text-muted">{{ translate('messages.street_No') }}</span>:
                                        <span>
                                            {{ json_decode($order->delivery_address, true)['road'] }}
                                        </span>
                                    </div>
                                @endif


                                @if (isset($order->delivery_address) && isset(json_decode($order->delivery_address, true)['house']))
                                    <div class="d-flex gap-1">
                                        <span class="text-muted">{{ translate('messages.House') }}</span>:
                                        <span class="font-light">
                                            {{ json_decode($order->delivery_address, true)['house'] }}
                                        </span>
                                    </div>
                                @endif

                                @if (isset($order->delivery_address) && isset(json_decode($order->delivery_address, true)['floor']))
                                    <div class="d-flex gap-1">
                                        <span class="text-muted">{{ translate('messages.floor') }}</span>:
                                        <span class="font-light">
                                            {{ json_decode($order->delivery_address, true)['floor'] }}
                                        </span>
                                    </div>
                                @endif
                            </div>
                        @endif
                    @else
                        <h5 class="d-flex justify-content-between gap-2">
                            {{ translate('Customer_Name') }} :
                            <span class="font-light">
                                {{ translate('messages.walk_in_customer') }}
                            </span>
                        </h5>
                    @endif
                </div>
                <h5 class="text-center pt-1  justify-content-center mb-0">
                    <span class="d-block fw-500">{{ translate('messages.THANK_YOU') }}</span>
                </h5>
                <div class="text-center">Powered By G Tech Nexa Limited</div>

            </div>


            <div class="text-center non-printable mt-3">
                @if ($order->order_status === 'pending')
                    <a href="{{ route('vendor.order.status', ['id' => $order->id, 'order_status' => 'confirmed']) }}"
                        class="btn btn-sm btn-success print-Div btn-green-custom">
                        {{ translate('Confirm & Print') }}
                    </a>



                    <a href="#" class="btn btn-sm btn-danger cancelled-status btn-cancel-custom" data-toggle="modal"
                        data-target="#cancelModal"
                        data-url="{{ route('vendor.order.status', ['id' => $order->id, 'order_status' => 'canceled']) }}">
                        {{ translate('Cancel') }}
                    </a>
                @elseif($order->order_status === 'confirmed')
                    {{-- Accepted Orders: Print + Cancel --}}
                    <button type="button" class="btn btn-sm btn-primary print-Div btn-print-custom" style="color: white !important;">Print</button>
                    @if (Carbon::now()->lte(Carbon::parse($order->confirmed)->addMinutes(30)))
                        <a href="#" class="btn btn-sm btn-danger cancelled-status btn-cancel-custom" data-toggle="modal"
                            data-target="#cancelModal"
                            data-url="{{ route('vendor.order.status', ['id' => $order->id, 'order_status' => 'canceled']) }}">
                            {{ translate('Cancel') }}
                        </a>
                    @endif
                @elseif($order->order_status === 'canceled')
                    <button type="button" class="btn btn-sm btn-primary print-Div btn-print-custom">Print</button>
                    @if (in_array($order->payment_method, ['card', 'digital_payment']))
                        <a href="javascript:" class="btn btn-sm btn-warning order-status-change-alert"
                            data-url="{{ route('vendor.order.status', ['id' => $order->id, 'order_status' => 'refunded']) }}"
                            data-message="{{ translate('Change status to refunded ?') }}">
                            Refund
                        </a>
                    @endif
                @else
                    {{-- Fallback: just Print --}}
                    <button type="button" class="btn btn-sm btn-primary print-Div btn-print-custom" style="color: white !important;>Print</button>
                @endif
            </div>
        </div>
    </div>
</div>
