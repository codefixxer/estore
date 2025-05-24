@extends('layouts.vendor.app')

@section('title',translate('messages.Order List'))

<!--@push('css_or_js')-->
<!--    <meta name="csrf-token" content="{{ csrf_token() }}">-->
<!--@endpush-->


@push('css_or_js')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        /* Invoice Sidebar Styles */
        .invoice-sidebar {
            position: fixed;
            top: 0;
            right: -450px;
            width: 450px;
            height: 100vh;
            background: white;
            z-index: 1050;
            box-shadow: -5px 0 15px rgba(0,0,0,0.1);
            transition: right 0.3s ease;
            overflow-y: auto;
            padding: 20px;
        }
        .invoice-sidebar.active {
            right: 0;
        }
        .sidebar-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            z-index: 1040;
            display: none;
        }
        .close-sidebar {
            position: absolute;
            top: 10px;
            right: 10px;
            font-size: 24px;
            cursor: pointer;
            z-index: 1051;
        }
        .loading-spinner {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 200px;
        }
    </style>
@endpush



@section('content')
<?php
?>
    <div class="content container-fluid">
        <!-- Page Header -->
        <div class="page-header pt-0 pb-2">
            <div class="d-flex flex-wrap justify-content-between">
                <h2 class="page-header-title align-items-center text-capitalize py-2 mr-2">
                    <div class="card-header-icon d-inline-flex mr-2 img">
                        @if(str_replace('_',' ',$status) == 'All')
                            <img class="mw-24px" src="{{dynamicAsset('/public/assets/admin/img/resturant-panel/page-title/order.png')}}" alt="public">
                        @elseif(str_replace('_',' ',$status) == 'Pending')
                            <img class="mw-24px" src="{{dynamicAsset('/public/assets/admin/img/resturant-panel/page-title/pending.png')}}" alt="public">
                        @elseif(str_replace('_',' ',$status) == 'Confirmed')
                            <img class="mw-24px" src="{{dynamicAsset('/public/assets/admin/img/resturant-panel/page-title/confirm.png')}}" alt="public">
                        @elseif(str_replace('_',' ',$status) == 'Cooking')
                            <img class="mw-24px" src="{{dynamicAsset('/public/assets/admin/img/resturant-panel/page-title/cooking.png')}}" alt="public">
                        @elseif(str_replace('_',' ',$status) == 'Ready for delivery')
                            <img class="mw-24px" src="{{dynamicAsset('/public/assets/admin/img/resturant-panel/page-title/ready.png')}}" alt="public">
                        @elseif(str_replace('_',' ',$status) == 'Food on the way')
                            <img class="mw-24px" src="{{dynamicAsset('/public/assets/admin/img/resturant-panel/page-title/ready.png')}}" alt="public">
                        @elseif(str_replace('_',' ',$status) == 'Delivered')
                            <img class="mw-24px" src="{{dynamicAsset('/public/assets/admin/img/resturant-panel/page-title/ready.png')}}" alt="public">
                        @elseif(str_replace('_',' ',$status) == 'Refunded')
                            <img class="mw-24px" src="{{dynamicAsset('/public/assets/admin/img/resturant-panel/page-title/order.png')}}" alt="public">
                        @elseif(str_replace('_',' ',$status) == 'Scheduled')
                            <img class="mw-24px" src="{{dynamicAsset('/public/assets/admin/img/resturant-panel/page-title/order.png')}}" alt="public">
                        @endif
                    </div>
                    <span>
                        {{str_replace('_',' ',$status)}} {{translate('messages.orders')}} <span class="badge badge-soft-dark ml-2">{{$orders->total()}}</span>
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
                    <form >
                        <!-- Search -->
                        <div class="input-group input--group">
                            <input id="datatableSearch_" type="search" name="search" class="form-control" value="{{ request()?->search ?? null}}"
                                    placeholder="{{ translate('Ex : Search by Order Id') }}" aria-label="{{translate('messages.search')}}">
                            <button type="submit" class="btn btn--secondary">
                                <i class="tio-search"></i>
                            </button>
                        </div>
                        <!-- End Search -->
                    </form>
                    
                    
                    
                    
                    <div class="d-sm-flex justify-content-sm-end align-items-sm-center m-0">
    <!-- Date Filter -->
    
    @if(str_replace('_',' ',$status) != 'pending')
    <div class="mr-2">
<form id="dateFilterForm" class="form-inline">
    <div class="form-group position-relative d-flex align-items-center">
        <!-- Actual date input -->
        <input type="date" name="filter_date" id="filter_date"
               class="form-control pr-5"
               value="{{ request()->filter_date ?? \Carbon\Carbon::now('Europe/Dublin')->format('Y-m-d') }}" />

        <!-- Overlay span shown only when today -->
        <span id="todayLabel"
              style="position: absolute; left: 12px; color: gray; pointer-events: none; display: none;">
              Today
        </span>
    </div>
</form>
@endif
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const dateInput = document.getElementById('filter_date');
        const todayLabel = document.getElementById('todayLabel');

        const today = new Date().toLocaleDateString('en-CA', { timeZone: 'Europe/Dublin' });

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

        dateInput.addEventListener('input', function (e) {
            updateLabel();
            // Optional: prevent auto-submitting behavior
            e.preventDefault();
        });

        // Also make sure the form does not auto-submit on change
        const form = document.getElementById('dateFilterForm');
        form.addEventListener('submit', function (e) {
            e.preventDefault(); // only if you want to stop submission
        });
    });
</script>





    </div>
    
    <!-- Keep Export Button -->
    <!--<div class="hs-unfold">-->
    <!--    <a class="js-hs-unfold-invoker btn btn-sm btn-white" href="{{route('vendor.order.export', ['status'=>$st, 'type'=>'excel', request()->getQueryString()])}}">-->
    <!--        <i class="tio-download-to mr-1"></i> {{translate('messages.export')}}-->
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
                        <!--        <i class="tio-download-to mr-1"></i> {{translate('messages.export')}}-->
                        <!--    </a>-->

                        <!--    <div id="usersExportDropdown"-->
                        <!--            class="hs-unfold-content dropdown-unfold dropdown-menu dropdown-menu-sm-right">-->

                        <!--        <span-->
                        <!--            class="dropdown-header">{{translate('messages.download_options')}}</span>-->
                        <!--        <a id="export-excel" class="dropdown-item" href="{{route("vendor.order.export",['status'=>$st,'type'=>'excel',request()->getQueryString() ])}}">-->
                        <!--            <img class="avatar avatar-xss avatar-4by3 mr-2"-->
                        <!--                    src="{{dynamicAsset('public/assets/admin')}}/svg/components/excel.svg"-->
                        <!--                    alt="Image Description">-->
                        <!--            {{translate('messages.excel')}}-->
                        <!--        </a>-->
                        <!--        <a id="export-csv" class="dropdown-item" href="{{route("vendor.order.export",['status'=>$st,'type'=>'csv',request()->getQueryString() ])}}">-->
                        <!--            <img class="avatar avatar-xss avatar-4by3 mr-2"-->
                        <!--                    src="{{dynamicAsset('public/assets/admin')}}/svg/components/placeholder-csv-format.svg"-->
                        <!--                    alt="Image Description">-->
                        <!--            {{translate('messages.csv')}}-->
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
                        <th class="w-90px table-column-pl-0">{{translate('messages.Order_ID')}}</th>
                        <th class="w-140px">{{translate('messages.order_date')}}</th>
                        <th class="w-140px">{{translate('messages.customer_information')}}</th>
                        <th class="w-100px">{{translate('messages.total_amount')}}</th>
                        <th class="w-100px text-center">{{translate('messages.order_status')}}</th>
                        <th class="w-100px text-center">{{translate('messages.actions')}}</th>
                    </tr>
                    </thead>

                    <tbody id="set-rows">
                    @foreach($orders as $key=>$order)
                        <tr class="status-{{$order['order_status']}} class-all">
                            <td class="">
                                {{$key+$orders->firstItem()}}
                            </td>
                            <td class="table-column-pl-0">
                                <a href="{{route('vendor.order.details',['id'=>$order['id']])}}" class="text-hover">{{$order['id']}}</a>
                            </td>
                            <td>
                                <span class="d-block">
                                    {{ Carbon\Carbon::parse($order['created_at'])->locale(app()->getLocale())->translatedFormat('d M Y') }}
                                </span>
                                <span class="d-block text-uppercase">
                                    {{ Carbon\Carbon::parse($order['created_at'])->locale(app()->getLocale())->translatedFormat(config('timeformat')) }}
                                </span>
                            </td>
                            <td>
                                @if($order->is_guest)
                                     <?php
                                        $customer_details = json_decode($order['delivery_address'],true);
                                    ?>
                                    <strong>{{$customer_details['contact_person_name']}}</strong>
                                    <div>{{$customer_details['contact_person_number']}}</div>
                                @elseif($order->customer)
                                    <a class="text-body text-capitalize"
                                        href="{{route('vendor.order.details',['id'=>$order['id']])}}">
                                        <span class="d-block font-semibold">
                                                {{$order->customer['f_name'].' '.$order->customer['l_name']}}
                                        </span>
                                        <span class="d-block">
                                                {{$order->customer['phone']}}
                                        </span>
                                    </a>
                                @else
                                    <label
                                        class="badge badge-danger">{{translate('messages.invalid_customer_data')}}</label>
                                @endif
                            </td>
                            <td>


                                <div class="text-right mw-85px">
                                    <div>
                                        {{\App\CentralLogics\Helpers::format_currency($order['order_amount'])}}
                                    </div>
                                    @if($order->payment_status=='paid')
                                    <strong class="text-success">
                                        {{translate('messages.paid')}}
                                    </strong>
                                    @elseif($order->payment_status=='partially_paid')
                                        <strong class="text-success">
                                            {{translate('messages.partially_paid')}}
                                        </strong>
                                    @else
                                        <strong class="text-danger">
                                            {{translate('messages.unpaid')}}
                                        </strong>
                                    @endif
                                </div>

                            </td>
                            <td class="text-capitalize text-center">
                                @if (isset($order->subscription)  && $order->subscription->status != 'canceled' )
                                    @php
                                        $order->order_status = $order->subscription_log ? $order->subscription_log->order_status : $order->order_status;
                                    @endphp
                                @endif
                                    @if($order['order_status']=='pending')
                                        <span class="badge badge-soft-info mb-1">
                                            {{translate('messages.pending')}}
                                        </span>
                                    @elseif($order['order_status']=='confirmed')
                                        <span class="badge badge-soft-info mb-1">
                                        {{translate('messages.confirmed')}}
                                        </span>
                                    @elseif($order['order_status']=='processing')
                                        <span class="badge badge-soft-warning mb-1">
                                        {{translate('messages.processing')}}
                                        </span>
                                    @elseif($order['order_status']=='picked_up')
                                        <span class="badge badge-soft-warning mb-1">
                                        {{translate('messages.out_for_delivery')}}
                                        </span>
                                    @elseif($order['order_status']=='delivered')
                                        <span class="badge badge-soft-success mb-1">
                                            {{$order?->order_type == 'dine_in' ? translate('messages.Completed') : translate('messages.delivered')}}
                                        </span>
                                    @else
                                        <span class="badge badge-soft-danger mb-1">
                                            {{translate(str_replace('_',' ',$order['order_status']))}}
                                        </span>
                                    @endif


                                <div class="text-capitalze opacity-7">
                                    @if($order['order_type']=='take_away')
                                        <span>
                                            {{translate('messages.take_away')}}
                                        </span>
                                        @elseif ($order['order_type'] == 'dine_in')
                                            <span>
                                                {{ translate('Dine_in') }}
                                            </span>
                                        @else
                                        <span>
                                            {{translate('messages.delivery')}}
                                        </span>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <div class="btn--container justify-content-center">
                                    <a class="btn action-btn btn--warning btn-outline-warning" href="{{route('vendor.order.details',['id'=>$order['id']])}}"><i class="tio-visible-outlined"></i></a>
                                    <a class="btn action-btn btn--primary btn-outline-primary" target="_blank" href="{{route('vendor.order.generate-invoice',[$order['id']])}}"><i class="tio-print"></i></a>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            @if(count($orders) === 0)
            <div class="empty--data">
                <img src="{{dynamicAsset('/public/assets/admin/img/empty.png')}}" alt="public">
                <h5>
                    {{translate('no_data_found')}}
                </h5>
            </div>
            @endif
            <!-- End Table -->

            <!-- Footer -->
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
@endsection

@push('script_2')
    <script>
                function showInvoice(orderId) {
            // Show loading spinner
            $('#invoiceContent').html('<div class="loading-spinner"><div class="spinner-border text-primary"></div></div>');
            
            // Show the sidebar and overlay
            $('.sidebar-overlay').show();
            $('#invoiceSidebar').addClass('active');
            
            // Load invoice content via AJAX
            $.ajax({
                url: "{{ route('vendor.order.generate-invoice', ['']) }}/" + orderId,
                type: 'GET',
                success: function(response) {
                    $('#invoiceContent').html(response);
                },
                error: function() {
                    $('#invoiceContent').html('<div class="alert alert-danger">Failed to load invoice</div>');
                }
            });
        }
        
        // Function to close sidebar
        function closeInvoiceSidebar() {
            $('.sidebar-overlay').hide();
            $('#invoiceSidebar').removeClass('active');
        }
        
        // Close sidebar when clicking on overlay
        $(document).on('click', '.sidebar-overlay', function() {
            closeInvoiceSidebar();
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
        $('.date-display').text('(Today)').show();
    }
});    
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        
        "use strict";
        $(document).on('ready', function () {
            // INITIALIZATION OF NAV SCROLLER
            // =======================================================
            $('.js-nav-scroller').each(function () {
                new HsNavScroller($(this)).init()
            });

            // INITIALIZATION OF SELECT2
            // =======================================================
            $('.js-select2-custom').each(function () {
                let select2 = $.HSCore.components.HSSelect2.init($(this));
            });


            // INITIALIZATION OF DATATABLES
            // =======================================================
            let datatable = $.HSCore.components.HSDatatables.init($('#datatable'), {
                dom: 'Bfrtip',
                buttons: [
                    {
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
                        '<img class="mb-3 w-7rem" src="{{dynamicAsset('public/assets/admin')}}/svg/illustrations/sorry.svg" alt="Image Description">' +
                        '<p class="mb-0">{{ translate('No_data_to_show') }}</p>' +
                        '</div>'
                }
            });

            $('#export-copy').click(function () {
                datatable.button('.buttons-copy').trigger()
            });

            $('#export-excel').click(function () {
                datatable.button('.buttons-excel').trigger()
            });

            $('#export-csv').click(function () {
                datatable.button('.buttons-csv').trigger()
            });

            $('#export-pdf').click(function () {
                datatable.button('.buttons-pdf').trigger()
            });

            $('#export-print').click(function () {
                datatable.button('.buttons-print').trigger()
            });

            $('#toggleColumn_order').change(function (e) {
                datatable.columns(1).visible(e.target.checked)
            })

            $('#toggleColumn_date').change(function (e) {
                datatable.columns(2).visible(e.target.checked)
            })

            $('#toggleColumn_customer').change(function (e) {
                datatable.columns(3).visible(e.target.checked)
            })

            $('#toggleColumn_order_status').change(function (e) {
                datatable.columns(5).visible(e.target.checked)
            })


            $('#toggleColumn_total').change(function (e) {
                datatable.columns(4).visible(e.target.checked)
            })

            $('#toggleColumn_actions').change(function (e) {
                datatable.columns(6).visible(e.target.checked)
            })


            // INITIALIZATION OF TAGIFY
            // =======================================================
            $('.js-tagify').each(function () {
                let tagify = $.HSCore.components.HSTagify.init($(this));
            });
        });
    </script>

@endpush
