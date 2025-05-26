@extends('layouts.vendor.app')

@section('title', translate('messages.Order List'))

<!--@push('css_or_js')-->
    <!--    <meta name="csrf-token" content="{{ csrf_token() }}">-->
<!--@endpush-->


@push('css_or_js')
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Update the CSS in your @push('css_or_js') section -->
        <style>
            #invoiceContent .content {
                padding-top: 0;
            }

            .sidebar-overlay {
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background-color: rgba(0, 0, 0, 0.5);
                z-index: 1040;
                display: none;
            }

            .invoice-sidebar {
                position: fixed;
                top: 0;
                right: 0;
                width: 25%;
                height: 100%;
                background-color: white;
                z-index: 1050;
                padding: 20px;
                overflow-y: auto;
                display: none;
                box-shadow: -5px 0 15px rgba(0, 0, 0, 0.1);
            }

            .close-sidebar {
                position: absolute;
                top: 15px;
                right: 15px;
                font-size: 24px;
                cursor: pointer;
                z-index: 2;
                background: #f8f9fa;
                width: 30px;
                height: 30px;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                border: 1px solid #ddd;
            }

            .close-sidebar:hover {
                background: #e9ecef;
            }

            /* Loader Overlay */
            .dotted-loader-overlay {
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(255, 255, 255, 0.85);
                z-index: 1060;
                display: none;
                justify-content: center;
                align-items: center;
            }

            /* Dotted Loader */
            .dotted-loader {
                display: flex;
                gap: 0.5rem;
            }

            .dotted-loader span {
                width: 14px;
                height: 14px;
                background-color: #377dff;
                border-radius: 50%;
                animation: bounce 1.2s infinite ease-in-out;
            }

            .dotted-loader span:nth-child(1) {
                animation-delay: 0s;
            }

            .dotted-loader span:nth-child(2) {
                animation-delay: 0.2s;
            }

            .dotted-loader span:nth-child(3) {
                animation-delay: 0.4s;
            }

            .dotted-loader span:nth-child(4) {
                animation-delay: 0.6s;
            }

            @keyframes bounce {

                0%,
                80%,
                100% {
                    transform: scale(0.8);
                    opacity: 0.5;
                }

                40% {
                    transform: scale(1.2);
                    opacity: 1;
                }
            }
        </style>


    @endpush



    @section('content')
        <?php
        ?>

        <!--invoice -->
        <div class="sidebar-overlay" id="invoiceOverlay"></div>
        <div class="invoice-sidebar" id="invoiceSidebar">
            <span class="close-sidebar" onclick="closeInvoiceSidebar()">
                <i class="tio-clear"></i>
            </span>
            <div id="invoiceContent"></div>
        </div>
        <!-- Add loading overlay -->
        <!-- Loader Container -->
        <div class="dotted-loader-overlay" id="loadingOverlay">
            <div class="dotted-loader">
                <span></span>
                <span></span>
                <span></span>
                <span></span>
            </div>
        </div>

        </div>



        <div class="content container-fluid">
            <!-- Page Header -->
            <div class="page-header pt-0 pb-2">
                <div class="d-flex flex-wrap justify-content-between">
                    <h2 class="page-header-title align-items-center text-capitalize py-2 mr-2">
                        <div class="card-header-icon d-inline-flex mr-2 img">
                            @if (str_replace('_', ' ', $status) == 'All')
                                <img class="mw-24px"
                                    src="{{ dynamicAsset('/public/assets/admin/img/resturant-panel/page-title/order.png') }}"
                                    alt="public">
                            @elseif(str_replace('_', ' ', $status) == 'Pending')
                                <img class="mw-24px"
                                    src="{{ dynamicAsset('/public/assets/admin/img/resturant-panel/page-title/pending.png') }}"
                                    alt="public">
                            @elseif(str_replace('_', ' ', $status) == 'Confirmed')
                                <img class="mw-24px"
                                    src="{{ dynamicAsset('/public/assets/admin/img/resturant-panel/page-title/confirm.png') }}"
                                    alt="public">
                            @elseif(str_replace('_', ' ', $status) == 'Cooking')
                                <img class="mw-24px"
                                    src="{{ dynamicAsset('/public/assets/admin/img/resturant-panel/page-title/cooking.png') }}"
                                    alt="public">
                            @elseif(str_replace('_', ' ', $status) == 'Ready for delivery')
                                <img class="mw-24px"
                                    src="{{ dynamicAsset('/public/assets/admin/img/resturant-panel/page-title/ready.png') }}"
                                    alt="public">
                            @elseif(str_replace('_', ' ', $status) == 'Food on the way')
                                <img class="mw-24px"
                                    src="{{ dynamicAsset('/public/assets/admin/img/resturant-panel/page-title/ready.png') }}"
                                    alt="public">
                            @elseif(str_replace('_', ' ', $status) == 'Delivered')
                                <img class="mw-24px"
                                    src="{{ dynamicAsset('/public/assets/admin/img/resturant-panel/page-title/ready.png') }}"
                                    alt="public">
                            @elseif(str_replace('_', ' ', $status) == 'Refunded')
                                <img class="mw-24px"
                                    src="{{ dynamicAsset('/public/assets/admin/img/resturant-panel/page-title/order.png') }}"
                                    alt="public">
                            @elseif(str_replace('_', ' ', $status) == 'Scheduled')
                                <img class="mw-24px"
                                    src="{{ dynamicAsset('/public/assets/admin/img/resturant-panel/page-title/order.png') }}"
                                    alt="public">
                            @endif
                        </div>
                        <span>
                            {{ str_replace('_', ' ', $status) }} {{ translate('messages.orders') }} <span
                                class="badge badge-soft-dark ml-2">{{ $orders->total() }}</span>
                        </span>
                    </h2>
                </div>
            </div>
            <!-- End Page Header -->


            <!-- End Page Header -->

            <!-- Card -->
            <div class="card">
                <!-- Header -->
                <div class="card-header py-2">
                    <div class="search--button-wrapper justify-content-end max-sm-flex-100">
                        <form>
                            <!-- Search -->
                            <div class="input-group input--group">
                                <input id="datatableSearch_" type="search" name="search" class="form-control"
                                    value="{{ request()?->search ?? null }}"
                                    placeholder="{{ translate('Ex : Search by Order Id') }}"
                                    aria-label="{{ translate('messages.search') }}">
                                <button type="submit" class="btn btn--secondary">
                                    <i class="tio-search"></i>
                                </button>
                            </div>
                            <!-- End Search -->
                        </form>




                        <div class="d-sm-flex justify-content-sm-end align-items-sm-center m-0">
                            <!-- Date Filter -->

                            @if (str_replace('_', ' ', $status) != 'pending')
                  {{-- always show this form, regardless of status --}}
<div class="mr-2">
  <form id="dateFilterForm"
        class="form-inline"
        method="GET"
        action="{{ route('vendor.order.list', ['status' => $st]) }}">
    <div class="form-group position-relative d-flex align-items-center">
      <input
        type="date"
        name="filter_date"
        id="filter_date"
        class="form-control pr-5"
        value="{{ $filterDate ?? \Carbon\Carbon::now('Europe/Dublin')->format('Y-m-d') }}"
        onchange="this.form.submit()"
      />

      <span id="todayLabel"
            style="position:absolute; left:12px; color:gray; pointer-events:none;
                   {{ ($filterDate ?? now('Europe/Dublin')->format('Y-m-d')) === now('Europe/Dublin')->format('Y-m-d')
                       ? 'display:inline' : 'display:none' }}">
        Today
      </span>
    </div>
  </form>
</div>

                            @endif
                            <script>
                                document.addEventListener('DOMContentLoaded', function() {
                                    const dateInput = document.getElementById('filter_date');
                                    const todayLabel = document.getElementById('todayLabel');

                                    const today = new Date().toLocaleDateString('en-CA', {
                                        timeZone: 'Europe/Dublin'
                                    });

                                    function updateLabel() {
                                        if (dateInput.value === today) {
                                            todayLabel.style.display = 'inline';
                                            dateInput.style.color = 'transparent';
                                            dateInput.style.caretColor = 'black';
                                        } else {
                                            todayLabel.style.display = 'none';
                                            dateInput.style.color = '';
                                        }
                                    }

                                    updateLabel();

                                    dateInput.addEventListener('input', function(e) {
                                        updateLabel();
                                        // Optional: prevent auto-submitting behavior
                                        e.preventDefault();
                                    });

                                    // Also make sure the form does not auto-submit on change
                                    const form = document.getElementById('dateFilterForm');
                                    form.addEventListener('submit', function(e) {
                                        e.preventDefault(); // only if you want to stop submission
                                    });
                                });




                                $(document).ready(function() {
                                    // Auto-submit form when date changes
                                    $('#filter_date').change(function() {
                                        let url = new URL(window.location.href);
                                        let selectedDate = $(this).val();

                                        if (selectedDate) {
                                            url.searchParams.set('filter_date', selectedDate);
                                        } else {
                                            url.searchParams.delete('filter_date');
                                        }

                                        // Remove page parameter to go back to first page
                                        url.searchParams.delete('page');

                                        window.location.href = url.toString();
                                    });

                                    // Initialize with current date from URL or today's date
                                    const urlParams = new URLSearchParams(window.location.search);
                                    const today = new Date().toISOString().split('T')[0];

                                    if (urlParams.has('filter_date')) {
                                        $('#filter_date').val(urlParams.get('filter_date'));
                                    } else {
                                        $('#filter_date').val(today);
                                    }
                                });
                            </script>





                        </div>

                        <!-- Keep Export Button -->
                        <!--<div class="hs-unfold">-->
                        <!--    <a class="js-hs-unfold-invoker btn btn-sm btn-white" href="{{ route('vendor.order.export', ['status' => $st, 'type' => 'excel', request()->getQueryString()]) }}">-->
                        <!--        <i class="tio-download-to mr-1"></i> {{ translate('messages.export') }}-->
                        <!--    </a>-->
                        <!--</div>-->
                    </div>















                    <!--<div class="d-sm-flex justify-content-sm-end align-items-sm-center m-0">-->

                    <!-- Unfold -->
                    <!--<div class="hs-unfold mr-2">-->
                    <!--    <a class="js-hs-unfold-invoker btn btn-sm btn-white dropdown-toggle" href="javascript:;"-->
                    <!--        data-hs-unfold-options='{-->
                        <!--            "target": "#usersExportDropdown",-->
                        <!--            "type": "css-animation"-->
                        <!--        }'>-->
                    <!--        <i class="tio-download-to mr-1"></i> {{ translate('messages.export') }}-->
                    <!--    </a>-->

                    <!--    <div id="usersExportDropdown"-->
                    <!--            class="hs-unfold-content dropdown-unfold dropdown-menu dropdown-menu-sm-right">-->

                    <!--        <span-->
                    <!--            class="dropdown-header">{{ translate('messages.download_options') }}</span>-->
                    <!--        <a id="export-excel" class="dropdown-item" href="{{ route('vendor.order.export', ['status' => $st, 'type' => 'excel', request()->getQueryString()]) }}">-->
                    <!--            <img class="avatar avatar-xss avatar-4by3 mr-2"-->
                    <!--                    src="{{ dynamicAsset('public/assets/admin') }}/svg/components/excel.svg"-->
                    <!--                    alt="Image Description">-->
                    <!--            {{ translate('messages.excel') }}-->
                    <!--        </a>-->
                    <!--        <a id="export-csv" class="dropdown-item" href="{{ route('vendor.order.export', ['status' => $st, 'type' => 'csv', request()->getQueryString()]) }}">-->
                    <!--            <img class="avatar avatar-xss avatar-4by3 mr-2"-->
                    <!--                    src="{{ dynamicAsset('public/assets/admin') }}/svg/components/placeholder-csv-format.svg"-->
                    <!--                    alt="Image Description">-->
                    <!--            {{ translate('messages.csv') }}-->
                    <!--        </a>-->

                    <!--    </div>-->
                    <!--</div>-->
                    <!--</div>-->
                </div>
            </div>
            <!-- End Header -->



            <!-- Table -->
            <div class="table-responsive datatable-custom">
                <table id="datatable"
                    class="table table-hover table-borderless table-thead-bordered table-nowrap table-align-middle card-table"
                    data-hs-datatables-options='{
                                 "order": [],
                                 "orderCellsTop": true,
                                 "paging":false
                               }'>
                    <thead class="thead-light">
                        <tr>
                            <th class="w-60px">
                                {{ translate('messages.sl') }}
                            </th>
                            <th class="w-90px table-column-pl-0">{{ translate('messages.Order_ID') }}</th>
                            <th class="w-140px">{{ translate('messages.order_date') }}</th>
                            <th class="w-140px">{{ translate('messages.scheduled_at') }}</th>

                            <th class="w-140px">{{ translate('messages.customer_information') }}</th>
                            <th class="w-100px">{{ translate('messages.total_amount') }}</th>
                            <th class="w-100px text-center">{{ translate('messages.order_status') }}</th>
                            <th class="w-100px text-center">{{ translate('messages.actions') }}</th>
                        </tr>
                    </thead>

                    <tbody id="set-rows">
                        @foreach ($orders as $key => $order)
                            <tr class="status-{{ $order['order_status'] }} class-all">
                                <td class="">
                                    {{ $key + $orders->firstItem() }}
                                </td>
                                <td class="table-column-pl-0">
                                    <a class="text-hover  print-invoice-btn" href="#"
                                        data-order-id="{{ $order['id'] }}">{{ $order['id'] }}</a>
                                </td>
                                <td>
                                    <span class="d-block">
                                        {{ Carbon\Carbon::parse($order['created_at'])->locale(app()->getLocale())->translatedFormat('d M Y') }}
                                    </span>
                                    <span class="d-block text-uppercase">
                                        {{ Carbon\Carbon::parse($order['created_at'])->locale(app()->getLocale())->translatedFormat(config('timeformat')) }}
                                    </span>
                                </td>
                                @php
                                    $iso = \Carbon\Carbon::parse($order->schedule_at)->format('Y-m-d\TH:i');
                                @endphp

                         <td>
    @php
        $iso = \Carbon\Carbon::parse($order->schedule_at)->format('Y-m-d\TH:i');
    @endphp

    @if ($order->order_status === 'pending')
        <a href="javascript:void(0)" class="open-reschedule text-decoration-underline"
           data-route="{{ route('vendor.order.reschedule', $order->id) }}"
           data-iso="{{ $iso }}">
           {{ \Carbon\Carbon::parse($order->schedule_at)->format('d M Y ' . config('timeformat')) }}
        </a>

    @elseif($order->order_status === 'confirmed' && \Carbon\Carbon::now()->lte(\Carbon\Carbon::parse($order->confirmed)->addMinutes(30)))
        <a href="javascript:void(0)" class="open-reschedule text-decoration-underline"
           data-route="{{ route('vendor.order.reschedule', $order->id) }}"
           data-iso="{{ $iso }}">
           {{ \Carbon\Carbon::parse($order->schedule_at)->format('d M Y ' . config('timeformat')) }}
        </a>

    @else
        {{ \Carbon\Carbon::parse($order->schedule_at)->format('d M Y ' . config('timeformat')) }}
    @endif
</td>


                                <td>
                                    @if ($order->is_guest)
                                        <?php
                                        $customer_details = json_decode($order['delivery_address'], true);
                                        ?>
                                        <strong>{{ $customer_details['contact_person_name'] }}</strong>
                                        <div>{{ $customer_details['contact_person_number'] }}</div>
                                    @elseif($order->customer)
                                        
                                            <span class="d-block font-semibold">
                                                {{ $order->customer['f_name'] . ' ' . $order->customer['l_name'] }}
                                            </span>
                                            <span class="d-block">
                                                {{ $order->customer['phone'] }}
                                            </span>
                                    @else
                                        <label
                                            class="badge badge-danger">{{ translate('messages.invalid_customer_data') }}</label>
                                    @endif
                                </td>
                                <td>


                                    <div class="text-right mw-85px">
                                        <div>
                                            {{ \App\CentralLogics\Helpers::format_currency($order['order_amount']) }}
                                        </div>
                                        @if ($order->payment_status == 'paid')
                                            <strong class="text-success">
                                                {{ translate('messages.paid') }}
                                            </strong>
                                        @elseif($order->payment_status == 'partially_paid')
                                            <strong class="text-success">
                                                {{ translate('messages.partially_paid') }}
                                            </strong>
                                        @else
                                            <strong class="text-danger">
                                                {{ translate('messages.unpaid') }}
                                            </strong>
                                        @endif
                                    </div>

                                </td>
                                <td class="text-capitalize text-center">
                                    @if (isset($order->subscription) && $order->subscription->status != 'canceled')
                                        @php
                                            $order->order_status = $order->subscription_log
                                                ? $order->subscription_log->order_status
                                                : $order->order_status;
                                        @endphp
                                    @endif
                                    @if ($order['order_status'] == 'pending')
                                        <span class="badge badge-soft-info mb-1">
                                            {{ translate('messages.pending') }}
                                        </span>
                                    @elseif($order['order_status'] == 'confirmed')
                                        <span class="badge badge-soft-info mb-1">
                                            {{ translate('messages.confirmed') }}
                                        </span>
                                    @elseif($order['order_status'] == 'processing')
                                        <span class="badge badge-soft-warning mb-1">
                                            {{ translate('messages.processing') }}
                                        </span>
                                    @elseif($order['order_status'] == 'picked_up')
                                        <span class="badge badge-soft-warning mb-1">
                                            {{ translate('messages.out_for_delivery') }}
                                        </span>
                                    @elseif($order['order_status'] == 'delivered')
                                        <span class="badge badge-soft-success mb-1">
                                            {{ $order?->order_type == 'dine_in' ? translate('messages.Completed') : translate('messages.delivered') }}
                                        </span>
                                    @else
                                        <span class="badge badge-soft-danger mb-1">
                                            {{ translate(str_replace('_', ' ', $order['order_status'])) }}
                                        </span>
                                    @endif


                                    <div class="text-capitalze opacity-7">
                                        @if ($order['order_type'] == 'take_away')
                                            <span>
                                                {{ translate('messages.take_away') }}
                                            </span>
                                        @elseif ($order['order_type'] == 'dine_in')
                                            <span>
                                                {{ translate('Dine_in') }}
                                            </span>
                                        @else
                                            <span>
                                                {{ translate('messages.delivery') }}
                                            </span>
                                        @endif
                                    </div>
                                </td>
                                <!--<td>-->
                                <!--    <div class="btn--container justify-content-center">-->
                                <!--        <a class="btn action-btn btn--warning btn-outline-warning" href="{{ route('vendor.order.details', ['id' => $order['id']]) }}"><i class="tio-visible-outlined"></i></a>-->
                                <!--        <a class="btn action-btn btn--primary btn-outline-primary" target="_blank" href="{{ route('vendor.order.generate-invoice', [$order['id']]) }}"><i class="tio-print"></i></a>-->
                                <!--    </div>-->


                                <td>
                                    <div class="btn--container justify-content-center">
                                        {{-- <a class="btn action-btn btn--warning btn-outline-warning"
                                            href="{{ route('vendor.order.details', ['id' => $order['id']]) }}">
                                            <i class="tio-visible-outlined"></i>
                                        </a> --}}
                                        <a class="btn action-btn btn--primary btn-outline-primary print-invoice-btn"
                                            href="#" data-order-id="{{ $order['id'] }}">
                                            <i class="tio-print"></i>
                                        </a>
                                    </div>
                                </td>


                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if (count($orders) === 0)
                <div class="empty--data">
                    <img src="{{ dynamicAsset('/public/assets/admin/img/empty.png') }}" alt="public">
                    <h5>
                        {{ translate('no_data_found') }}
                    </h5>
                </div>
            @endif
            <!-- End Table -->

    <div class="card-footer">
                <!-- Pagination -->
                <div class="row justify-content-center justify-content-sm-between align-items-sm-center">
                    <div class="col-sm-auto">
                        <div class="d-flex justify-content-center justify-content-sm-end">
                            <!-- Pagination -->
                            {!! $orders->links() !!}
                        </div>
                    </div>
                </div>
                <!-- End Pagination -->
            </div>
            <!-- End Footer -->
        </div>
        <!-- End Card -->
        </div>






        </div>


        {{-- CANCEL CONFIRMATION MODAL --}}
        <div class="modal fade" id="cancelModal" tabindex="-1" role="dialog" aria-labelledby="cancelModalLabel"
            aria-hidden="true" style="z-index:100000">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="cancelModalLabel">
                            {{ translate('messages.are_you_sure_?') }}
                        </h5>
                        <button type="button" class="close" data-dismiss="modal"
                            aria-label="{{ translate('messages.close') }}">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p>{{ translate('messages.You_want_to_cancel_this_order_?') }}</p>
                        <div class="form-group">
                            <label for="cancelReason">
                                {{ translate('select_cancellation_reason') }}
                            </label>
                            <select id="cancelReason" class="form-control">
                                <option value="">{{ translate('select_cancellation_reason') }}</option>
                                @foreach ($reasons as $r)
                                    <option value="{{ $r->reason }}">{{ $r->reason }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">
                            {{ translate('messages.no') }}
                        </button>
                        <button type="button" class="btn btn-danger" id="confirmCancelBtn">
                            {{ translate('messages.yes') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
     <div class="modal fade" id="rescheduleModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-sm">
    <div class="modal-content">
      <form id="rescheduleForm" method="POST">
        @csrf
        @method('PUT')
        <div class="modal-header">
          <h5 class="modal-title">{{ translate('messages.update_schedule') }}</h5>
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>
        <div class="modal-body">
          <div class="form-group">
            <label for="newScheduleTime">{{ translate('messages.new_time') }}</label>
            <select id="newScheduleTime" name="schedule_at" class="form-control" required>
              <!-- Options will be dynamically added by JS -->
            </select>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">
            {{ translate('messages.cancel') }}
          </button>
          <button type="submit" class="btn btn-primary">
            {{ translate('messages.update') }}
          </button>
        </div>
      </form>
    </div>
  </div>
</div>


    @endsection

    @push('script_2')

<script>
    $(function() {
  function pad(num) {
    return num.toString().padStart(2, '0');
  }

  function formatTime24To12(h24, m) {
    const ampm = h24 >= 12 ? 'PM' : 'AM';
    let h12 = h24 % 12;
    if (h12 === 0) h12 = 12;
    return h12 + ':' + pad(m) + ' ' + ampm;
  }

  function roundUpToNextFiveMinutes(date) {
    const ms = 1000 * 60 * 5;
    return new Date(Math.ceil(date.getTime() / ms) * ms);
  }

  function generateTimeOptions(startTime) {
    const options = [];
    const endTime = new Date(startTime);
    endTime.setHours(22, 0, 0, 0); // 10:00 PM exactly

    let current = new Date(startTime);

    while (current <= endTime) {
      const h = current.getHours();
      const m = current.getMinutes();
      const value = pad(h) + ':' + pad(m);
      const display = formatTime24To12(h, m);
      options.push({ value, display });
      current = new Date(current.getTime() + 5 * 60 * 1000); // add 5 minutes
    }
    return options;
  }

  $(document).on('click', '.open-reschedule', function() {
    const btn = $(this);
    const route = btn.data('route');
    const iso = btn.data('iso'); // example: 2025-05-27T20:23

    $('#rescheduleForm').attr('action', route);

    // Extract time part and round up to next 5 minutes
    if (iso) {
      const timePart = iso.split('T')[1]; // e.g. "20:23"
      const [hour, minute] = timePart.split(':').map(Number);

      // Create a Date object today with that time
      const now = new Date();
      const selectedTime = new Date(now.getFullYear(), now.getMonth(), now.getDate(), hour, minute);

      const roundedTime = roundUpToNextFiveMinutes(selectedTime);

      // Generate options from rounded time to 10 PM
      const options = generateTimeOptions(roundedTime);

      const $select = $('#newScheduleTime');
      $select.empty();
      options.forEach(opt => {
        $select.append(`<option value="${opt.value}">${opt.display}</option>`);
      });

      // Select first option (which is roundedTime)
      if (options.length > 0) {
        $select.val(options[0].value);
      }
    }

    // Show modal
    $('#rescheduleModal').modal('show');
  });

  // On submit, convert selected time back to full ISO datetime with today's date
  $('#rescheduleForm').on('submit', function(e) {
    e.preventDefault();

    const selectedTime = $('#newScheduleTime').val(); // "HH:mm"

    if (!selectedTime) {
      alert('Please select a time');
      return;
    }

    // Build ISO datetime string with today's date + selected time + seconds "00"
    const today = new Date();
    const year = today.getFullYear();
    const month = pad(today.getMonth() + 1);
    const day = pad(today.getDate());
    const isoDatetime = `${year}-${month}-${day}T${selectedTime}:00`;

    // Create or update hidden input 'schedule_at' with value isoDatetime
    if ($('#schedule_at').length === 0) {
      $('<input>').attr({
        type: 'hidden',
        id: 'schedule_at',
        name: 'schedule_at',
        value: isoDatetime
      }).appendTo('#rescheduleForm');
    } else {
      $('#schedule_at').val(isoDatetime);
    }

    // Submit the form
    this.submit();
  });
});

</script>
        <script>
            $(function() {
                $(document).on('click', '.open-reschedule', function() {
                    let btn = $(this),
                        route = btn.data('route'),
                        iso = btn.data('iso');

                    $('#rescheduleForm')
                        .attr('action', route)
                        .find('#newSchedule')
                        .val(iso);

                    $('#rescheduleModal').modal('show');
                });
            });
        </script>


        <script>
            $(function() {
                let cancelUrl = '';

                // When the modal opens, grab the cancel-URL
                $('#cancelModal').on('show.bs.modal', function(e) {
                    cancelUrl = $(e.relatedTarget).data('url');
                    $('#cancelReason').val(''); // reset the dropdown
                });

                // When the user confirms cancellation:
                $('#confirmCancelBtn').on('click', function() {
                    const reason = $('#cancelReason').val() || 'No reason provided';

                    // Build a proper URL object
                    const url = new URL(cancelUrl, window.location.origin);
                    url.searchParams.set('reason', reason);

                    // Redirect
                    window.location.href = url.toString();
                });
            });
        </script>

        <script>
            // code of invoice nd date chage 
            // code by mrsaqib ale ( saqib ali )

            // First remove any existing handlers to prevent duplicates
            $(document).off('click', '.print-invoice-btn');

            // Invoice Popup Functions
            function openInvoiceSidebar() {
                const overlay = document.getElementById('invoiceOverlay');
                const sidebar = document.getElementById('invoiceSidebar');

                if (!overlay || !sidebar) {
                    console.error('Invoice elements not found!');
                    return;
                }

                overlay.style.display = 'block';
                sidebar.style.display = 'block';
                document.body.style.overflow = 'hidden';
            }

            function closeInvoiceSidebar() {
                const overlay = document.getElementById('invoiceOverlay');
                const sidebar = document.getElementById('invoiceSidebar');

                if (overlay) overlay.style.display = 'none';
                if (sidebar) sidebar.style.display = 'none';
                document.body.style.overflow = 'auto';
                $('#invoiceContent').empty();
            }

            function showLoading() {
                const loader = document.getElementById('loadingOverlay');
                if (loader) loader.style.display = 'flex';
            }

            function hideLoading() {
                const loader = document.getElementById('loadingOverlay');
                if (loader) loader.style.display = 'none';
            }

            // Handle print button clicks
            $(document).on('click', '.print-invoice-btn', function(e) {
                e.preventDefault();
                e.stopImmediatePropagation();

                const orderId = $(this).data('order-id');
                const url = "{{ route('vendor.order.generate-invoice', ['id' => ':id']) }}".replace(':id', orderId);

                console.log('Loading invoice for order:', orderId);

                showLoading();

                // Use a fresh AJAX call
                $.ajax({
                    url: url,
                    type: 'GET',
                    dataType: 'html',
                    success: function(data) {
                        hideLoading();
                        try {
                            $('#invoiceContent').empty().html(data);
                            openInvoiceSidebar();
                        } catch (error) {
                            console.error('Error processing invoice:', error);
                            $('#invoiceContent').html(
                                '<div class="alert alert-danger">Error displaying invoice</div>');
                            openInvoiceSidebar();
                        }
                    },
                    error: function(xhr, status, error) {
                        hideLoading();
                        console.error('AJAX Error:', status, error);
                        $('#invoiceContent').html(
                            '<div class="alert alert-danger">Failed to load invoice. Please try again.</div>'
                        );
                        openInvoiceSidebar();
                    }
                });
            });

            // Close handlers
            $(document).on('click', '#invoiceOverlay', closeInvoiceSidebar);
            $(document).on('keydown', function(e) {
                if (e.key === 'Escape') closeInvoiceSidebar();
            });

            // Debugging
            $(document).ready(function() {
                console.log('Invoice system initialized');
                console.log('Print buttons:', $('.print-invoice-btn').length);
            });

            // date control 






















            "use strict";
            $(document).on('ready', function() {
                // INITIALIZATION OF NAV SCROLLER
                // =======================================================
                $('.js-nav-scroller').each(function() {
                    new HsNavScroller($(this)).init()
                });

                // INITIALIZATION OF SELECT2
                // =======================================================
                $('.js-select2-custom').each(function() {
                    let select2 = $.HSCore.components.HSSelect2.init($(this));
                });


                // INITIALIZATION OF DATATABLES
                // =======================================================
                let datatable = $.HSCore.components.HSDatatables.init($('#datatable'), {
                    dom: 'Bfrtip',
                    buttons: [{
                            extend: 'copy',
                            className: 'd-none'
                        },
                        {
                            extend: 'pdf',
                            className: 'd-none'
                        },
                        {
                            extend: 'print',
                            className: 'd-none'
                        },
                    ],
                    select: {
                        style: 'multi',
                        selector: 'td:first-child input[type="checkbox"]',
                        classMap: {
                            checkAll: '#datatableCheckAll',
                            counter: '#datatableCounter',
                            counterInfo: '#datatableCounterInfo'
                        }
                    },
                    language: {
                        zeroRecords: '<div class="text-center p-4">' +
                            '<img class="mb-3 w-7rem" src="{{ dynamicAsset('public/assets/admin') }}/svg/illustrations/sorry.svg" alt="Image Description">' +
                            '<p class="mb-0">{{ translate('No_data_to_show') }}</p>' +
                            '</div>'
                    }
                });

                $('#export-copy').click(function() {
                    datatable.button('.buttons-copy').trigger()
                });

                $('#export-excel').click(function() {
                    datatable.button('.buttons-excel').trigger()
                });

                $('#export-csv').click(function() {
                    datatable.button('.buttons-csv').trigger()
                });

                $('#export-pdf').click(function() {
                    datatable.button('.buttons-pdf').trigger()
                });

                $('#export-print').click(function() {
                    datatable.button('.buttons-print').trigger()
                });

                $('#toggleColumn_order').change(function(e) {
                    datatable.columns(1).visible(e.target.checked)
                })

                $('#toggleColumn_date').change(function(e) {
                    datatable.columns(2).visible(e.target.checked)
                })

                $('#toggleColumn_customer').change(function(e) {
                    datatable.columns(3).visible(e.target.checked)
                })

                $('#toggleColumn_order_status').change(function(e) {
                    datatable.columns(5).visible(e.target.checked)
                })


                $('#toggleColumn_total').change(function(e) {
                    datatable.columns(4).visible(e.target.checked)
                })

                $('#toggleColumn_actions').change(function(e) {
                    datatable.columns(6).visible(e.target.checked)
                })


                // INITIALIZATION OF TAGIFY
                // =======================================================
                $('.js-tagify').each(function() {
                    let tagify = $.HSCore.components.HSTagify.init($(this));
                });
            });
        </script>


    @endpush
