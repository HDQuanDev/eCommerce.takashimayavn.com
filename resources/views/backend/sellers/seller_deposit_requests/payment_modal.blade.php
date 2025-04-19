<form class="form-horizontal" id="payment_form" method="POST"
    enctype="multipart/form-data">
    @csrf
    <div class="modal-header">
        <h5 class="modal-title h6">{{ translate('Seller Deposit Request') }}</h5>
        <button type="button" class="close" data-dismiss="modal">
        </button>
    </div>
    <div class="modal-body">
        <table class="table table-striped table-bordered">
            <tbody>
                <tr>
                    <td>{{ translate('Requested Amount is ') }}</td>
                    <td>{{ single_price($seller_deposit_request->amount) }}</td>
                </tr>
            </tbody>
        </table>

        <input type="hidden" name="seller_deposit_request_id" value="{{ $seller_deposit_request->id }}">
        <div class="form-group row">
            <label class="col-sm-3 col-from-label" for="amount">{{ translate('Requested Amount') }}</label>
            <div class="col-sm-9">
                <input type="number" lang="en" min="0" step="0.01" name="amount" id="amount"
                    value="{{ $seller_deposit_request->amount }}" class="form-control" required readonly>
            </div>
        </div>

        <div class="form-group row">
            <label class="col-sm-3 col-from-label" for="reply">{{ translate('Reply') }}</label>
            <div class="col-sm-9">
                <textarea id="reply" name="reply" rows="8" class="form-control mb-3"></textarea>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button id="approve_button" type="button" class="btn btn-primary">{{ translate('Approve') }}</button>
        <button id="reject_button" type="button" class="btn btn-danger">{{ translate('Reject') }}</button>
        <button type="button" class="btn btn-light" data-dismiss="modal">{{ translate('Cancel') }}</button>
    </div>
</form>

<script>
    $(document).ready(function() {
        AIZ.plugins.bootstrapSelect('refresh');

        $('#approve_button').on('click', function() {
            $('#payment_form').attr('action', '{{ route('deposit_request.approve') }}');
            $('#payment_form').submit();
        });

        $('#reject_button').on('click', function() {
            $('#payment_form').attr('action', '{{ route('deposit_request.reject') }}');
            $('#payment_form').submit();
        });
    });
</script>
