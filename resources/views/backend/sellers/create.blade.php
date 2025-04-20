@extends('backend.layouts.app')

@section('content')
@if (env('MAIL_USERNAME') == null && env('MAIL_PASSWORD') == null)
    <div class="alert alert-info d-flex align-items-center">
        {{ translate('You need to configure SMTP correctly to to add Seller.') }}
        <a class="alert-link ml-2" href="{{ route('smtp_settings.index') }}">{{ translate('Configure Now') }}</a>
    </div>
@endif

<div class="aiz-titlebar text-left mt-2 mb-3">
    <h5 class="mb-0 h6">{{translate('Add New Seller')}}</h5>
</div>

<div class="row">
    <div class="col-lg-8 mr-auto">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0 h6">{{translate('Seller Information')}}</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('sellers.store') }}" method="POST">
                    @csrf
                    <div class="form-group row">
                        <label class="col-sm-3 col-from-label" for="name">{{translate('Name')}}</label>
                        <div class="col-sm-9">
                            <input type="text" placeholder="{{translate('Name')}}" id="name" name="name" class="form-control" required>
                            @error('name')
                            <small class="form-text text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-3 col-from-label" for="email">{{translate('Email Address')}}</label>
                        <div class="col-sm-9">
                            <input type="text" placeholder="{{translate('Email Address')}}" id="email" name="email" class="form-control" required>
                            @error('email')
                            <small class="form-text text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-3 col-from-label" for="shop_name">{{translate('Shop Name')}}</label>
                        <div class="col-sm-9">
                            <input type="text" placeholder="{{translate('Shop Name')}}" id="shop_name" name="shop_name" class="form-control" required>
                            @error('shop_name')
                            <small class="form-text text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-3 col-from-label" for="address">{{translate('Address')}}</label>
                        <div class="col-sm-9">
                            <textarea class="form-control" rows="4" placeholder="{{ translate('Address') }}" name="address"></textarea>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-sm-3 col-from-label" for="referral_code">{{translate('Referral Code')}}</label>
                        <div class="col-sm-9">
                            <select name="referral_code" id="referral_code" class="form-control aiz-selectpicker" required data-placeholder="{{ translate('Select a referral code') }}">
                                <option value="">{{ translate('Select a referral code') }}</option>
                                @foreach(\App\Models\ReferralCode::where('is_active', 1)->get() as $referral)
                                    <option value="{{ $referral->code }}">{{ $referral->code }}
                                        @if($referral->usage_limit)
                                            ({{ translate('Used') }}: {{ $referral->used_count }}/{{ $referral->usage_limit }})
                                        @else
                                            ({{ translate('Used') }}: {{ $referral->used_count }} - {{ translate('Unlimited') }})
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                            @error('referral_code')
                            <small class="form-text text-danger">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>

                    <div class="form-group mb-0 text-right">
                        <button type="submit" class="btn btn-primary">{{translate('Save')}}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection
