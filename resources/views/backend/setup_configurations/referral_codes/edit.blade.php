@extends('backend.layouts.app')

@section('content')
<div class="aiz-titlebar text-left mt-2 mb-3">
    <h5 class="mb-0 h6">{{translate('Edit Referral Code')}}</h5>
</div>

<div class="col-lg-8 mx-auto">
    <div class="card">
        <div class="card-body p-0">
            <form class="p-4" action="{{ route('referral-codes.update', $referral_code->id) }}" method="POST">
                @csrf
                <input type="hidden" name="_method" value="PATCH">
                <div class="form-group row">
                    <label class="col-sm-3 col-from-label" for="code">{{translate('Code')}} <span class="text-danger">*</span></label>
                    <div class="col-sm-9">
                        <input type="text" placeholder="{{translate('Code')}}" id="code" name="code" value="{{ $referral_code->code }}" class="form-control" required>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-3 col-from-label" for="usage_limit">{{translate('Usage Limit')}}</label>
                    <div class="col-sm-9">
                        <input type="number" min="1" placeholder="{{translate('Leave empty for unlimited')}}" id="usage_limit" name="usage_limit" value="{{ $referral_code->usage_limit }}" class="form-control">
                        <small class="text-muted">{{translate('Number of times this code can be used')}}</small>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-3 col-from-label" for="description">{{translate('Description')}}</label>
                    <div class="col-sm-9">
                        <textarea class="form-control" name="description" rows="5" placeholder="{{translate('Description')}}">{{ $referral_code->description }}</textarea>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-3 col-from-label" for="is_active">{{translate('Status')}}</label>
                    <div class="col-sm-9">
                        <label class="aiz-switch aiz-switch-success mb-0">
                            <input type="checkbox" name="is_active" id="is_active" {{ $referral_code->is_active ? 'checked' : '' }}>
                            <span class="slider round"></span>
                        </label>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-3 col-from-label" for="used_count">{{translate('Used Count')}}</label>
                    <div class="col-sm-9">
                        <input type="text" class="form-control" value="{{ $referral_code->used_count }}" disabled>
                        <small class="text-muted">{{translate('Number of times this code has been used')}}</small>
                    </div>
                </div>
                <div class="form-group mb-0 text-right">
                    <button type="submit" class="btn btn-primary">{{translate('Update')}}</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection