@extends('backend.layouts.app')

@section('content')

<div class="aiz-titlebar text-left mt-2 mb-3">
    @can('add_digital_product')
		<div class="text-md-right">
			<a href="{{ route('digitalproducts.create') }}" class="btn btn-circle btn-info">
				<span>{{translate('Add New Digital Product')}}</span>
			</a>
		</div>
    @endcan
</div>


<div class="card">
    <div class="card-header row gutters-5">
        <div class="col text-center text-md-left">
            <h5 class="mb-md-0 h6">{{ translate('Digital Products') }}</h5>
        </div>
        <div class="col-md-4">
            <form class="" id="sort_digital_products" action="" method="GET">
                <div class="input-group input-group-sm">
                    <input type="text" class="form-control" id="search" name="search"@isset($sort_search) value="{{ $sort_search }}" @endisset placeholder="{{ translate('Type name & Enter') }}">
                </div>
            </form>
        </div>
    </div>
    <div class="card-body">
        <table class="lmt-table">
            <thead>
                <tr>
                    <th data-breakpoints="lg">#</th>
                    <th width="30%">{{translate('Name')}}</th>
                    @if($type == 'Seller')
                        <th data-breakpoints="lg">{{translate('Added By')}}</th>
                    @endif
                    <th data-breakpoints="lg">{{translate('Photo')}}</th>
                    <th data-breakpoints="lg">{{translate('Base Price')}}</th>
                    <th data-breakpoints="lg">{{translate('Todays Deal')}}</th>
                    <th data-breakpoints="lg">{{translate('Published')}}</th>
                    @if(get_setting('product_approve_by_admin') == 1 && $type == 'Seller')
                        <th data-breakpoints="lg">{{translate('Approved')}}</th>
                    @endif
                    <th data-breakpoints="lg">{{translate('Featured')}}</th>
                    <th data-breakpoints="lg" class="text-right">{{translate('Options')}}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($products as $key => $product)
                    <tr>
                        <td data-text="#">{{ ($key+1) + ($products->currentPage() - 1)*$products->perPage() }}</td>
                        <td data-text="{{translate('Name')}}"><a href="{{ route('product', $product->slug) }}" class="text-muted" target="_blank"><b>{{ $product->getTranslation('name') }}</b></a></td>
                        @if($type == 'Seller')
                            <td data-text="{{translate('Added By')}}">{{ optional($product->user)->name }}</td>
                        @endif
                        <td data-text="{{translate('Photo')}}">
                            <img src="{{ uploaded_asset($product->thumbnail_img)}}" alt="Image" class="w-50px">
                        </td>
                        <td data-text="{{translate('Base Price')}}">{{ number_format($product->unit_price,2) }}</td>
                        <td data-text="{{translate('Todays Deal')}}">
                            <label class="aiz-switch aiz-switch-success mb-0">
                                <input onchange="update_todays_deal(this)" value="{{ $product->id }}" type="checkbox" <?php if($product->todays_deal == 1) echo "checked";?> >
                                <span class="slider round"></span>
                            </label>
                        </td>
                        <td data-text="{{translate('Published')}}">
                            <label class="aiz-switch aiz-switch-success mb-0">
                                <input onchange="update_published(this)" value="{{ $product->id }}" type="checkbox" <?php if($product->published == 1) echo "checked";?> >
                                <span class="slider round"></span>
                            </label>
                        </td>
                        @if(get_setting('product_approve_by_admin') == 1 && $type == 'Seller')
                            <td data-text="{{translate('Approved')}}">
                                <label class="aiz-switch aiz-switch-success mb-0">
                                    <input onchange="update_approved(this)" value="{{ $product->id }}" type="checkbox" <?php if ($product->approved == 1) echo "checked"; ?> >
                                    <span class="slider round"></span>
                                </label>
                            </td>
                        @endif
                        <td data-text="{{translate('Featured')}}">
                            <label class="aiz-switch aiz-switch-success mb-0">
                                <input onchange="update_featured(this)" value="{{ $product->id }}" type="checkbox" <?php if($product->featured == 1) echo "checked";?> >
                                <span class="slider round"></span>
                            </label>
                        </td>
                        <td class="text-right" data-text="{{translate('Options')}}">
                            <div class="">
                                @can('add_digital_product')
                                    <a class="btn btn-soft-primary btn-icon btn-circle btn-sm" href="{{route('digitalproducts.edit', ['id'=>$product->id, 'lang'=>env('DEFAULT_LANGUAGE')] )}}" title="{{ translate('Edit') }}">
                                        <i class="las la-edit"></i>
                                    </a>
                                @endcan
                                @can('add_digital_product')
                                    <a href="#" class="btn btn-soft-danger btn-icon btn-circle btn-sm confirm-delete" data-href="{{route('digitalproducts.destroy', $product->id)}}" title="{{ translate('Delete') }}">
                                        <i class="las la-trash"></i>
                                    </a>
                                @endcan
                                @can('add_digital_product')
                                    <a class="btn btn-soft-primary btn-icon btn-circle btn-sm" href="{{route('digitalproducts.download', encrypt($product->id))}}" title="{{ translate('Download') }}">
                                        <i class="las la-download"></i>
                                    </a>
                                @endcan
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        {{-- <table class="table aiz-table mb-0">
            <thead>
                <tr>
                    <th data-breakpoints="lg">#</th>
                    <th width="30%">{{translate('Name')}}</th>
                    @if($type == 'Seller')
                        <th data-breakpoints="lg">{{translate('Added By')}}</th>
                    @endif
                    <th data-breakpoints="lg">{{translate('Photo')}}</th>
                    <th data-breakpoints="lg">{{translate('Base Price')}}</th>
                    <th data-breakpoints="lg">{{translate('Todays Deal')}}</th>
                    <th data-breakpoints="lg">{{translate('Published')}}</th>
                    @if(get_setting('product_approve_by_admin') == 1 && $type == 'Seller')
                        <th data-breakpoints="lg">{{translate('Approved')}}</th>
                    @endif
                    <th data-breakpoints="lg">{{translate('Featured')}}</th>
                    <th data-breakpoints="lg" class="text-right">{{translate('Options')}}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($products as $key => $product)
                    <tr>
                        <td>{{ ($key+1) + ($products->currentPage() - 1)*$products->perPage() }}</td>
                        <td><a href="{{ route('product', $product->slug) }}" class="text-muted" target="_blank"><b>{{ $product->getTranslation('name') }}</b></a></td>
                        @if($type == 'Seller')
                            <td>{{ optional($product->user)->name }}</td>
                        @endif
                        <td>
                            <img src="{{ uploaded_asset($product->thumbnail_img)}}" alt="Image" class="w-50px">
                        </td>
                        <td>{{ number_format($product->unit_price,2) }}</td>
                        <td>
                            <label class="aiz-switch aiz-switch-success mb-0">
                                <input onchange="update_todays_deal(this)" value="{{ $product->id }}" type="checkbox" <?php if($product->todays_deal == 1) echo "checked";?> >
                                <span class="slider round"></span>
                            </label>
                        </td>
                        <td>
                            <label class="aiz-switch aiz-switch-success mb-0">
                                <input onchange="update_published(this)" value="{{ $product->id }}" type="checkbox" <?php if($product->published == 1) echo "checked";?> >
                                <span class="slider round"></span>
                            </label>
                        </td>
                        @if(get_setting('product_approve_by_admin') == 1 && $type == 'Seller')
                            <td>
                                <label class="aiz-switch aiz-switch-success mb-0">
                                    <input onchange="update_approved(this)" value="{{ $product->id }}" type="checkbox" <?php if ($product->approved == 1) echo "checked"; ?> >
                                    <span class="slider round"></span>
                                </label>
                            </td>
                        @endif
                        <td>
                            <label class="aiz-switch aiz-switch-success mb-0">
                                <input onchange="update_featured(this)" value="{{ $product->id }}" type="checkbox" <?php if($product->featured == 1) echo "checked";?> >
                                <span class="slider round"></span>
                            </label>
                        </td>
                        <td class="text-right">
                            @can('add_digital_product')
                                <a class="btn btn-soft-primary btn-icon btn-circle btn-sm" href="{{route('digitalproducts.edit', ['id'=>$product->id, 'lang'=>env('DEFAULT_LANGUAGE')] )}}" title="{{ translate('Edit') }}">
                                    <i class="las la-edit"></i>
                                </a>
                            @endcan
                            @can('add_digital_product')
                                <a href="#" class="btn btn-soft-danger btn-icon btn-circle btn-sm confirm-delete" data-href="{{route('digitalproducts.destroy', $product->id)}}" title="{{ translate('Delete') }}">
                                    <i class="las la-trash"></i>
                                </a>
                            @endcan
                            @can('add_digital_product')
                                <a class="btn btn-soft-primary btn-icon btn-circle btn-sm" href="{{route('digitalproducts.download', encrypt($product->id))}}" title="{{ translate('Download') }}">
                                    <i class="las la-download"></i>
                                </a>
                            @endcan
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table> --}}
        <div class="aiz-pagination">
            {{ $products->appends(request()->input())->links() }}
        </div>
    </div>
</div>


@endsection

@section('modal')
    @include('modals.delete_modal')
@endsection

@section('script')
    <script type="text/javascript">

        $(document).ready(function(){
            //$('#container').removeClass('mainnav-lg').addClass('mainnav-sm');
        });

        function update_todays_deal(el){

            if('{{env('DEMO_MODE')}}' == 'On'){
                AIZ.plugins.notify('info', '{{ translate('Data can not change in demo mode.') }}');
                return;
            }

            if(el.checked){
                var status = 1;
            }
            else{
                var status = 0;
            }
            $.post('{{ route('products.todays_deal') }}', {_token:'{{ csrf_token() }}', id:el.value, status:status}, function(data){
                if(data == 1){
                    AIZ.plugins.notify('success', '{{ translate('Todays Deal updated successfully') }}');
                }
                else{
                    AIZ.plugins.notify('danger', '{{ translate('Something went wrong') }}');
                }
            });
        }

        function update_published(el){
            if('{{env('DEMO_MODE')}}' == 'On'){
                AIZ.plugins.notify('info', '{{ translate('Data can not change in demo mode.') }}');
                return;
            }

            if(el.checked){
                var status = 1;
            }
            else{
                var status = 0;
            }
            $.post('{{ route('products.published') }}', {_token:'{{ csrf_token() }}', id:el.value, status:status}, function(data){
                if(data == 1){
                    AIZ.plugins.notify('success', '{{ translate('Published products updated successfully') }}');
                }
                else{
                    AIZ.plugins.notify('danger', '{{ translate('Something went wrong') }}');
                }
            });
        }

        function update_approved(el){

            if('{{env('DEMO_MODE')}}' == 'On'){
                AIZ.plugins.notify('info', '{{ translate('Data can not change in demo mode.') }}');
                return;
            }

            if(el.checked){
                var approved = 1;
            }
            else{
                var approved = 0;
            }
            $.post('{{ route('products.approved') }}', {
                _token      :   '{{ csrf_token() }}',
                id          :   el.value,
                approved    :   approved
            }, function(data){
                if(data == 1){
                    AIZ.plugins.notify('success', '{{ translate('Product approval update successfully') }}');
                }
                else{
                    AIZ.plugins.notify('danger', '{{ translate('Something went wrong') }}');
                }
            });
        }

        function update_featured(el){

            if('{{env('DEMO_MODE')}}' == 'On'){
                AIZ.plugins.notify('info', '{{ translate('Data can not change in demo mode.') }}');
                return;
            }

            if(el.checked){
                var status = 1;
            }
            else{
                var status = 0;
            }
            $.post('{{ route('products.featured') }}', {_token:'{{ csrf_token() }}', id:el.value, status:status}, function(data){
                if(data == 1){
                    AIZ.plugins.notify('success', '{{ translate('Featured products updated successfully') }}');
                }
                else{
                    AIZ.plugins.notify('danger', '{{ translate('Something went wrong') }}');
                }
            });
        }
    </script>
@endsection
