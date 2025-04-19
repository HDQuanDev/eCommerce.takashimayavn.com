@extends('seller.layouts.app')

@section('panel_content')
    <div class="aiz-titlebar mt-2 mb-4">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h1 class="h3">{{ translate('Money Deposit') }}</h1>
            </div>
        </div>
    </div>

    <div class="row gutters-10">
        <div class="col-md-4 mb-3 ml-auto">
            <div class="bg-grad-1 text-white rounded-lg overflow-hidden">
                <span
                    class="size-30px rounded-circle mx-auto bg-soft-primary d-flex align-items-center justify-content-center mt-3">
                    <i class="las la-dollar-sign la-2x text-white"></i>
                </span>
                <div class="px-3 pt-3 pb-3">
                    <div class="h4 fw-700 text-center">{{ single_price($total_deposit_amount) }}</div>
                    <div class="opacity-50 text-center">{{ translate('Pending Balance') }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3 ml-auto">
            <div class="bg-grad-3 text-white rounded-lg overflow-hidden">
                <span
                    class="size-30px rounded-circle mx-auto bg-soft-primary d-flex align-items-center justify-content-center mt-3">
                    <i class="las la-dollar-sign la-2x text-white"></i>
                </span>
                <div class="px-3 pt-3 pb-3">
                    <div class="h4 fw-700 text-center">{{ single_price(Auth::user()->shop->admin_to_pay) }}</div>
                    <div class="opacity-50 text-center">{{ translate('Available Balance') }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3 mr-auto">
            <div class="p-3 rounded mb-3 c-pointer text-center bg-white shadow-sm hov-shadow-lg has-transition"
                onclick="show_request_modal()">
                <span
                    class="size-60px rounded-circle mx-auto bg-secondary d-flex align-items-center justify-content-center mb-3">
                    <i class="las la-plus la-3x text-white"></i>
                </span>
                <div class="fs-18 text-primary">{{ translate('Send Deposit Request') }}</div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h5 class="mb-0 h6">{{ translate('Deposit Request history') }}</h5>
        </div>
        <div class="card-body">
            <table class="table aiz-table mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>{{ translate('Date') }}</th>
                        <th>{{ translate('Amount') }}</th>
                        <th data-breakpoints="lg">{{ translate('Payment Method') }}</th>
                        <th data-breakpoints="lg">{{ translate('Status') }}</th>
                        <th data-breakpoints="lg" width="40%">{{ translate('Message') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($seller_deposit_requests as $key => $seller_deposit_request)
                        <tr>
                            <td>{{ $key + 1 }}</td>
                            <td>{{ date('d-m-Y', strtotime($seller_deposit_request->created_at)) }}</td>
                            <td>{{ single_price($seller_deposit_request->amount) }}</td>
                            <td>{{ $seller_deposit_request->payment_method->card_name }}</td>
                            <td>
                                @if ($seller_deposit_request->status == 1)
                                    <span class=" badge badge-inline badge-success">{{ translate('Approved') }}</span>
                                @elseif($seller_deposit_request->status == 2)
                                    <span class="badge badge-inline badge-danger">{{ translate('Rejected') }}</span>
                                @else
                                    <span class=" badge badge-inline badge-info">{{ translate('Pending') }}</span>
                                @endif
                            </td>
                            <td>
                                {{ $seller_deposit_request->message }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="aiz-pagination">
                {{ $seller_deposit_requests->links() }}
            </div>
        </div>
    </div>
@endsection

@section('modal')
    <div class="modal fade" id="request_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">{{ translate('Send A Deposit Request') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="deposit-form" class="" action="{{ route('seller.money_deposit_request.store') }}"
                    method="post">
                    @csrf
                    <div class="modal-body gry-bg px-3 pt-3">
                        <div class="">
                            @if ($payment_methods->count() > 1)
                                <label>{{ translate('Payment Method') }} <span class="text-danger">*</span></label>
                                <div class="btn-group btn-group-toggle w-100" data-toggle="buttons">
                                    <div class="row w-100">
                                        @foreach ($payment_methods as $payment_method)
                                            <div class="col-md-6 mb-2 d-flex justify-content-center">
                                                <label
                                                    class="btn btn-outline-primary w-100 text-center py-3 payment-method-label"
                                                    data-toggle="button" style="position:relative;">
                                                    <input type="radio" name="payment_method_id"
                                                        value="{{ $payment_method->id }}" autocomplete="off" required
                                                        style="position:absolute;left:0;opacity:0;width:100%;height:100%;z-index:2;cursor:pointer;">
                                                    <img src="{{ uploaded_asset($payment_method->logo) }}" height="40"
                                                        class="mb-2">
                                                    {{-- <span class="font-weight-bold">{{ $payment_method->card_name }}</span> --}}
                                                </label>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @else
                                <div class="p-5 heading-3">
                                    {{ translate('No Payment Method Available') }}
                                </div>
                            @endif
                        </div>
                        <div class="row">
                            <div class="col-md-3">
                                <label>{{ translate('Amount') }} <span class="text-danger">*</span></label>
                            </div>
                            <div class="col-md-9">
                                <input type="number" lang="en" class="form-control mb-3" name="amount"
                                    min="{{ get_setting('minimum_seller_amount_deposit') }}" placeholder="{{ translate('Amount') }}"
                                    required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3">
                                <label>{{ translate('Deposit Code') }}</label>
                            </div>
                            <div class="col-md-9">
                                <input type="text" name="message" class="form-control mb-3">
                            </div>
                        </div>
                        <div class="form-group text-right">
                            <button type="submit" id="deposit-submit"
                                class="btn btn-sm btn-primary">{{ translate('Send') }}</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script type="text/javascript">
        document.getElementById('deposit-form').addEventListener('submit', function() {
            document.getElementById('deposit-submit').disabled = true;
        });

        function show_request_modal() {
            $('#request_modal').modal('show');
        }

        $(document).on('change', 'input[name="payment_method_id"]', function() {
            $('.payment-method-label').removeClass('active');
            if ($(this).is(':checked')) {
                $(this).closest('label').addClass('active');
            }
        });
    </script>
    <style>
        .payment-method-label input[type="radio"] {
            position: absolute;
            left: 0;
            top: 0;
            opacity: 0;
            width: 100%;
            height: 100%;
            z-index: 2;
            cursor: pointer;
        }
    </style>
@endsection
