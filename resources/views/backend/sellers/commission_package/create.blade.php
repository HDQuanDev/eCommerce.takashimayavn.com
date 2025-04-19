<div class="modal-header">
    <h5 class="modal-title" id="createPackageModalLabel">
        {{ isset($package) ? translate('Edit Package') : translate('Create New Package') }}
    </h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>
<div class="modal-body">
    <form id="packageForm" enctype="multipart/form-data">
        @csrf
        <div class="mb-3">
            <label for="name" class="form-label">{{ translate('Package Name') }}</label>
            <input type="text" class="form-control @error('name') is-invalid @enderror"
                   id="name" name="name"
                   value="{{ isset($package) ? $package->name : old('name') }}" required>
            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
        <div class="mb-3">
            <label for="price" class="form-label">{{ translate('Price') }}</label>
            <input type="number" step="0.01" class="form-control @error('price') is-invalid @enderror"
                   id="price" name="price"
                   value="{{ isset($package) ? $package->price : old('price') }}" required>
            @error('price') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
        <div class="mb-3">
            <label for="duration" class="form-label">{{ translate('Duration (days)') }}</label>
            <input type="number" class="form-control @error('duration') is-invalid @enderror"
                   id="duration" name="duration"
                   value="{{ isset($package) ? $package->duration : old('duration') }}" required>
            @error('duration') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
        <div class="mb-3">
            <label for="commission_percentage" class="form-label">{{ translate('Commission Percentage (%)') }}</label>
            <input type="number" class="form-control @error('commission_percentage') is-invalid @enderror"
                   id="commission_percentage" name="commission_percentage"
                   value="{{ isset($package) ? $package->commission_percentage : old('commission_percentage') }}" required>
            @error('commission_percentage') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
        <div class="mb-3">
            <label for="description" class="form-label">{{ translate('Description') }}</label>
            <textarea class="form-control @error('description') is-invalid @enderror"
                      id="description" name="description" rows="3">{{ isset($package) ? $package->description : old('description') }}</textarea>
            @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
        <div class="mb-3">
            <label class="form-label" for="thumbnail_img">{{translate('Thumbnail Image')}}</label>
            <div>
                <div class="input-group" data-toggle="aizuploader" data-type="image">
                    <div class="input-group-prepend">
                        <div class="input-group-text bg-soft-secondary font-weight-medium">{{ translate('Browse')}}</div>
                    </div>
                    <div class="form-control file-amount">{{ translate('Choose File') }}</div>
                    <input type="hidden" name="thumbnail_img" class="selected-files"
                        value="{{ isset($package) ? $package->thumbnail_img : '' }}">
                </div>
                <div class="file-preview box sm"></div>
            </div>
        </div>
        <div class="mb-3">
            <label for="status" class="form-label">{{ translate('Status') }}</label>
            <select class="form-control @error('status') is-invalid @enderror" id="status" name="status" required>
                <option value="active" {{ (isset($package) && $package->status == 'active') ? 'selected' : (old('status') == 'active' ? 'selected' : '') }}>
                    {{ translate('Active') }}
                </option>
                <option value="inactive" {{ (isset($package) && $package->status == 'inactive') ? 'selected' : (old('status') == 'inactive' ? 'selected' : '') }}>
                    {{ translate('Inactive') }}
                </option>
            </select>
            @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
        <div class="modal-footer">
            <button type="submit" class="btn btn-primary">
                {{ isset($package) ? translate('Save Changes') : translate('Create Package') }}
            </button>
        </div>
    </form>
</div>
<script>
    $('#packageForm').on('submit', function(e) {
        e.preventDefault();
        let formData = new FormData(this);
        formData.append('_token', '{{ csrf_token() }}');
        let url = "{{ route('commission-packages.store') }}";
        let type = "POST";
        @if(isset($package))
            url = "{{ route('commission-packages.update', isset($package) ? $package->id : 0) }}";
            type = "POST";
        @endif
        $.ajax({
            url: url,
            type: type,
            data: formData,
            cache: false,
            contentType: false,
            processData: false,
            success: function(response) {
                $('#createPackageModal').modal('hide');
                $('#editPackageModal').modal('hide');
                location.reload();
            },
            error: function (xhr) {
                if(xhr.responseJSON && xhr.responseJSON.message) {
                    if(Array.isArray(xhr.responseJSON.message)){
                        xhr.responseJSON.message.forEach((message, index) => {
                            setTimeout(() => {
                                AIZ.plugins.notify('danger', message);
                            }, 500 * index);
                        });
                    } else {
                        AIZ.plugins.notify('danger', xhr.responseJSON.message);
                    }
                } else {
                    AIZ.plugins.notify('danger', '{{ translate('Something went wrong') }}');
                }
            }
        });
    });
</script>