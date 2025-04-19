<form id="payment_form" class="form-horizontal" enctype="multipart/form-data" method="POST">
    @csrf
    <div class="modal-header">
        <h5 class="modal-title h6">{{ translate('Seller Withdraw Request') }}</h5>
        <button type="button" class="close" data-dismiss="modal">
        </button>
    </div>
    <div class="modal-body">
        <table class="table table-striped table-bordered">
            <tbody>
                <tr>
                    @if ($user->balance >= 0)
                        <td>{{ translate('Seller Balance') }}</td>
                        <td>{{ single_price($user->balance) }}</td>
                    @endif
                </tr>
                <tr>
                    @if ($seller_withdraw_request->amount > $user->balance)
                        <td>{{ translate('Requested Amount is ') }}</td>
                        <td>{{ single_price($seller_withdraw_request->amount) }}</td>
                    @endif
                </tr>
                @if ($user->shop->bank_payment_status == 1)
                    <tr>
                        <td>{{ translate('Bank Name') }}</td>
                        <td>{{ $user->shop->bank_name }}</td>
                    </tr>
                    <tr>
                        <td>{{ translate('Bank Account Name') }}</td>
                        <td>{{ $user->shop->bank_acc_name }}</td>
                    </tr>
                    <tr>
                        <td>{{ translate('Bank Account Number') }}</td>
                        <td>{{ $user->shop->bank_acc_no }}</td>
                    </tr>
                    <tr>
                        <td>{{ translate('Bank Routing Number') }}</td>
                        <td>{{ $user->shop->bank_routing_no }}</td>
                    </tr>
                @endif
            </tbody>
        </table>

        @if ($user->balance > 0)
            <input type="hidden" name="seller_withdraw_request_id" value="{{ $seller_withdraw_request->id }}">
            <div class="form-group row">
                <label class="col-sm-3 col-from-label" for="amount">{{ translate('Requested Amount') }}</label>
                <div class="col-sm-9">
                    <input type="number" lang="en" min="0" step="0.01" name="amount" id="amount"
                        value="{{ $seller_withdraw_request->amount }}" class="form-control" required readonly>
                </div>
            </div>

            <div class="form-group row">
                <label class="col-sm-3 col-from-label" for="reply">{{ translate('Reply') }}</label>
                <div class="col-sm-9">
                    <textarea id="reply" name="reply" rows="8" class="form-control mb-3"></textarea>
                </div>
            </div>
        @endif
    </div>
    <div class="modal-footer">
        @if ($user->balance > 0)
            <button id="approve_button" type="button" class="btn btn-primary">{{ translate('Approve') }}</button>
        @endif
        <button id="reject_button" type="button" class="btn btn-danger">{{ translate('Reject') }}</button>
        <button type="button" class="btn btn-light" data-dismiss="modal">{{ translate('Cancel') }}</button>
    </div>
</form>

<script>
    $(document).ready(function() {
        AIZ.plugins.bootstrapSelect('refresh');

        $('#approve_button').on('click', function() {
            $('#payment_form').attr('action', '{{ route('withdraw_request.approve') }}');
            $('#payment_form').submit();
        });

        $('#reject_button').on('click', function() {
            $('#payment_form').attr('action', '{{ route('withdraw_request.reject') }}');
            $('#payment_form').submit();
        });
    });
</script>
