
@extends('layouts.vendor.app')

@section('title', translate('messages.order_report'))

@push('css_or_js')
@endpush

@section('content')
    <div class="content container-fluid">
        <!-- Page Header -->
        <div class="page-header">
            <h1 class="page-header-title">
                <span class="page-header-icon">
                    <img src="{{dynamicAsset('/public/assets/admin/img/report/new/order_report.png')}}" class="w--22" alt="">
                </span>
                <span>
                    {{ translate('messages.order_report') }}
                </span>
            </h1>
        </div>
        <!-- End Page Header -->

  <div class="card mb-20">
        <div class="card-body">
            <h4 class="">Search Data</h4>
            <form method="get" id="filterForm">
                <div class="row g-3">
                    <div class="col-sm-6 col-md-3">
                        <select class="form-control" name="week" id="weekSelect">
                            <!-- Weeks will be populated by JavaScript -->
                        </select>
                    </div>
                    <div class="col-sm-6 col-md-3 ml-auto">
                        <button type="submit" class="btn btn-primary btn-block">Filter</button>
                    </div>
                </div>
                <!-- Hidden inputs for actual form submission -->
                <input type="hidden" name="filter" value="custom">
                <input type="hidden" name="from" id="hiddenFrom">
                <input type="hidden" name="to" id="hiddenTo">
            </form>
        </div>
    </div>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const weekSelect = document.getElementById('weekSelect');
        const filterForm = document.getElementById('filterForm');
        const hiddenFrom = document.getElementById('hiddenFrom');
        const hiddenTo = document.getElementById('hiddenTo');
        
        // Initialize the week dropdown
        populateWeekDropdown();
        
        // Handle week selection change
        weekSelect.addEventListener('change', function() {
            // Get the selected week's dates
            const selectedWeek = weekSelect.value;
            if (selectedWeek) {
                const [startDate, endDate] = selectedWeek.split('|');
                
                // Set the hidden inputs
                hiddenFrom.value = startDate;
                hiddenTo.value = endDate;
                
                // Submit the form automatically
                filterForm.submit();
            }
        });
        
        function populateWeekDropdown() {
            const currentYear = new Date().getFullYear();
            const today = new Date();
            
            // Find the first Monday of the year
            let firstMonday = new Date(currentYear, 0, 1);
            while (firstMonday.getDay() !== 1) {
                firstMonday.setDate(firstMonday.getDate() + 1);
            }
            
            // Get current week's Monday
            const currentWeekMonday = getMondayOfCurrentWeek();
            
            // Create an array to store all weeks
            const weeks = [];
            
            // Check if there's a previously selected week in URL parameters
            const urlParams = new URLSearchParams(window.location.search);
            const selectedFrom = urlParams.get('from');
            const selectedTo = urlParams.get('to');
            let hasSelectedWeek = false;
            
            // Loop through all weeks from current week back to first week of the year
            let currentDate = new Date(currentWeekMonday);
            while (currentDate >= firstMonday) {
                const startDate = new Date(currentDate);
                const endDate = new Date(currentDate);
                endDate.setDate(endDate.getDate() + 6);
                
                // Format dates as YYYY-MM-DD
                const startDateStr = formatDate(startDate);
                const endDateStr = formatDate(endDate);
                
                // Calculate week number
                const weekNumber = getWeekNumber(startDate);
                
                // Check if this week matches the selected week from URL
                const isSelectedWeek = (selectedFrom && selectedTo && 
                                      startDateStr === selectedFrom && 
                                      endDateStr === selectedTo);
                
                // Check if this is the current week
                const isCurrentWeek = (startDate.getTime() === currentWeekMonday.getTime());
                
                // Add to weeks array
                weeks.push({
                    weekNumber: weekNumber,
                    startDate: startDate,
                    endDate: endDate,
                    startDateStr: startDateStr,
                    endDateStr: endDateStr,
                    isCurrent: isCurrentWeek,
                    isSelected: isSelectedWeek
                });
                
                if (isSelectedWeek) hasSelectedWeek = true;
                
                // Move to previous week
                currentDate.setDate(currentDate.getDate() - 7);
            }
            
            // Add weeks to dropdown
            weeks.forEach(week => {
                const option = document.createElement('option');
                option.value = `${week.startDateStr}|${week.endDateStr}`;
                
                let label;
                if (week.isSelected) {
                    label = `Selected Week (${formatDisplayDate(week.startDate)} - ${formatDisplayDate(week.endDate)})`;
                } else if (week.isCurrent) {
                    label = `Current Week (${formatDisplayDate(week.startDate)} - ${formatDisplayDate(week.endDate)})`;
                } else {
                    label = `Week ${week.weekNumber} (${formatDisplayDate(week.startDate)} - ${formatDisplayDate(week.endDate)})`;
                }
                
                option.textContent = label;
                option.selected = week.isSelected || (!hasSelectedWeek && week.isCurrent);
                
                // Set the hidden values if this is the selected option
                if (option.selected) {
                    hiddenFrom.value = week.startDateStr;
                    hiddenTo.value = week.endDateStr;
                }
                
                weekSelect.appendChild(option);
            });
        }
        
        function getMondayOfCurrentWeek() {
            const today = new Date();
            const currentDay = today.getDay();
            const diff = today.getDate() - currentDay + (currentDay === 0 ? -6 : 1); // adjust when day is Sunday
            return new Date(today.setDate(diff));
        }
        
        // Helper function to get ISO week number
        function getWeekNumber(date) {
            const d = new Date(date);
            d.setHours(0, 0, 0, 0);
            d.setDate(d.getDate() + 3 - (d.getDay() + 6) % 7);
            const week1 = new Date(d.getFullYear(), 0, 4);
            return 1 + Math.round(((d - week1) / 86400000 - 3 + (week1.getDay() + 6) % 7) / 7);
        }
        
        function formatDate(date) {
            const year = date.getFullYear();
            const month = String(date.getMonth() + 1).padStart(2, '0');
            const day = String(date.getDate()).padStart(2, '0');
            return `${year}-${month}-${day}`;
        }
        
        function formatDisplayDate(date) {
            const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
            const day = date.getDate();
            const month = months[date.getMonth()];
            return `${day} ${month}`;
        }
    });
</script>
        <!--<div class="card mb-20">-->
        <!--    <div class="card-body">-->
        <!--        <h4 class="">{{ translate('Search_Data') }}</h4>-->
        <!--        <form  method="get">-->

        <!--            <div class="row g-3">-->
        <!--                <div class="col-sm-6 col-md-3">-->
        <!--                    <select class="form-control set-filter" name="filter"-->
        <!--                            data-url="{{ url()->full() }}" data-filter="filter">-->
        <!--                        <option value="all_time" {{ isset($filter) && $filter == 'all_time' ? 'selected' : '' }}>-->
        <!--                            {{ translate('messages.All_Time') }}</option>-->
        <!--                        <option value="this_year" {{ isset($filter) && $filter == 'this_year' ? 'selected' : '' }}>-->
        <!--                            {{ translate('messages.This_Year') }}</option>-->
        <!--                        <option value="previous_year"-->
        <!--                            {{ isset($filter) && $filter == 'previous_year' ? 'selected' : '' }}>-->
        <!--                            {{ translate('messages.Previous_Year') }}</option>-->
        <!--                        <option value="this_month"-->
        <!--                            {{ isset($filter) && $filter == 'this_month' ? 'selected' : '' }}>-->
        <!--                            {{ translate('messages.This_Month') }}</option>-->
        <!--                        <option value="this_week" {{ isset($filter) && $filter == 'this_week' ? 'selected' : '' }}>-->
        <!--                            {{ translate('messages.This_Week') }}</option>-->
        <!--                        <option value="custom" {{ isset($filter) && $filter == 'custom' ? 'selected' : '' }}>-->
        <!--                            {{ translate('messages.Custom') }}</option>-->
        <!--                    </select>-->
        <!--                </div>-->
        <!--                @if (isset($filter) && $filter == 'custom')-->
        <!--                 <div class="col-sm-6 col-md-3">-->
        <!--                    <input type="date" name="from" id="from_date" class="form-control"-->
        <!--                        placeholder="{{ translate('Start_Date') }}"-->
        <!--                        value={{ isset($from) ? $from  : '' }} required>-->
        <!--                </div>-->
        <!--                <div class="col-sm-6 col-md-3">-->
        <!--                    <input type="date" name="to" id="to_date" class="form-control"-->
        <!--                        placeholder="{{ translate('End_Date') }}"-->
        <!--                        value={{ isset($to) ? $to  : '' }} required>-->
        <!--                </div>-->
        <!--                @endif-->
        <!--                <div class="col-sm-6 col-md-3 ml-auto">-->
        <!--                    <button type="submit"-->
        <!--                        class="btn btn-primary btn-block">{{ translate('Filter') }}</button>-->
        <!--                </div>-->
        <!--            </div>-->
        <!--        </form>-->
        <!--    </div>-->
        <!--</div>-->
        
        
        
<div class="mb-20">
    <div class="row g-3">  <!-- Increased gutter spacing to 3 -->
        <!-- Left Column (Graph) - 50% width -->
        <div class="col-lg-6 col-md-12">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="card-title">{{ translate('messages.weekly_order_amounts') }}</h5>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="weeklyOrderChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column (Order Status Cards) - 50% width -->
        <div class="col-lg-6 col-md-12">
            <div class="row g-3">  <!-- 2x2 grid layout -->
                <!-- First Row -->
                <div class="col-md-6">
                    <a class="order--card h-100" href="#">
                        <div class="d-flex justify-content-between align-items-center">
                            <h6 class="card-subtitle d-flex justify-content-between m-0 align-items-center">
                                <img src="{{dynamicAsset('/public/assets/admin/img/order-icons/accepted.png')}}" alt="dashboard" class="oder--card-icon">
                                <span>All Orders</span>
                            </h6>
                            <span class="card-title" style="--base-clr:#0661CB">
                                {{ $all_orders }}
                            </span>
                        </div>
                    </a>
                </div>
                <div class="col-md-6">
                    <a class="order--card h-100" href="#">
                        <div class="d-flex justify-content-between align-items-center">
                            <h6 class="card-subtitle d-flex justify-content-between m-0 align-items-center">
                                <img src="{{dynamicAsset('/public/assets/admin/img/order-icons/canceled.png')}}" alt="dashboard" class="oder--card-icon">
                                <span>Revenue</span>
                            </h6>
                            <span class="card-title" style="--base-clr:#FF7500">
                                {{ $total_revenue }}
                            </span>
                        </div>
                    </a>
                </div>
                
                <!-- Second Row -->
                
                 <div class="col-md-6">
                    <a class="order--card h-100" href="#">
                        <div class="d-flex justify-content-between align-items-center">
                            <h6 class="card-subtitle d-flex justify-content-between m-0 align-items-center">
                                <img src="{{dynamicAsset('/public/assets/admin/img/order-icons/accepted.png')}}" alt="dashboard" class="oder--card-icon">
                                <span>{{ translate('Accepted_Orders') }}</span>
                            </h6>
                            <span class="card-title" style="--base-clr:#0661CB">
                                {{ $total_confirmed_count }}
                            </span>
                        </div>
                    </a>
                </div>
                
                <div class="col-md-6">
                    <a class="order--card h-100" href="#">
                        <div class="d-flex justify-content-between align-items-center">
                            <h6 class="card-subtitle d-flex justify-content-between m-0 align-items-center">
                                <img src="{{dynamicAsset('/public/assets/admin/img/order-icons/failed.png')}}" alt="dashboard" class="oder--card-icon">
                                <span>Canceled Orders</span>
                            </h6>
                            <span class="card-title" style="--base-clr:#FF7500">
                                {{ $total_canceled_count }}
                            </span>
                        </div>
                    </a>
                </div>
               
                <!-- Second Row -->
                <div class="col-md-6">
                    <a class="order--card h-100" href="#">
                        <div class="d-flex justify-content-between align-items-center">
                            <h6 class="card-subtitle d-flex justify-content-between m-0 align-items-center">
                                <img src="{{dynamicAsset('/public/assets/admin/img/order-icons/failed.png')}}" alt="dashboard" class="oder--card-icon">
                                <span>Transaction</span>
                            </h6>
                            <span class="card-title" style="--base-clr:#FF7500">
                                {{ $total_trans }}
                            </span>
                        </div>
                    </a>
                </div>
                <div class="col-md-6">
                    <a class="order--card h-100" href="#">
                        <div class="d-flex justify-content-between align-items-center">
                            <h6 class="card-subtitle d-flex justify-content-between m-0 align-items-center">
                                <img src="{{dynamicAsset('/public/assets/admin/img/order-icons/delivered.png')}}" alt="dashboard" class="oder--card-icon">
                                <span>Payment Failed</span>
                            </h6>
                            <span class="card-title" style="--base-clr:#00AA6D">
                                <!--{{ $total_failed_count }}-->
                                {{$total_pending_count}}
                            </span>
                        </div>
                    </a>
                </div>
                
                <!-- Empty Space Row (for spacing) -->
            </div>
        </div>
    </div>
</div>



<h1>hello</h1>

<style>
    .chart-container {
        position: relative;
        height: 350px;
        width: 100%;
    }
    
    .order--card {
        display: block;
        padding: 15px;
        background: white;
        border-radius: 10px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        transition: transform 0.3s ease;
        height: 100%;
        min-height: 120px; /* Fixed height for consistency */
    }
    
    .order--card:hover {
        transform: translateY(-5px);
    }
    
    /* Responsive adjustments */
    @media (max-width: 992px) {
        .order--card {
            min-height: 100px;
        }
    }
    
    @media (max-width: 768px) {
        .chart-container {
            height: 300px;
        }
        .order--card {
            min-height: 90px;
        }
    }
</style>


                <!-- Graph Section -->


<!-- End Graph Section -->

@push('script_2')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        try {
            // Canvas check
            const canvas = document.getElementById('weeklyOrderChart');
            if (!canvas) {
                console.error('Canvas element not found!');
                return;
            }

            // Data from backend
            const weeklyData = {
                labels: [
                    "{{ translate('messages.mon') }}", 
                    "{{ translate('messages.tue') }}",
                    "{{ translate('messages.wed') }}",
                    "{{ translate('messages.thu') }}",
                    "{{ translate('messages.fri') }}",
                    "{{ translate('messages.sat') }}",
                    "{{ translate('messages.sun') }}"
                ],
                amounts: @json($weeklyRevenueOrdered ?? array_fill(0, 7, 0))
            };

            // Create chart
            new Chart(canvas.getContext('2d'), {
                type: 'bar',
                data: {
                    labels: weeklyData.labels,
                    datasets: [{
                        label: "{{ translate('messages.order_amount') }}",
                        data: weeklyData.amounts,
                        backgroundColor: 'rgba(54, 162, 235, 0.7)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return value + ' {{ \App\CentralLogics\Helpers::currency_symbol() }}';
                                }
                            }
                        }
                    }
                }
            });

        } catch (error) {
            console.error('Chart error:', error);
        }
    });
</script>
@endpush





<!--@push('script_2')-->
<!--<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>-->
<!--<script>-->
<!--    document.addEventListener('DOMContentLoaded', function () {-->
        // Sample data - replace with your actual data from backend
<!--        const weeklyData = {-->
<!--            labels: [-->
<!--                "{{ translate('messages.mon') }}", -->
<!--                "{{ translate('messages.tue') }}",-->
<!--                "{{ translate('messages.wed') }}",-->
<!--                "{{ translate('messages.thu') }}",-->
<!--                "{{ translate('messages.fri') }}",-->
<!--                "{{ translate('messages.sat') }}",-->
<!--                "{{ translate('messages.sun') }}"-->
<!--            ],-->
            amounts: [150, 220, 180, 250, 400, 130, 280] // Dummy data
<!--        };-->

        // Get the canvas element
<!--        const ctx = document.getElementById('weeklyOrderChart').getContext('2d');-->
        
        // Create the chart
<!--        const weeklyOrderChart = new Chart(ctx, {-->
<!--            type: 'bar',-->
<!--            data: {-->
<!--                labels: weeklyData.labels,-->
<!--                datasets: [{-->
<!--                    label: "{{ translate('messages.order_amount') }}",-->
<!--                    data: weeklyData.amounts,-->
<!--                    backgroundColor: 'rgba(54, 162, 235, 0.7)',-->
<!--                    borderColor: 'rgba(54, 162, 235, 1)',-->
<!--                    borderWidth: 1,-->
                    barPercentage: 0.7, // Adjusted for narrower container
<!--                    categoryPercentage: 0.8-->
<!--                }]-->
<!--            },-->
<!--            options: {-->
<!--                responsive: true,-->
<!--                maintainAspectRatio: false,-->
<!--                scales: {-->
<!--                    y: {-->
<!--                        beginAtZero: true,-->
<!--                        suggestedMax: Math.max(...weeklyData.amounts) * 1.2,-->
<!--                        title: {-->
<!--                            display: true,-->
<!--                            text: "{{ translate('messages.amount') }}",-->
<!--                            font: {-->
                                size: 12 // Smaller font for narrower space
<!--                            }-->
<!--                        },-->
<!--                        ticks: {-->
<!--                            callback: function(value) {-->
<!--                                return value + ' €';-->
<!--                            },-->
<!--                            font: {-->
                                size: 11 // Smaller ticks
<!--                            },-->
                            padding: 5 // Less padding
<!--                        }-->
<!--                    },-->
<!--                    x: {-->
<!--                        title: {-->
<!--                            display: true,-->
<!--                            text: "{{ translate('messages.day_of_week') }}",-->
<!--                            font: {-->
<!--                                size: 12-->
<!--                            }-->
<!--                        },-->
<!--                        grid: {-->
<!--                            display: false-->
<!--                        },-->
<!--                        ticks: {-->
<!--                            font: {-->
<!--                                size: 11-->
<!--                            }-->
<!--                        }-->
<!--                    }-->
<!--                },-->
<!--                plugins: {-->
<!--                    legend: {-->
<!--                        display: true,-->
<!--                        position: 'top',-->
<!--                        labels: {-->
                            boxWidth: 10, // Smaller legend items
<!--                            padding: 15,-->
<!--                            font: {-->
<!--                                size: 11-->
<!--                            }-->
<!--                        }-->
<!--                    },-->
<!--                    tooltip: {-->
<!--                        bodyFont: {-->
                            size: 12 // Smaller tooltip text
<!--                        },-->
<!--                        callbacks: {-->
<!--                            label: function(context) {-->
<!--                                return context.dataset.label + ': ' + context.raw + ' €';-->
<!--                            }-->
<!--                        }-->
<!--                    }-->
<!--                },-->
<!--                animation: {-->
<!--                    duration: 1000-->
<!--                },-->
<!--                layout: {-->
<!--                    padding: {-->
<!--                        top: 10,-->
<!--                        right: 10,-->
<!--                        bottom: 10,-->
<!--                        left: 10-->
<!--                    }-->
<!--                }-->
<!--            }-->
<!--        });-->

        // Make chart responsive on window resize
<!--        window.addEventListener('resize', function() {-->
<!--            weeklyOrderChart.resize();-->
<!--        });-->
<!--    });-->
<!--</script>-->
<!--@endpush-->

<style>
    .chart-container {
        position: relative;
        height: 350px;
        width: 100%;
        min-height: 300px;
        min-width: 300px; /* Prevent becoming too narrow */
    }
    
    @media (max-width: 992px) {
        .chart-container {
            width: 60%; /* Wider on medium screens */
        }
    }
    
    @media (max-width: 768px) {
        .chart-container {
            width: 80%; /* Wider on small screens */
            height: 300px;
        }
    }
    
    @media (max-width: 576px) {
        .chart-container {
            width: 100%; /* Full width on mobile */
            height: 250px;
        }
    }
</style>



<!--end of graph-->
        
        
        
        
        
        
        
        
        
        
        
        
        
        

        <!-- End Stats -->
        <!-- Card -->
        <div class="card mt-3">
            <!-- Header -->
            <div class="card-header border-0 py-2">
                <div class="search--button-wrapper">
                    <h3 class="card-title">
                        {{ translate('messages.Total_Orders') }} <span
                            class="badge badge-soft-secondary" id="countItems">{{ $orders->total() }}</span>
                    </h3>
                    <form  class="search-form">
                        <!-- Search -->
                        <div class="input--group input-group input-group-merge input-group-flush">
                            <input name="search" type="search" class="form-control"  value="{{ request()->search ?? null }}"  placeholder="{{ translate('Search_by_Order_ID') }}">
                            <button type="submit" class="btn btn--secondary"><i class="tio-search"></i></button>
                        </div>
                        <!-- End Search -->
                    </form>
                    <!-- Static Export Button -->
                    <div class="hs-unfold ml-3">
                        <a class="js-hs-unfold-invoker btn btn-sm btn-white dropdown-toggle btn export-btn font--sm"
                            href="javascript:;"
                            data-hs-unfold-options="{
                                &quot;target&quot;: &quot;#usersExportDropdown&quot;,
                                &quot;type&quot;: &quot;css-animation&quot;
                            }"
                            data-hs-unfold-target="#usersExportDropdown" data-hs-unfold-invoker="">
                            <i class="tio-download-to mr-1"></i> {{ translate('export') }}
                        </a>

                        <div id="usersExportDropdown"
                            class="hs-unfold-content dropdown-unfold dropdown-menu dropdown-menu-sm-right hs-unfold-content-initialized hs-unfold-css-animation animated hs-unfold-reverse-y hs-unfold-hidden">

                            <span class="dropdown-header">{{ translate('download_options') }}</span>
                            <a id="export-excel" class="dropdown-item"
                                href="{{ route('vendor.report.order-report-export', ['type' => 'excel', request()->getQueryString()]) }}">
                                <img class="avatar avatar-xss avatar-4by3 mr-2"
                                    src="{{ dynamicAsset('public/assets/admin/svg/components/excel.svg') }}"
                                    alt="Image Description">
                                {{ translate('messages.excel') }}
                            </a>
                            <a id="export-csv" class="dropdown-item"
                                href="{{ route('vendor.report.order-report-export', ['type' => 'csv', request()->getQueryString()]) }}">
                                <img class="avatar avatar-xss avatar-4by3 mr-2"
                                    src="{{ dynamicAsset('public/assets/admin/svg/components/placeholder-csv-format.svg') }}"
                                    alt="Image Description">
                                {{ translate('messages.csv') }}
                            </a>

                        </div>
                    </div>
                    <!-- Static Export Button -->
                </div>
            </div>
            <!-- End Header -->

            <!-- Body -->
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-borderless middle-align __txt-14px">
                        <thead class="thead-light white--space-false">
                            <tr>
                                <th class="border-top border-bottom word-nobreak">{{ translate('messages.sl') }}</th>
                                <th class="border-top border-bottom word-nobreak">{{ translate('messages.order_id') }}</th>
                                <th class="border-top border-bottom word-nobreak">{{ translate('messages.restaurant') }}</th>
                                <th class="border-top border-bottom word-nobreak">{{ translate('messages.customer_name') }}</th>
                                <th class="border-top border-bottom word-nobreak">{{ translate('messages.total_item_amount') }}</th>
                                <th class="border-top border-bottom word-nobreak">{{ translate('messages.item_discount') }}</th>
                                <th class="border-top border-bottom word-nobreak">{{ translate('messages.coupon_discount') }}</th>
                                <th class="border-top border-bottom word-nobreak">{{ translate('messages.referral_discount') }}</th>
                                <th class="border-top border-bottom word-nobreak">{{ translate('messages.discounted_amount') }}</th>
                                <th class="border-top border-bottom text-center">{{ translate('messages.tax') }}</th>
                                <th class="border-top border-bottom word-nobreak">{{ translate('messages.delivery_charge') }}</th>
                                <th class="border-top border-bottom text-center">{{ \App\CentralLogics\Helpers::get_business_data('additional_charge_name')??translate('messages.additional_charge') }}</th>
                                <th class="border-top border-bottom word-nobreak">{{ translate('messages.extra_packaging_amount') }}</th>
                                <th class="border-top border-bottom word-nobreak text-right">{{ translate('messages.order_amount') }}</th>
                                <th class="border-top border-bottom word-nobreak">{{ translate('messages.amount_received_by') }}</th>
                                <th class="border-top border-bottom word-nobreak">{{ translate('messages.payment_method') }}</th>
                                <th class="border-top border-bottom word-nobreak">{{ translate('messages.order_status') }}</th>
                                <th class="border-top border-bottom text-center">{{ translate('messages.action') }}
                                </th>
                            </tr>
                        </thead>
                        <tbody id="set-rows">
                            @foreach ($orders as $key => $order)
                                <tr class="status-{{ $order['order_status'] }} class-all">
                                    <td class="">
                                        {{ $key + $orders->firstItem() }}
                                    </td>
                                    <td class="table-column-pl-0">
                                        <a
                                            href="{{ route('vendor.order.details', ['id' => $order['id']]) }}">{{ $order['id'] }}</a>
                                    </td>
                                    <td  class="text-capitalize">
                                        @if($order->restaurant)
                                            {{Str::limit($order->restaurant->name,25,'...')}}
                                        @else
                                            <label class="badge badge-danger">{{ translate('messages.invalid') }}
                                        @endif
                                    </td>
                                    <td>
                                        @if ($order->customer)
                                            <a class="text-body text-capitalize"
                                                href="#">
                                                <strong>{{ $order->customer['f_name'] . ' ' . $order->customer['l_name'] }}</strong>
                                            </a>
                                        @elseif($order->is_guest)
                                             <?php
                                        $customer_details = json_decode($order['delivery_address'],true);
                                    ?>
                                            <strong>{{$customer_details['contact_person_name']}}</strong>
                                            <div>{{$customer_details['contact_person_number']}}</div>
                                        @else
                                            <label class="badge badge-danger">{{ translate('messages.invalid_customer_data') }}</label>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="text-right mw--85px">
                                            <div>
                                                {{ \App\CentralLogics\Helpers::number_format_short($order['order_amount']- $order['additional_charge'] - $order['dm_tips']-$order['total_tax_amount'] - $order['extra_packaging_amount']  -$order['delivery_charge']+$order['coupon_discount_amount'] + $order['restaurant_discount_amount'] + $order['ref_bonus_amount']) }}
                                            </div>
                                            @if ($order->payment_status == 'paid')
                                                <strong class="text-success">
                                                    {{ translate('messages.paid') }}
                                                </strong>
                                            @else
                                                <strong class="text-danger">
                                                    {{ translate('messages.unpaid') }}
                                                </strong>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="text-center mw--85px">
                                        {{ \App\CentralLogics\Helpers::number_format_short($order->details()->sum(DB::raw('discount_on_food * quantity'))) }}
                                    </td>
                                    <td class="text-center mw--85px">
                                        {{ \App\CentralLogics\Helpers::number_format_short($order['coupon_discount_amount']) }}
                                    </td>
                                    <td class="text-center mw--85px">
                                        {{ \App\CentralLogics\Helpers::number_format_short($order['ref_bonus_amount']) }}
                                    </td>
                                    <td class="text-center mw--85px">
                                        {{ \App\CentralLogics\Helpers::number_format_short($order['coupon_discount_amount'] + $order['restaurant_discount_amount'] + $order['ref_bonus_amount']) }}
                                    </td>
                                    <td class="text-center mw--85px white-space-nowrap">
                                        {{ \App\CentralLogics\Helpers::number_format_short($order['total_tax_amount']) }}
                                    </td>
                                    <td class="text-center mw--85px">
                                        {{ \App\CentralLogics\Helpers::number_format_short($order['delivery_charge']) }}
                                    </td>
                                    <td class="text-center mw--85px">
                                        {{ \App\CentralLogics\Helpers::number_format_short($order['additional_charge']) }}
                                    </td>
                                    <td class="text-center mw--85px">
                                        {{ \App\CentralLogics\Helpers::number_format_short($order['extra_packaging_amount']) }}
                                    </td>
                                    <td>
                                        <div class="text-right mw--85px">
                                            <div>
                                                {{ \App\CentralLogics\Helpers::number_format_short($order['order_amount']) }}
                                            </div>
                                            @if ($order->payment_status == 'paid')
                                                <strong class="text-success">
                                                    {{ translate('messages.paid') }}
                                                </strong>
                                            @else
                                                <strong class="text-danger">
                                                    {{ translate('messages.unpaid') }}
                                                </strong>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="text-center mw--85px text-capitalize">
                                        {{isset($order->transaction) ? translate(str_replace('_', ' ', $order->transaction->received_by))  : translate('messages.not_received_yet')}}
                                    </td>
                                    <td class="text-center mw--85px text-capitalize">
                                            {{ translate(str_replace('_', ' ', $order['payment_method'])) }}
                                    </td>
                                    <td class="text-center mw--85px text-capitalize">
                                        @if($order['order_status']=='pending')
                                                <span class="badge badge-soft-info">
                                                  {{translate('messages.pending')}}
                                                </span>
                                            @elseif($order['order_status']=='confirmed')
                                                <span class="badge badge-soft-info">
                                                  {{translate('messages.confirmed')}}
                                                </span>
                                            @elseif($order['order_status']=='processing')
                                                <span class="badge badge-soft-warning">
                                                  {{translate('messages.processing')}}
                                                </span>
                                            @elseif($order['order_status']=='picked_up')
                                                <span class="badge badge-soft-warning">
                                                  {{translate('messages.out_for_delivery')}}
                                                </span>
                                            @elseif($order['order_status']=='delivered')
                                                <span class="badge badge-soft-success">
                                                  {{$order?->order_type == 'dine_in' ? translate('messages.Completed') : translate('messages.delivered')}}
                                                </span>
                                            @elseif($order['order_status']=='failed')
                                                <span class="badge badge-soft-danger">
                                                  {{translate('messages.payment_failed')}}
                                                </span>
                                            @elseif($order['order_status']=='handover')
                                                <span class="badge badge-soft-danger">
                                                  {{translate('messages.handover')}}
                                                </span>
                                            @elseif($order['order_status']=='canceled')
                                                <span class="badge badge-soft-danger">
                                                  {{translate('messages.canceled')}}
                                                </span>
                                            @elseif($order['order_status']=='accepted')
                                                <span class="badge badge-soft-danger">
                                                  {{translate('messages.accepted')}}
                                                </span>
                                            @else
                                                <span class="badge badge-soft-danger">
                                                  {{translate(str_replace('_',' ',$order['order_status']))}}
                                                </span>
                                            @endif

                                    </td>


                                    <td>
                                        <div class="btn--container justify-content-center">
                                            <a class="ml-2 btn btn-sm btn--warning btn-outline-warning action-btn"
                                                href="{{ route('vendor.order.details', ['id' => $order['id']]) }}">
                                                <i class="tio-invisible"></i>
                                            </a>
                                            <a class="ml-2 btn btn-sm btn--primary btn-outline-primary action-btn"
                                                href="{{ route('vendor.order.generate-invoice', ['id' => $order['id']]) }}">
                                                <i class="tio-print"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <!-- End Table -->


            </div>
            <!-- End Body -->
            @if (count($orders) !== 0)
                <hr>
            @endif
            <div class="page-area px-4 pb-3">
                {!! $orders->links() !!}
            </div>
            @if (count($orders) === 0)
                <div class="empty--data">
                    <img src="{{ dynamicAsset('/public/assets/admin/svg/illustrations/sorry.svg') }}" alt="public">
                    <h5>
                        {{ translate('no_data_found') }}
                    </h5>
                </div>
            @endif
        </div>
        <!-- End Card -->
    </div>
@endsection

@push('script')
@endpush

@push('script_2')

    <script src="{{ dynamicAsset('public/assets/admin') }}/vendor/chart.js/dist/Chart.min.js"></script>
    <script src="{{ dynamicAsset('public/assets/admin') }}/vendor/chartjs-chart-matrix/dist/chartjs-chart-matrix.min.js">
    </script>
    <script src="{{ dynamicAsset('public/assets/admin') }}/js/hs.chartjs-matrix.js"></script>
    <script src="{{dynamicAsset('public/assets/admin')}}/js/view-pages/vendor/report.js"></script>

    <script>
        $(document).on('ready', function() {



            $('.js-data-example-ajax-2').select2({
                ajax: {
                    url: '{{ url('/') }}/admin/customer/select-list',
                    data: function(params) {
                        return {
                            q: params.term, // search term
                            all:true,
                            @if (isset($zone))
                                zone_ids: [{{ $zone->id }}],
                            @endif
                            @if (request('restaurant_id'))
                                restaurant_id: {{ request('restaurant_id') }},
                            @endif
                            page: params.page
                        };
                    },
                    processResults: function(data) {
                        return {
                            results: data
                        };
                    },
                    __port: function(params, success, failure) {
                        let $request = $.ajax(params);

                        $request.then(success);
                        $request.fail(failure);

                        return $request;
                    }
                }
            });
        });
    </script>
@endpush

