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
                        <h1 class="display-5 mb-4">{{number_format(convert_price($package->price), 2) }} {{ currency_symbol() }}<small class="text-muted fw-light">/ {{ $package->duration }} {{  translate('Days') }}</small></h1>
                        <ul class="list-unstyled feature-list">
                            <li><i class="bi bi-check2 text-primary me-2"></i>{{ translate('Increase product commission to: ') }} {{ $package->commission_percentage }}%</li>
                        </ul>
                        <div class="description">
                            {{ $package->description }}
                        </div>
                        <a href="{{$currentPackage && $currentPackage->id == $package->id ? '#': route('seller.commission-packages.register', $package->id) }}" class="btn btn-outline-primary btn-lg w-100 mt-4 {{$currentPackage && $currentPackage->id == $package->id ? 'disabled ' : ''}}">{{$currentPackage && $currentPackage->id == $package->id ? translate('Current Package') : translate('Get Started') }}</a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>
@if($currentPackage)
<div class="card">
    <div class="card-header">
        <h5 class="mb-0 h6">{{ translate('Current Package') }}</h5>
    </div>
    <div class="card-body">
        <h5 class="card-title">{{ $currentPackage->name }}</h5>
        <p class="card-text">{{ $currentPackage->description }}</p>
        <p class="card-text">{{ translate('Commission: ') }} {{ $currentPackage->commission_percentage }}%</p>
        <p class="card-text">{{ translate('Duration: ') }} {{ $currentPackage->duration }} {{ translate('Days') }}</p>
    </div>
</div>
@endif
    <div class="card">
        <form class="" action="" id="sort_commission_history" method="GET">
            <div class="card-header row gutters-5">
                <div class="col">
                    <h5 class="mb-md-0 h6">{{ translate('Commission Packages History') }}</h5>
                </div>
                {{-- <div class="col-lg-2">
                    <div class="form-group mb-0">
                        <input type="text" class="form-control form-control-sm aiz-date-range" id="search" name="date_range"@isset($date_range) value="{{ $date_range }}" @endisset placeholder="{{ translate('Daterange') }}" autocomplete="off">
                    </div>
                </div>
                <div class="col-auto">
                    <div class="form-group mb-0">
                        <button type="submit" class="btn btn-primary">{{ translate('Filter') }}</button>
                    </div>
                </div> --}}
            </div>
        </form>
        <div class="card-body">
            <table class="table aiz-table mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th data-breakpoints="lg">{{ translate('Package Name') }}</th>
                        <th>{{ translate('Commission') }}</th>
                        <th>{{ translate('Start Date') }}</th>
                        <th>{{ translate('End Date') }}</th>
                        {{-- <th data-breakpoints="lg">{{ translate('Status') }}</th> --}}
                    </tr>
                </thead>
                <tbody>
                    @foreach ($packages_histories as $key => $package_history)
                        <tr>
                            <td>{{ $key + 1 }}</td>
                            <td>{{ $package_history->name }}</td>
                            <td>{{ $package_history->commission_percentage }} %</td>
                            <td>{{ $package_history->pivot->start_date }}</td>
                            <td>{{ $package_history->pivot->end_date }}</td>
                            {{-- <td>
                                @if($package_history->pivot->status == 'active')
                                    <span class="badge badge-inline badge-success">{{ translate('Active') }}</span>
                                @else
                                    <span class="badge badge-inline badge-danger">{{ translate('Inactive') }}</span>
                                @endif
                            </td> --}}
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
    function sort_commission_history(el){
        $('#sort_commission_history').submit();
    }
</script>
@endsection
