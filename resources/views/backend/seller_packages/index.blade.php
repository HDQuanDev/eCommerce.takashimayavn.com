@extends('backend.layouts.app')

@section('content')
<div class="aiz-titlebar text-left mt-2 mb-3">
    <div class="row align-items-center">
        <div class="col-md-6">
            <h1 class="h3">{{translate('All Seller Packages')}}</h1>
        </div>
        <div class="col-md-6 text-md-right">
            <a href="{{ route('seller_packages.create') }}" class="btn btn-primary">
                <span>{{translate('Add New Package')}}</span>
            </a>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header row gutters-5">
        <div class="col text-center text-md-left">
            <h5 class="mb-md-0 h6">{{ translate('All Seller Packages') }}</h5>
        </div>
    </div>
    <div class="card-body">
        <table class="table aiz-table mb-0">
            <thead>
                <tr>
                    <th>#</th>
                    <th>{{translate('Logo')}}</th>
                    <th>{{translate('Name')}}</th>
                    <th>{{translate('Amount')}}</th>
                    <th>{{translate('Product Upload Limit')}}</th>
                    <th>{{translate('Duration')}}</th>
                    <th>{{translate('Status')}}</th>
                    <th class="text-right">{{translate('Options')}}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($seller_packages as $key => $seller_package)
                    <tr>
                        <td>{{ $key+1 }}</td>
                        <td>
                            @if($seller_package->logo)
                                <img src="{{ uploaded_asset($seller_package->logo) }}" alt="{{translate('Logo')}}" class="h-50px">
                            @else
                                <img src="{{ static_asset('assets/img/placeholder.jpg') }}" alt="{{translate('Logo')}}" class="h-50px">
                            @endif
                        </td>
                        <td>{{ $seller_package->name }}</td>
                        <td>{{ format_price($seller_package->amount) }}</td>
                        <td>{{ $seller_package->product_upload_limit }}</td>
                        <td>{{ $seller_package->duration }} {{translate('Days')}}</td>
                        <td>
                            <label class="aiz-switch aiz-switch-success mb-0">
                                <input onchange="update_status(this)" value="{{ $seller_package->id }}" type="checkbox" <?php if($seller_package->status == 'active') echo "checked";?> >
                                <span class="slider round"></span>
                            </label>
                        </td>
                        <td class="text-right">
                            <a class="btn btn-soft-primary btn-icon btn-circle btn-sm" href="{{route('seller_packages.edit', $seller_package->id )}}" title="{{ translate('Edit') }}">
                                <i class="las la-edit"></i>
                            </a>
                            <a href="#" class="btn btn-soft-danger btn-icon btn-circle btn-sm confirm-delete" data-href="{{route('seller_packages.destroy', $seller_package->id)}}" title="{{ translate('Delete') }}">
                                <i class="las la-trash"></i>
                            </a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div class="aiz-pagination">
            @if(method_exists($seller_packages, 'links'))
                {{ $seller_packages->links() }}
            @endif
        </div>
    </div>
</div>
@endsection

@section('modal')
    @include('modals.delete_modal')
@endsection

@section('script')
    <script type="text/javascript">
        function update_status(el){
            if(el.checked){
                var status = 'active';
            }
            else{
                var status = 'inactive';
            }
            $.post('{{ route('seller_packages.update_status') }}', {_token:'{{ csrf_token() }}', id:el.value, status:status}, function(data){
                if(data == 1){
                    AIZ.plugins.notify('success', '{{ translate('Package status updated successfully') }}');
                }
                else{
                    AIZ.plugins.notify('danger', '{{ translate('Something went wrong') }}');
                }
            });
        }
    </script>
@endsection
