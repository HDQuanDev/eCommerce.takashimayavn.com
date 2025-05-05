@extends('seller.layouts.app')

@section('panel_content')
<div class="aiz-titlebar text-left mt-2 mb-3">
    <div class="row align-items-center">
        <div class="col-md-6">
            <h1 class="h3">{{ translate('Gói người bán') }}</h1>
        </div>
    </div>
</div>

<div class="row">
    @foreach ($seller_packages as $key => $seller_package)
        <div class="col-md-4">
            <div class="card">
                <div class="card-body text-center">
                    @if($seller_package->logo)
                        <img src="{{ uploaded_asset($seller_package->logo) }}" class="mw-100 mx-auto mb-4" height="100">
                    @else
                        <img src="{{ static_asset('assets/img/placeholder-rect.jpg') }}" class="mw-100 mx-auto mb-4" height="100">
                    @endif
                    <div class="mb-3 text-primary">
                        @if ($seller_package->amount == 0)
                            <span class="display-4 fw-600">{{ translate('Free') }}</span>
                        @else
                            <span class="display-4 fw-600">{{ format_price($seller_package->amount) }}</span>
                        @endif
                    </div>
                    <div class="h5 fw-600 mb-3">{{ $seller_package->name }}</div>
                    <ul class="list-group list-group-raw fs-15 mb-5">
                        <li class="list-group-item py-2">
                            <span>{{ translate('Giới hạn tải sản phẩm:') }} {{ $seller_package->product_upload_limit }}</span>
                        </li>
                        <li class="list-group-item py-2">
                            <span>{{ translate('Thời hạn:') }} {{ $seller_package->duration }} {{ translate('ngày') }}</span>
                        </li>
                    </ul>
                    <div class="mb-3">
                        @php

                            $shop = Auth::user()->shop;
                            $seller_package = $shop->seller_package;
                        @endphp
                        @if ($shop && $shop->seller_package_id == $seller_package->id)
                            <button class="btn btn-success" disabled>{{ translate('Đang sử dụng') }}</button>
                            @if($shop->package_invalid_at)
                                <div class="mt-2">
                                    <span class="text-muted">{{ translate('Hết hạn:') }} {{ date('d/m/Y', strtotime($shop->package_invalid_at)) }}</span>
                                </div>
                            @endif
                        @else
                            <button type="button" onclick="show_price_modal('{{ $seller_package->id }}', '{{ $seller_package->amount }}')" class="btn btn-primary">{{ translate('Mua ngay') }}</button>
                            @php
                                $user = Auth::user();
                                $admin_to_pay = $user ? $user->balance : 1;
                            @endphp
                            @if($seller_package->amount > $admin_to_pay)
                                <div class="mt-2">
                                    <span class="text-danger">{{ translate('Số dư không đủ') }}</span>
                                </div>
                            @endif
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endforeach
</div>
@if($seller_package)
<div class="card my-2">
    <div class="card-header">
        <h5 class="mb-0 h6">{{ translate('Current Package') }}</h5>
    </div>
    <div class="card-body">
        <h5 class="card-title">{{ $seller_package->name }}</h5>
        <p class="card-text">{{ translate('Giới hạn tải sản phẩm: ') }} {{ $seller_package->product_upload_limit }}</p>
        <p class="card-text">{{ translate('Thời hạn: ') }} {{ $seller_package->duration }} {{ translate('ngày') }}</p>
    </div>
</div>
@endif
{{-- <div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">{{ translate('Số dư hiện tại') }}</h5>
            </div>
            <div class="card-body">
                @php
                    $shop = Auth::user()->shop;
                    $admin_to_pay = $shop ? $shop->admin_to_pay : 0;
                @endphp
                <h3 class="text-primary">{{ format_price($admin_to_pay) }}</h3>
                <p>{{ translate('Bạn có thể sử dụng số dư này để mua gói người bán. Số dư này được cộng dồn từ doanh thu bán hàng của bạn.') }}</p>
            </div>
        </div>
    </div>
</div> --}}

<!-- Purchase History Section -->
<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">{{ translate('Lịch sử mua gói') }}</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table aiz-table mb-0">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>{{ translate('Tên gói') }}</th>
                                <th>{{ translate('Số tiền') }}</th>
                                <th>{{ translate('Phương thức thanh toán') }}</th>
                                <th>{{ translate('Ngày mua') }}</th>
                                <th>{{ translate('Trạng thái') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(isset($package_history) && !$package_history->isEmpty())
                                @foreach($package_history as $key => $payment)
                                    <tr>
                                        <td>{{ $key+1 + ($package_history->currentPage() - 1)*$package_history->perPage() }}</td>
                                        <td>{{ $payment?->seller_package->name ?? '-' }}</td>
                                        <td>{{ format_price($payment?->seller_package->amount ?? 0) }}</td>
                                        <td>{{ ucfirst($payment?->payment_method ?? '-') }}</td>
                                        <td>{{ date('d/m/Y', strtotime($payment?->created_at ?? '-')) }}</td>
                                        <td>
                                            @if($payment?->approval == 1)
                                                <span class="badge badge-inline badge-success">{{ translate('Đã duyệt') }}</span>
                                            @else
                                                <span class="badge badge-inline badge-warning">{{ translate('Đang xử lý') }}</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td class="text-center" colspan="6">{{ translate('Không có dữ liệu') }}</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
                <div class="aiz-pagination mt-4">
                    @if(isset($package_history))
                        {{ $package_history->links() }}
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('modal')
    <div class="modal fade" id="price_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">{{ translate('Xác nhận mua gói') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="text-center">
                        <p>{{ translate('Số tiền thanh toán sẽ được trừ từ số dư của bạn.') }}</p>
                        <p>{{ translate('Bạn có chắc chắn muốn mua gói này không?') }}</p>
                    </div>
                </div>
                <div class="modal-footer">
                    <form class="form-horizontal" id="package_payment_form" action="{{ route('seller.purchase_package') }}" method="POST">
                        @csrf
                        <input type="hidden" name="seller_package_id" id="package_id" value="">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ translate('Hủy') }}</button>
                        <button type="submit" class="btn btn-primary">{{ translate('Xác nhận') }}</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script type="text/javascript">
        function show_price_modal(id, amount){
            if(amount > 0){
                $('#package_id').val(id);
                $('#price_modal').modal('show');
            }else{
                $('#package_id').val(id);
                $('#package_payment_form').submit();
            }
        }
    </script>
@endsection
