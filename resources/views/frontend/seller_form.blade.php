@extends('frontend.layouts.app')

@section('content')
    <section class="py-5">
        <div class="container">
            <div class="d-flex align-items-start py-3">
                <div class="aiz-titlebar text-left">
                    <h1 class="h3">{{ translate('Register your shop')}}</h1>
                </div>
            </div>
            <form id="shop" class="" action="{{ route('shops.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="card p-4">
                    <div class="row">
                        <div class="col-12">
                            <div class="form-group">
                                <div class="input-group input-group--style-1">
                                    <input type="text" class="form-control@error('referral_code') is-invalid @enderror" value="{{ old('referral_code') }}" placeholder="{{ translate('Referral Code') }}" name="referral_code" required>
                                    @error('referral_code')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                                <small class="form-text text-muted">{{ translate('Enter a valid referral code provided to you.') }}</small>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-group mt-3">
                    <button type="submit" class="btn btn-primary btn-sm w-100">{{ translate('Register Your Shop')}}</button>
                </div>
            </form>
        </div>
    </section>

@endsection