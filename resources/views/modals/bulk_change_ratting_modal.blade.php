<div id="bulk-change-ratting" class="modal fade">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title h6">{{ translate('Change Ratting Confirmation') }}</h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
            </div>
            <div class="modal-body text-center">
                <div class="form-group">
                    <label for="rating">{{ translate('Rating') }}</label>
                    <input type="number" class="form-control" id="rating" name="rating" step="0.1" min="1" max="5" required>
                </div>
                <button type="button" class="btn btn-link mt-2" data-dismiss="modal">{{ translate('Cancel') }}</button>
                <a href="javascript:void(0)" onclick="bulk_change_ratting()" class="btn btn-primary mt-2">{{ translate('Change') }}</a>
            </div>
        </div>
    </div>
</div>
