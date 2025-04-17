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
                        @endphp
                        @if ($shop && $shop->seller_package_id == $seller_package->id)
                            <button class="btn btn-success" disabled>{{ translate('Đang sử dụng') }}</button>
                            @if($shop->package_invalid_at)
                                <div class="mt-2">
                                    <span class="text-muted">{{ translate('Hết hạn:') }} {{ date('d/m/Y', strtotime($shop->package_invalid_at)) }}</span>
                                </div>
                            @endif
                        @else
                            <button type="button" onclick="show_price_modal('{{ $seller_package->id }}')" class="btn btn-primary">{{ translate('Mua ngay') }}</button>
                            @php
                                $shop = Auth::user()->shop;
                                $admin_to_pay = $shop ? $shop->admin_to_pay : 0;
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

<div class="row mt-4">
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
                    <form class="form-horizontal" action="{{ route('seller.purchase_package') }}" method="POST">
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
        function show_price_modal(id){
            $('#package_id').val(id);
            $('#price_modal').modal('show');
        }
    </script>
@endsection
