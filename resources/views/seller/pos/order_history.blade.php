@extends('seller.layouts.app')

@section('panel_content')
    <div class="aiz-titlebar mt-2 mb-3">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h1 class="h3">{{ translate('POS Orders') }}</h1>
            </div>
            <div class="col-md-6 text-md-right">
                <a href="{{ route('seller.pos.index') }}" class="btn btn-circle btn-info">{{ translate('Back to POS') }}</a>
            </div>
        </div>
    </div>

    <div class="card">
        <form class="" id="sort_orders" action="" method="GET">
            <div class="card-header row gutters-5">
                <div class="col">
                    <h5 class="mb-md-0 h6">{{ translate('All Orders') }}</h5>
                </div>
                <div class="col-lg-2 ml-auto">
                    <select class="form-control aiz-selectpicker" name="sort" id="sort_select" data-live-search="true" data-selected="{{ $sort ?? '' }}">
                        <option value="newest" @if ($sort == 'newest') selected @endif>{{ translate('Newest') }}</option>
                        <option value="oldest" @if ($sort == 'oldest') selected @endif>{{ translate('Oldest') }}</option>
                        <option value="highest_amount" @if ($sort == 'highest_amount') selected @endif>{{ translate('Highest Amount') }}</option>
                        <option value="lowest_amount" @if ($sort == 'lowest_amount') selected @endif>{{ translate('Lowest Amount') }}</option>
                    </select>
                </div>
                <div class="col-lg-2">
                    <div class="form-group mb-0">
                        <input type="text" class="form-control form-control-sm" id="search" name="search" @isset($sort_search) value="{{ $sort_search }}" @endisset placeholder="{{ translate('Type Order code & hit Enter') }}">
                    </div>
                </div>
            </div>
        </form>

        <div class="card-body">
            <table class="table aiz-table mb-0">
                <thead>
                    <tr>
                        <th data-breakpoints="lg">#</th>
                        <th>{{ translate('Order Code') }}</th>
                        <th data-breakpoints="lg">{{ translate('Num. of Products') }}</th>
                        <th>{{ translate('Amount') }}</th>
                        <th data-breakpoints="lg">{{ translate('Shipping') }}</th>
                        <th data-breakpoints="lg">{{ translate('Discount') }}</th>
                        <th>{{ translate('Date') }}</th>
                        <th class="text-right">{{ translate('Options') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($orders as $key => $order)
                        <tr>
                            <td>
                                {{ ($key+1) + ($orders->currentPage() - 1)*$orders->perPage() }}
                            </td>
                            <td>
                                {{ $order->code }}
                            </td>
                            <td>
                                {{ count($order->orderDetails) }}
                            </td>
                            <td>
                                {{ format_price($order->grand_total) }}
                            </td>
                            <td>
                                {{ format_price($order->shipping_cost) }}
                            </td>
                            <td>
                                {{ format_price($order->coupon_discount) }}
                            </td>
                            <td>
                                {{ date('d-m-Y h:i A', strtotime($order->created_at)) }}
                            </td>
                            <td class="text-right">
                                <a href="{{ route('seller.pos.orderDetails', $order->code) }}" class="btn btn-soft-info btn-icon btn-circle btn-sm">
                                    <i class="las la-eye"></i>
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="aiz-pagination">
                {{ $orders->appends(request()->input())->links() }}
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script type="text/javascript">
        $('#sort_select').on('change', function() {
            $('#sort_orders').submit();
        });
    </script>
@endsection
