<div class="col-xl-3 col-sm-6">
    <a class="resturant-card dashboard--card bg--2" href="{{route('vendor.order.list',['all'])}}">
        <h4 class="title">{{$data['all']}}</h4>
        <span class="subtitle">All Orders</span>
        <img class="resturant-icon max-width-34px" src="{{dynamicAsset('/public/assets/admin/img/resturant-panel/dashboard/top-resturant.png')}}" alt="">
        <!--<img class="resturant-icon max-width-34px" src="{{dynamicAsset('public/assets/admin/img/resturant-panel/dashboard/confirmed.png')}}" alt="dashboard">-->
    </a>
</div>

<div class="col-xl-3 col-sm-6">
    <!-- Card -->
    <a class="resturant-card dashboard--card bg--3" href="{{route('vendor.order.list',['new'])}}">
        <h4 class="title">{{$data['cooking']}}</h4>
        <span class="subtitle">New Orders</span>
        <img class="resturant-icon max-width-34px" src="{{dynamicAsset('public/assets/admin/img/resturant-panel/dashboard/cooking.png')}}" alt="dashboard">
    </a>
    <!-- End Card -->
</div>

<div class="col-xl-3 col-sm-6">
    <!-- Card -->
    <a class="resturant-card dashboard--card bg--5" href="{{route('vendor.order.list',['accepted'])}}">
        <h4 class="title">{{$data['ready_for_delivery']}}</h4>
        <span class="subtitle">Accepted Orders</span>
        <img class="resturant-icon max-width-34px" src="{{dynamicAsset('/public/assets/admin/img/resturant-panel/dashboard/scheduled.png')}}" alt="">
        <!--<img class="resturant-icon max-width-34px" src="{{dynamicAsset('public/assets/admin/img/resturant-panel/dashboard/ready.png')}}" alt="dashboard">-->
    </a>
    <!-- End Card -->
</div>

<div class="col-xl-3 col-sm-6">
    <!-- Card -->
    <a class="resturant-card dashboard--card bg--14" href="{{route('vendor.order.list',['canceled'])}}">
        <h4 class="title">{{$data['food_on_the_way']}}</h4>
        <span class="subtitle">Canceled Orders</span>
        <img class="resturant-icon max-width-34px" src="{{dynamicAsset('public/assets/admin/img/resturant-panel/dashboard/on-the-way.png')}}" alt="dashboard">
    </a>
    <!-- End Card -->
</div>

<!--<div class="col-12">-->
<!--    <div class="row g-2 mt-2">-->
        <!--<div class="col-sm-6 col-lg-3">-->
        <!--    <a href="{{route('vendor.order.list',['delivered'])}}" class="order--card h-100">-->
        <!--        <div class="d-flex justify-content-between align-items-center">-->
        <!--            <h6 class="card-subtitle d-flex justify-content-between m-0 align-items-center">-->
        <!--                <img src="{{dynamicAsset('/public/assets/admin/img/resturant-panel/dashboard/all.png')}}" alt="">-->
        <!--                <span>{{translate('messages.delivered')}}</span>-->
        <!--            </h6>-->
        <!--            <span class="card-title h3">-->
        <!--                {{$data['delivered']}}-->
        <!--            </span>-->
        <!--        </div>-->
        <!--    </a>-->
        <!--</div>-->

        <!--<div class="col-sm-6 col-lg-3">-->
        <!--    <a href="{{route('vendor.order.list',['refunded'])}}" class="order--card h-100">-->
        <!--        <div class="d-flex justify-content-between align-items-center">-->
        <!--            <h6 class="card-subtitle d-flex justify-content-between m-0 align-items-center">-->
        <!--                <img src="{{dynamicAsset('/public/assets/admin/img/resturant-panel/dashboard/refunded.png')}}" alt="">-->
        <!--                <span>{{translate('messages.refunded')}}</span>-->
        <!--            </h6>-->
        <!--            <span-->
        <!--                class="card-title h3">{{$data['refunded']}}</span>-->
        <!--        </div>-->
        <!--    </a>-->
        <!--</div>-->

        <!--<div class="col-sm-6 col-lg-3">-->
        <!--    <a href="{{route('vendor.order.list',['scheduled'])}}" class="order--card h-100">-->
        <!--        <div class="d-flex justify-content-between align-items-center">-->
        <!--            <h6 class="card-subtitle d-flex justify-content-between m-0 align-items-center">-->
        <!--                <img src="{{dynamicAsset('/public/assets/admin/img/resturant-panel/dashboard/scheduled.png')}}" alt="">-->
        <!--                <span>{{translate('messages.scheduled')}}</span>-->
        <!--            </h6>-->
        <!--            <span-->
        <!--                class="card-title h3">{{$data['scheduled']}}</span>-->
        <!--        </div>-->
        <!--    </a>-->
        <!--</div>-->

        <!--<div class="col-sm-6 col-lg-3">-->
        <!--    <a href="{{route('vendor.order.list',['all'])}}" class="order--card h-100">-->
        <!--        <div class="d-flex justify-content-between align-items-center">-->
        <!--            <h6 class="card-subtitle d-flex justify-content-between m-0 align-items-center">-->
        <!--                <img src="{{dynamicAsset('/public/assets/admin/img/resturant-panel/dashboard/top-resturant.png')}}" alt="">-->
        <!--                <span>{{translate('messages.all')}}</span>-->
        <!--            </h6>-->
        <!--            <span-->
        <!--                class="card-title h3">{{$data['all']}}</span>-->
        <!--        </div>-->
        <!--    </a>-->
        <!--</div>-->
<!--    </div>-->
<!--</div>-->
