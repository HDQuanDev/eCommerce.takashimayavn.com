@extends('backend.layouts.app')

@section('content')
<div class="aiz-titlebar text-left mt-2 mb-3">
    <div class="row align-items-center">
        <div class="col-md-6">
            <h1 class="h3">{{translate('All Referral Codes')}}</h1>
        </div>
        <div class="col-md-6 text-md-right">
            <a href="{{ route('referral-codes.create') }}" class="btn btn-primary">
                <span>{{translate('Add New Referral Code')}}</span>
            </a>
        </div>
    </div>
</div>

<div class="card">
    <form class="" id="sort_sellers" action="" method="GET">
        <div class="card-header row gutters-5">
            <div class="col text-center text-md-left">
                <h5 class="mb-md-0 h6">{{ translate('Referral Codes') }}</h5>
            </div>
            <div class="col-md-4">
                <div class="input-group input-group-sm">
                    <input type="text" class="form-control" id="search" name="search" @isset($sort_search) value="{{ $sort_search }}" @endisset placeholder="{{ translate('Type code & hit Enter') }}">
                </div>
            </div>
        </div>
    </form>
    <div class="card-body">
        <table class="table aiz-table mb-0">
            <thead>
                <tr>
                    <th data-breakpoints="lg">#</th>
                    <th>{{translate('Code')}}</th>
                    <th>{{translate('Usage Limit')}}</th>
                    <th>{{translate('Used Count')}}</th>
                    <th>{{translate('Status')}}</th>
                    <th data-breakpoints="lg">{{translate('Description')}}</th>
                    <th>{{translate('Created At')}}</th>
                    <th class="text-right">{{translate('Options')}}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($referral_codes as $key => $referral_code)
                    <tr>
                        <td>{{ ($key+1) + ($referral_codes->currentPage() - 1)*$referral_codes->perPage() }}</td>
                        <td>{{ $referral_code->code }}</td>
                        <td>{{ $referral_code->usage_limit ?? translate('Unlimited') }}</td>
                        <td>{{ $referral_code->used_count }}</td>
                        <td>
                            <label class="aiz-switch aiz-switch-success mb-0">
                                <input onchange="update_status(this)" value="{{ $referral_code->id }}" type="checkbox" <?php if($referral_code->is_active == 1) echo "checked";?> >
                                <span class="slider round"></span>
                            </label>
                        </td>
                        <td>{{ $referral_code->description }}</td>
                        <td>{{ date('d-m-Y', strtotime($referral_code->created_at)) }}</td>
                        <td class="text-right">
                            <a href="{{ route('referral-codes.edit', encrypt($referral_code->id)) }}" class="btn btn-soft-primary btn-icon btn-circle btn-sm" title="{{ translate('Edit') }}">
                                <i class="las la-edit"></i>
                            </a>
                            <a href="#" class="btn btn-soft-danger btn-icon btn-circle btn-sm confirm-delete" data-href="{{route('referral-codes.destroy', $referral_code->id)}}" title="{{ translate('Delete') }}">
                                <i class="las la-trash"></i>
                            </a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div class="aiz-pagination">
            {{ $referral_codes->appends(request()->input())->links() }}
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
                var status = 1;
            }
            else{
                var status = 0;
            }
            $.post('{{ route('referral-codes.update_status') }}', {_token:'{{ csrf_token() }}', id:el.value, status:status}, function(data){
                if(data == 1){
                    AIZ.plugins.notify('success', '{{ translate('Referral code status updated successfully') }}');
                }
                else{
                    AIZ.plugins.notify('danger', '{{ translate('Something went wrong') }}');
                }
            });
        }
    </script>
@endsection