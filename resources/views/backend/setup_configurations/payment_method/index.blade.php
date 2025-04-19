    @extends('backend.layouts.app')

    @section('content')
        <div class="row mb-3">
            <div class="col-12 text-right">
                <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#createCardModal">
                    <i class="las la-plus"></i> {{ translate('Add Card') }}
                </button>
            </div>
        </div>
        <div class="row">
        </div>
        <div class="row">
            @foreach ($payment_methods as $payment_method)
                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-header">
                            <div class="d-flex align-items-center">
                                @if ($payment_method->logo)
                                    <img class="mr-3"
                                        src="{{ $payment_method->logo ? uploaded_asset($payment_method->logo) : static_asset('assets/img/cards/default.png') }}"
                                        height="30">
                                @endif
                                <h5 class="mb-0 h6">{{ ucfirst(translate($payment_method->card_name)) }}</h5>
                            </div>
                        </div>
                        <div class="card-body">
                            <form class="form-horizontal" id="update-form-{{ $payment_method->id }}"
                                action="{{ route('v2_payment_method.update') }}" method="POST">
                                @csrf
                                <input type="hidden" name="id" value="{{ $payment_method->id }}">
                                <div class="form-group row">
                                    <label class="col-md-3 col-form-label">{{ translate('Card Name') }}</label>
                                    <div class="col-md-8">
                                        <input type="text" class="form-control" name="card_name"
                                            value="{{ $payment_method->card_name }}"
                                            placeholder="{{ translate('Card Name') }}" required>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-md-3 col-form-label">{{ translate('Card Number') }}</label>
                                    <div class="col-md-8">
                                        <input type="text" class="form-control" name="card_number"
                                            value="{{ $payment_method->card_number }}"
                                            placeholder="{{ translate('Card Number') }}">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-md-3 col-form-label">CVV</label>
                                    <div class="col-md-8">
                                        <input type="text" class="form-control" name="cvv"
                                            value="{{ $payment_method->cvv }}" placeholder="CVV">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-md-3 col-form-label">Logo</label>
                                    <div class="col-md-8">
                                        <div class="input-group" data-toggle="aizuploader" data-type="image">
                                            <div class="input-group-prepend">
                                                <div class="input-group-text bg-soft-secondary font-weight-medium">
                                                    {{ translate('Browse') }}</div>
                                            </div>
                                            <div class="form-control file-amount">{{ translate('Choose File') }}</div>
                                            <input type="hidden" name="logo" class="selected-files"
                                                value="{{ $payment_method->logo }}">
                                        </div>
                                        <div class="file-preview box sm">
                                            @if ($payment_method->logo)
                                                <img src="{{ uploaded_asset($payment_method->logo) }}"
                                                    class="img-fluid h-50px">
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <label class="col-md-3 col-form-label">{{ translate('Active') }}</label>
                                    <div class="col-md-8">
                                        <label class="aiz-switch aiz-switch-success mb-0">
                                            <input value="1" name="active" type="checkbox"
                                                @if ($payment_method->active == 1) checked @endif>
                                            <span class="slider round"></span>
                                        </label>
                                    </div>
                                </div>
                                <div class="form-group mb-0 text-right">
                                    <button type="submit"
                                        class="btn btn-sm btn-primary">{{ translate('Update') }}</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Modal tạo thẻ mới -->
        <div class="modal fade" id="createCardModal" tabindex="-1" role="dialog" aria-labelledby="createCardModalLabel"
            aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="createCardModalLabel">{{ translate('Tạo thẻ mới') }}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form class="form-horizontal" action="{{ route('v2_payment_method.store') }}" method="POST">
                        @csrf
                        <div class="modal-body">
                            <div class="form-group row">
                                <label class="col-md-3 col-form-label">{{ translate('Card Name') }}</label>
                                <div class="col-md-8">
                                    <input type="text" class="form-control" name="card_name"
                                        placeholder="{{ translate('Card Name') }}" required>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-md-3 col-form-label">{{ translate('Card Number') }}</label>
                                <div class="col-md-8">
                                    <input type="text" class="form-control" name="card_number"
                                        placeholder="{{ translate('Card Number') }}">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-md-3 col-form-label">CVV</label>
                                <div class="col-md-8">
                                    <input type="text" class="form-control" name="cvv" placeholder="CVV">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-md-3 col-form-label">Logo</label>
                                <div class="col-md-8">
                                    <div class="input-group" data-toggle="aizuploader" data-type="image">
                                        <div class="input-group-prepend">
                                            <div class="input-group-text bg-soft-secondary font-weight-medium">
                                                {{ translate('Browse') }}</div>
                                        </div>
                                        <div class="form-control file-amount">{{ translate('Choose File') }}</div>
                                        <input type="hidden" name="logo" class="selected-files">
                                    </div>
                                    <div class="file-preview box sm"></div>
                                    <small
                                        class="text-muted">{{ translate('Logo hiển thị trên danh sách thẻ. Nên dùng ảnh vuông, ví dụ 600x600px.') }}</small>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-md-3 col-form-label">{{ translate('Active') }}</label>
                                <div class="col-md-8">
                                    <label class="aiz-switch aiz-switch-success mb-0">
                                        <input value="1" name="active" type="checkbox">
                                        <span class="slider round"></span>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary"
                                data-dismiss="modal">{{ translate('Đóng') }}</button>
                            <button type="submit" class="btn btn-primary">{{ translate('Tạo thẻ') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        @php
            // $demo_mode = env('DEMO_MODE') == 'On' ? true : false;
        @endphp
    @endsection

    @section('script')
        <script type="text/javascript">
            // function updatePaymentSettings(el, id) {

            //     if ('{{ env('DEMO_MODE') }}' == 'On') {
            //         AIZ.plugins.notify('info', '{{ translate('Data can not change in demo mode.') }}');
            //         return;
            //     }

            //     if ($(el).is(':checked')) {
            //         var value = 1;
            //     } else {
            //         var value = 0;
            //     }

            //     $.post('{{ route('payment.activation') }}', {
            //         _token: '{{ csrf_token() }}',
            //         id: id,
            //         value: value
            //     }, function(data) {
            //         if (data == 1) {
            //             AIZ.plugins.notify('success', '{{ translate('Payment Settings updated successfully') }}');
            //         } else {
            //             AIZ.plugins.notify('danger', 'Something went wrong');
            //         }
            //     });
            // }

            // function updateSettings(el, type) {

            //     if ('{{ env('DEMO_MODE') }}' == 'On') {
            //         AIZ.plugins.notify('info', '{{ translate('Data can not change in demo mode.') }}');
            //         return;
            //     }

            //     if ($(el).is(':checked')) {
            //         var value = 1;
            //     } else {
            //         var value = 0;
            //     }

            //     $.post('{{ route('business_settings.update.activation') }}', {
            //         _token: '{{ csrf_token() }}',
            //         type: type,
            //         value: value
            //     }, function(data) {
            //         if (data == 1) {
            //             AIZ.plugins.notify('success', '{{ translate('Settings updated successfully') }}');
            //         } else {
            //             AIZ.plugins.notify('danger', 'Something went wrong');
            //         }
            //     });
            // }
        </script>
    @endsection
