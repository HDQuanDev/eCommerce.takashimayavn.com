@extends('seller.layouts.app')

@section('panel_content')
<div class="bg-light">
    <div class="container py-5">
        <div class="row text-center mb-5">
            <div class="col">
                <h2 class="display-4 fw-bold">{{ translate('Choose Your Package') }}</h2>
            </div>
        </div>
        <div class="row row-cols-1 row-cols-md-3 g-4 justify-content-center">
            <!-- Basic Plan -->
            @foreach ($packages as $package)
            <div class="col">
                <div class="card h-100 pricing-card shadow-sm">
                    <div class="card-body p-5">
                        <h5 class="card-title text-muted text-uppercase mb-4">{{ $package->name }}</h5>
                        <h1 class="display-5 mb-4">{{number_format(convert_price($package->price), 2) }} {{ currency_symbol() }}</h1>
                        <ul class="list-unstyled feature-list">
                            <li><i class="bi bi-check2 text-primary me-2"></i>{{ translate('Reach: ') }} {{ number_format($package->reach) }}</li>
                        </ul>
                        <a href="{{ route('seller.ads-packages.register', $package->id) }}" class="btn btn-outline-primary btn-lg w-100 mt-4">{{ translate('Get Started') }}</a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>
@php
    $currentPackage = Auth::user()->seller_ads_packages()->wherePivot('end_date', '>=', now())->orderBy('reach', 'desc')->first();
@endphp
@if($currentPackage)
<div class="card my-2">
    <div class="card-header">
        <h5 class="mb-0 h6">{{ translate('Current Package') }}</h5>
    </div>
    <div class="card-body">
        <h5 class="card-title">{{ $currentPackage->name }}</h5>
        <p class="card-text">{{ translate('Reach: ') }} {{ number_format($currentPackage->reach) }}</p>
    </div>
</div>
@endif
<div class="card">
        <form class="" action="" id="sort_ads_history" method="GET">
            <div class="card-header row gutters-5">
                <div class="col">
                    <h5 class="mb-md-0 h6">{{ translate('Ads Packages History') }}</h5>
                </div>
            </div>
        </form>
        <div class="card-body">
            <table class="table aiz-table mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th data-breakpoints="lg">{{ translate('Package Name') }}</th>
                        <th>{{ translate('Price') }}</th>
                        <th>{{ translate('Reach') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($packages_histories as $key => $package_history)
                        <tr>
                            <td>{{ $key + 1 }}</td>
                            <td>{{ $package_history->name }}</td>
                            <td>{{ number_format(convert_price($package_history->price), 2) }} {{ currency_symbol() }}</td>
                            <td>{{ $package_history->reach }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="aiz-pagination mt-4">

            </div>
        </div>
    </div>
@endsection

@section('script')
<script type="text/javascript">
    function sort_ads_history(el){
        $('#sort_ads_history').submit();
    }
</script>
@endsection
