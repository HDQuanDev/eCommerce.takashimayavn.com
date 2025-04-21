<form action="{{ route('sellers.change_balance') }}" method="POST">
    @csrf
    <input type="hidden" name="shop_id" value="{{ $shop->id }}">
    <div class="modal-header">
        <h5 class="modal-title h6">{{ translate('Change Seller Balance') }}</h5>
        <button type="button" class="close" data-dismiss="modal">
        </button>
    </div>
    <div class="modal-body">
        <table class="table table-striped table-bordered">
            <tbody>
                <tr>
                    <td>{{ translate('Balance') }}</td>
                    <td>{{ single_price($shop->user->balance) }}</td>
                </tr>
            </tbody>
        </table>

        <div class="form-group row">
            <label class="col-md-3 col-from-label" for="amount">{{ translate('Amount') }}</label>
            <div class="col-md-9">
                <input type="number" lang="en" min="0" step="0.01" name="amount" id="amount"
                    class="form-control" required>
            </div>
        </div>

        <div class="form-group row">
            <label class="col-md-3 col-from-label" for="payment_option">{{ translate('Action') }}</label>
            <div class="col-md-9">
                <select name="payment_option" id="payment_option" class="form-control aiz-selectpicker" required>
                    <option value="">{{ translate('Select Action') }}</option>
                    <option value="add">{{ translate('Add') }}</option>
                    <option value="subtract">{{ translate('Subtract') }}</option>
                </select>
            </div>
        </div>

    </div>
    <div class="modal-footer">

        <button type="submit" class="btn btn-primary">{{ translate('Save') }}</button>
        <button type="button" class="btn btn-light" data-dismiss="modal">{{ translate('Cancel') }}</button>
    </div>
</form>

<script></script>
