@extends('backend.layouts.app')

@section('content')
<div class="aiz-titlebar text-left mt-2 mb-3">
    <h5 class="mb-0 h6">{{translate('Add New Referral Code')}}</h5>
</div>

<div class="col-lg-8 mx-auto">
    <div class="card">
        <div class="card-body p-0">
            <form class="p-4" action="{{ route('referral-codes.store') }}" method="POST">
                @csrf
                <div class="form-group row">
                    <label class="col-sm-3 col-from-label" for="code">{{translate('Code')}} <span class="text-danger">*</span></label>
                    <div class="col-sm-9">
                        <input type="text" placeholder="{{translate('Code')}}" id="code" name="code" class="form-control" required>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-3 col-from-label" for="usage_limit">{{translate('Usage Limit')}}</label>
                    <div class="col-sm-9">
                        <input type="number" min="1" placeholder="{{translate('Leave empty for unlimited')}}" id="usage_limit" name="usage_limit" class="form-control">
                        <small class="text-muted">{{translate('Number of times this code can be used')}}</small>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-sm-3 col-from-label" for="description">{{translate('Description')}}</label>
                    <div class="col-sm-9">
                        <textarea class="form-control" name="description" rows="5" placeholder="{{translate('Description')}}"></textarea>
                    </div>
                </div>
                <div class="form-group mb-0 text-right">
                    <button type="submit" class="btn btn-primary">{{translate('Save')}}</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
