@extends('backend.layouts.app')

@section('content')

@php
    CoreComponentRepository::instantiateShopRepository();
    CoreComponentRepository::initializeCache();

    $lang = request()->query('lang') ?? 'en';
@endphp

<div class="aiz-titlebar text-left mt-2 mb-3">
    <h5 class="mb-0 h6">{{translate('Update Package Information')}}</h5>
</div>

<div class="row">
    <div class="col-lg-8 mx-auto">
        <div class="card">
            <div class="card-body p-0">
                {{-- <ul class="nav nav-tabs nav-fill border-light">
                    @foreach (\App\Models\Language::all() as $key => $language)
                    <li class="nav-item">
                        <a class="nav-link text-reset @if ($language->code == $lang) active @else bg-soft-dark border-light border-left-0 @endif py-3" href="{{ route('seller_packages.edit', ['id'=>$seller_package->id, 'lang'=> $language->code] ) }}">
                            <img src="{{ static_asset('assets/img/flags/'.$language->code.'.png') }}" height="11" class="mr-1">
                            <span>{{ $language->name }}</span>
                        </a>
                    </li>
                    @endforeach
                </ul> --}}
                <form class="form-horizontal" action="{{ route('seller_packages.update', $seller_package->id) }}" method="POST" enctype="multipart/form-data">
                    <input name="_method" type="hidden" value="PATCH">
                    <input type="hidden" name="lang" value="{{ $lang }}">
                    @csrf
                    <div class="card-body">
                        <div class="form-group row">
                            <label class="col-sm-3 col-from-label" for="name">{{translate('Package Name')}}</label>
                            <div class="col-sm-9">
                                <input type="text" placeholder="{{translate('Name')}}" id="name" name="name" value="{{ $seller_package->getTranslation('name', $lang) }}" class="form-control" required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-3 col-from-label" for="amount">{{translate('Amount')}}</label>
                            <div class="col-sm-9">
                                <input type="number" min="0" step="0.01" placeholder="{{translate('Amount')}}" id="amount" name="amount" value="{{ $seller_package->amount }}" class="form-control" required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-3 col-from-label" for="product_upload_limit">{{translate('Product Upload Limit')}}</label>
                            <div class="col-sm-9">
                                <input type="number" min="0" step="1" placeholder="{{translate('Product Upload Limit')}}" id="product_upload_limit" name="product_upload_limit" value="{{ $seller_package->product_upload_limit }}" class="form-control" required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-3 col-from-label" for="preorder_product_upload_limit">{{translate('Preorder Product Upload Limit')}}</label>
                            <div class="col-sm-9">
                                <input type="number" min="0" step="1" placeholder="{{translate('Preorder Product Upload Limit')}}" id="preorder_product_upload_limit" name="preorder_product_upload_limit" value="{{ $seller_package->preorder_product_upload_limit ?? 0 }}" class="form-control" required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-3 col-from-label" for="duration">{{translate('Duration')}}</label>
                            <div class="col-sm-9">
                                <input type="number" min="0" step="1" placeholder="{{translate('Validity in number of days')}}" id="duration" name="duration" value="{{ $seller_package->duration }}" class="form-control" required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-3 col-form-label" for="logo">{{translate('Package Logo')}}</label>
                            <div class="col-md-9">
                                <div class="input-group" data-toggle="aizuploader" data-type="image">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text bg-soft-secondary font-weight-medium">{{ translate('Browse')}}</div>
                                    </div>
                                    <div class="form-control file-amount">{{ translate('Choose File') }}</div>
                                    <input type="hidden" name="logo" value="{{ $seller_package->logo }}" class="selected-files">
                                </div>
                                <div class="file-preview box sm">
                                </div>
                            </div>
                        </div>
                        <div class="form-group mb-0 text-right">
                            <button type="submit" class="btn btn-primary">{{translate('Save')}}</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection
