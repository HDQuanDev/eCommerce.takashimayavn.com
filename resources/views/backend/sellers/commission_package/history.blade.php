@extends('backend.layouts.app')

@section('content')
    <div class="aiz-titlebar text-left mt-2 mb-3">
        <div class=" align-items-center">
            <h1 class="h3">{{ translate('Commission Package History') }}</h1>
        </div>
    </div>

    <div class="row">
        <div class="col-md-10 mx-auto">
            <div class="card">
                <form action="{{ route('commission-log.index') }}" method="GET">
                    <div class="card-header row gutters-5">
                        <div class="col text-center text-md-left">
                            <h5 class="mb-md-0 h6">{{ translate('Commission Package History') }}</h5>
                        </div>
                        <div class="col-md-3 ml-auto">
                            <select id="demo-ease" class="form-control form-control-sm aiz-selectpicker mb-2 mb-md-0"
                                name="seller_id">
                                <option value="">{{ translate('Choose Seller') }}</option>
                                @foreach (App\Models\User::where('user_type', '=', 'seller')->get() as $key => $seller)
                                    <option value="{{ $seller->id }}">
                                        {{ $seller->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group mb-0">
                                <input type="text" class="form-control form-control-sm aiz-date-range" id="search"
                                    name="date_range"@isset($date_range) value="{{ $date_range }}" @endisset
                                    placeholder="{{ translate('Daterange') }}">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <button class="btn btn-md btn-primary" type="submit">
                                {{ translate('Filter') }}
                            </button>
                        </div>
                    </div>
                </form>
                <div class="card-body">
                    <table class="table aiz-table mb-0">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th data-breakpoints="lg">{{ translate('Seller') }}</th>
                                <th>{{ translate('Package') }}</th>
                                <th>{{ translate('Price') }}</th>
                                <th>{{ translate('Commission Percentage') }}</th>
                                <th>{{ translate('Start Date') }}</th>
                                <th>{{ translate('End Date') }}</th>

                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($histories as $key => $history)
                                <tr>
                                    <td>{{ $key + 1 }}</td>
                                    <td>{{ $history->user_name }}</td>
                                    <td>{{ $history->package_name }}</td>
                                    <td>{{ number_format($history->package_price) }} {{ currency_symbol() }}</td>
                                    <td>{{ $history->commission_percentage }} %</td>
                                    <td>{{ $history->start_date }}</td>
                                    <td>{{ $history->end_date }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div class="aiz-pagination mt-4">
                        {{ $histories->appends(request()->input())->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
