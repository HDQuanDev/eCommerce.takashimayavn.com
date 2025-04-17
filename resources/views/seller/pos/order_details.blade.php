@extends('seller.layouts.app')

@section('panel_content')
    <div class="aiz-titlebar mt-2 mb-3">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h1 class="h3">{{ translate('Order Details') }}</h1>
            </div>
            <div class="col-md-6 text-md-right">
                <a href="{{ route('seller.pos.orders') }}" class="btn btn-circle btn-info">{{ translate('Back to Order List') }}</a>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h5 class="h6 mb-0">{{ translate('Order Summary') }}</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-lg-6">
                    <table class="table table-borderless">
                        <tr>
                            <td class="w-50 fw-600">{{ translate('Order Code') }}:</td>
                            <td>{{ $order->code }}</td>
                        </tr>
                        <tr>
                            <td class="w-50 fw-600">{{ translate('Date') }}:</td>
                            <td>{{ date('d-m-Y H:i A', strtotime($order->created_at)) }}</td>
                        </tr>
                        <tr>
                            <td class="w-50 fw-600">{{ translate('Order Status') }}:</td>
                            <td>
                                <span class="badge badge-inline badge-success">{{ translate('Completed') }}</span>
                            </td>
                        </tr>
                        <tr>
                            <td class="w-50 fw-600">{{ translate('Payment Status') }}:</td>
                            <td>
                                <span class="badge badge-inline badge-success">{{ translate('Paid') }}</span>
                            </td>
                        </tr>
                    </table>
                </div>
                <div class="col-lg-6">
                    <table class="table table-borderless">
                        <tr>
                            <td class="w-50 fw-600">{{ translate('Order Type') }}:</td>
                            <td>{{ translate('POS Purchase') }}</td>
                        </tr>
                        <tr>
                            <td class="w-50 fw-600">{{ translate('Payment Method') }}:</td>
                            <td>{{ translate('Seller Balance') }}</td>
                        </tr>
                        <tr>
                            <td class="w-50 fw-600">{{ translate('Payment Reference') }}:</td>
                            <td>{{ $order->code }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="row gutters-5">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="h6 mb-0">{{ translate('Order Details') }}</h5>
                </div>
                <div class="card-body">
                    <table class="table aiz-table">
                        <thead>
                            <tr>
                                <th data-breakpoints="md" width="5%">#</th>
                                <th width="40%">{{ translate('Product') }}</th>
                                <th data-breakpoints="md" width="15%">{{ translate('Unit Price') }}</th>
                                <th width="15%">{{ translate('Quantity') }}</th>
                                <th width="10%">{{ translate('Tax') }}</th>
                                <th class="text-right" width="15%">{{ translate('Total') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($orderDetails as $key => $orderDetail)
                                <tr>
                                    <td>{{ $key+1 }}</td>
                                    <td>{{ $orderDetail->product ? $orderDetail->product->name : translate('Product Unavailable') }}</td>
                                    <td>{{ format_price($orderDetail->price / $orderDetail->quantity) }}</td>
                                    <td>{{ $orderDetail->quantity }}</td>
                                    <td>{{ format_price($orderDetail->tax) }}</td>
                                    <td class="text-right">{{ format_price($orderDetail->price) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div class="row">
                        <div class="col-xl-5 col-md-6 ml-auto mr-0">
                            <table class="table">
                                <tbody>
                                    <tr>
                                        <td><strong class="text-muted">{{ translate('Sub Total') }} :</strong></td>
                                        <td>
                                            {{ format_price($orderDetails->sum('price')) }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong class="text-muted">{{ translate('Tax') }} :</strong></td>
                                        <td>{{ format_price($orderDetails->sum('tax')) }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong class="text-muted">{{ translate('Shipping') }} :</strong></td>
                                        <td>{{ format_price($order->shipping_cost) }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong class="text-muted">{{ translate('Discount') }} :</strong></td>
                                        <td>{{ format_price($order->coupon_discount) }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>{{ translate('Grand Total') }} :</strong></td>
                                        <td>
                                            <strong>{{ format_price($order->grand_total) }}</strong>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
