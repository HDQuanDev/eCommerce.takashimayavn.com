@extends('backend.layouts.app')

@section('content')


<div class="aiz-titlebar text-left mt-2 mb-3">
    <div class="row align-items-center">
        <div class="col-auto">
            <h1 class="h3">{{ translate('Commission Packages') }}</h1>
        </div>
        @if(auth()->user()->can('add_commission_package'))
            <div class="col text-right">
                <button onclick="show_create_package_modal()" class="btn btn-circle btn-info">
                    <span>{{ translate('Add New Package')}}</span>
                </button>
            </div>
        @endif
    </div>
</div>

<div class="card">
    <form class="" id="sort_commission_packages" action="" method="GET">
        <div class="card-header row gutters-5">
            <div class="col">
                <h5 class="mb-md-0 h6">{{ translate('Commission Packages') }}</h5>
            </div>

                <div class="dropdown mb-2 mb-md-0">
                    <button class="btn border dropdown-toggle" type="button" data-toggle="dropdown">
                        {{translate('Bulk Action')}}
                    </button>
                    <div class="dropdown-menu dropdown-menu-right">
                        @can('delete_seller')
                            <a class="dropdown-item confirm-alert" href="javascript:void(0)"  data-target="#bulk-delete-modal">{{translate('Delete selection')}}</a>
                        @endcan
                        @can('seller_commission_configuration')
                            <a class="dropdown-item confirm-alert" onclick="set_bulk_commission()">{{translate('Set Bulk Commission')}}</a>
                        @endcan
                    </div>
                </div>

                <div class="col-md-2 ml-auto">
                    <select class="form-control aiz-selectpicker" name="status" id="status" onchange="sort_commission_packages()">
                        <option value="">{{translate('Filter by Status')}}</option>
                        <option value="active"  @isset($status) @if($status == 'active') selected @endif @endisset>{{translate('Active')}}</option>
                        <option value="inactive"  @isset($status) @if($status == 'inactive') selected @endif @endisset>{{translate('Inactive')}}</option>
                    </select>
                </div>
            <div class="col-md-3">
                <div class="form-group mb-0">
                  <input type="text" class="form-control" id="search" name="sort_search"@isset($sort_search) value="{{ $sort_search }}" @endisset placeholder="{{ translate('Type name & Enter') }}">
                </div>
            </div>
        </div>
        <div class="card-body">
            <table class="table aiz-table mb-0">
                <thead>
                <tr>
                    <th>
                        @if(auth()->user()->can('delete_commission_package'))
                            <div class="form-group">
                                <div class="aiz-checkbox-inline">
                                    <label class="aiz-checkbox">
                                        <input type="checkbox" class="check-all">
                                        <span class="aiz-square-check"></span>
                                    </label>
                                </div>
                            </div>
                        @else
                            #
                        @endif
                    </th>
                    <th>{{translate('Name')}}</th>
                    <th >{{translate('Duration')}}</th>
                    <th >{{translate('Price')}}</th>
                    <th >{{translate('Commission Percentage')}}</th>
                    <th data-breakpoints="lg">{{translate('Description')}}</th>
                    <th data-breakpoints="lg">{{translate('Image')}}</th>
                    <th >{{translate('Status')}}</th>
                    <th width="10%">{{translate('Options')}}</th>
                </tr>
                </thead>
                <tbody>
                    @foreach($packages as $package)
                        <tr>
                            <td>
                                @if(auth()->user()->can('delete_commission_package'))
                                    <div class="form-group">
                                        <div class="aiz-checkbox-inline">
                                            <label class="aiz-checkbox">
                                                <input type="checkbox" class="check" name="id[]" value="{{ $package->id }}">
                                                <span class="aiz-square-check"></span>
                                            </label>
                                        </div>
                                    </div>
                                @else
                                    {{ $package->id }}
                                @endif
                            </td>
                            <td>{{ $package->name }}</td>
                            <td>{{ $package->duration }}</td>
                            <td>{{number_format(convert_price($package->price), 2) }} {{ currency_symbol() }}</td>
                            <td>{{ $package->commission_percentage }}</td>
                            <td>{{ $package->description }}</td>
                            <td>
                                @if($package->image)
                                    <img src="{{ uploaded_asset($package->image) }}" alt="{{ $package->name }}" width="50">
                                @endif
                            </td>
                            <td>
                                <label class="aiz-switch aiz-switch-success mb-0">
                                    <input
                                        @can('approve_seller') onchange="update_status(this)" @endcan
                                        value="{{ $package->id }}" type="checkbox"
                                        <?php if($package->status == 'active') echo "checked";?>
                                        @cannot('approve_seller') disabled @endcan
                                    >
                                    <span class="slider round"></span>
                                </label>
                            </td>
                            <td>
                                @if(auth()->user()->can('edit_commission_package'))
                                <a href="javascript:void(0);" onclick="show_edit_package_modal({{ $package->id }})" class="btn btn-primary btn-sm">{{ translate('Edit') }}</a>
                                @endif
                                @if(auth()->user()->can('delete_commission_package'))
                                    <a href="{{ route('commission-packages.destroy', $package->id) }}" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this package?')">{{ translate('Delete') }}</a>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="aiz-pagination">
                {{ $packages->links() }}
            </div>
        </div>
    </form>
</div>

@endsection

@section('modal')
	<!-- Delete Modal -->
	@include('modals.delete_modal')
    <!-- Bulk Delete modal -->
    @include('modals.bulk_delete_modal')

    <!-- Create Package Modal -->
	<div class="modal fade" id="create_package_modal">
		<div class="modal-dialog">
			<div class="modal-content" id="create_package_modal_content">

			</div>
		</div>
	</div>
	<!-- Seller Profile Modal -->
	<div class="modal fade" id="profile_modal">
		<div class="modal-dialog">
			<div class="modal-content" id="profile-modal-content">

			</div>
		</div>
	</div>

	<!-- Seller Payment Modal -->
	<div class="modal fade" id="payment_modal">
	    <div class="modal-dialog">
	        <div class="modal-content" id="payment-modal-content">

	        </div>
	    </div>
	</div>

	<!-- Ban Seller Modal -->
	<div class="modal fade" id="confirm-ban">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title h6">{{translate('Confirmation')}}</h5>
					<button type="button" class="close" data-dismiss="modal">
					</button>
				</div>
				<div class="modal-body">
                    <p>{{translate('Do you really want to ban this seller?')}}</p>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-light" data-dismiss="modal">{{translate('Cancel')}}</button>
					<a class="btn btn-primary" id="confirmation">{{translate('Proceed!')}}</a>
				</div>
			</div>
		</div>
	</div>

	<!-- Unban Seller Modal -->
	<div class="modal fade" id="confirm-unban">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title h6">{{translate('Confirmation')}}</h5>
                    <button type="button" class="close" data-dismiss="modal">
                    </button>
                </div>
                <div class="modal-body">
                        <p>{{translate('Do you really want to unban this seller?')}}</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-dismiss="modal">{{translate('Cancel')}}</button>
                    <a class="btn btn-primary" id="confirmationunban">{{translate('Proceed!')}}</a>
                </div>
            </div>
        </div>
    </div>

    {{-- Set Selelr Commission --}}
    <div class="modal fade" id="set_seller_commission">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title h6">{{translate('Set Seller Commission')}}</h5>
                    <button type="button" class="close" data-dismiss="modal">
                    </button>
                </div>
                <form class="form-horizontal" action="{{ route('set_seller_based_commission') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <input type="hidden" name="seller_ids" value="" id="seller_ids">
                        <div class="form-group row">
                            <label class="col-md-3 col-from-label">{{translate('Selle Commission')}}</label>
                            <div class="col-md-9">
                                <div class="input-group">
                                    <input type="number" lang="en" min="0" max="100" step="0.01" placeholder="{{translate('Commission Percentage')}}" name="commission_percentage" class="form-control" required>
                                    <div class="input-group-append">
                                        <span class="input-group-text">%</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary btn-sm text-white">{{translate('save!')}}</button>
                        <button type="button" class="btn btn-sm btn-light" data-dismiss="modal">{{translate('Cancel')}}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Edit Seller Custom Followers --}}
    <div class="modal fade" id="edit_seller_custom_followers">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title h6">{{translate('Edit Seller Custom Followers')}}</h5>
                    <button type="button" class="close" data-dismiss="modal">
                    </button>
                </div>
                <form class="form-horizontal" action="{{ route('edit_Seller_custom_followers') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <input type="hidden" name="shop_id" value="" id="shop_id">
                        <div class="form-group row">
                            <label class="col-md-3 col-from-label">{{translate('Custom Followers')}}</label>
                            <div class="col-md-9">
                                <input type="number" lang="en" min="0" step="1" placeholder="{{translate('Custom Followers')}}" value="" name="custom_followers" id="custom_followers" class="form-control" required>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary btn-sm text-white">{{translate('save!')}}</button>
                        <button type="button" class="btn btn-sm btn-light" data-dismiss="modal">{{translate('Cancel')}}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script type="text/javascript">
        $(document).on("change", ".check-all", function() {
            if(this.checked) {
                // Iterate each checkbox
                $('.check-one:checkbox').each(function() {
                    this.checked = true;
                });
            } else {
                $('.check-one:checkbox').each(function() {
                    this.checked = false;
                });
            }

        });
        function show_create_package_modal(){
            $.get('{{ route('commission-packages.create') }}',{_token:'{{ @csrf_token() }}'}, function(data){
                $('#create_package_modal #create_package_modal_content').html(data);
                $('#create_package_modal').modal('show', {backdrop: 'static'});
            });
        }
        function show_edit_package_modal(id) {
    $.get('{{ route('commission-packages.edit', '') }}/' + id, function(data) {

        $('#create_package_modal_content').html(data);
        $('#create_package_modal').modal('show');
    });
}


        function update_status(el){
            if(el.checked){
                var status = 'active';
            }
            else{
                var status = 'inactive';
            }
            $.post('{{ route('commission-packages.update_status') }}', {_token:'{{ csrf_token() }}', id:el.value, status:status}, function(data){
                if(data == 1){
                    AIZ.plugins.notify('success', '{{ translate('Status updated successfully') }}');
                }
                else{
                    AIZ.plugins.notify('danger', '{{ translate('Something went wrong') }}');
                }
            });
        }

        function sort_commission_packages(el){
            $('#sort_commission_packages').submit();
        }

        function confirm_ban(url)
        {
            if('{{env('DEMO_MODE')}}' == 'On'){
                AIZ.plugins.notify('info', '{{ translate('Data can not change in demo mode.') }}');
                return;
            }

            $('#confirm-ban').modal('show', {backdrop: 'static'});
            document.getElementById('confirmation').setAttribute('href' , url);
        }

        function confirm_unban(url)
        {
            if('{{env('DEMO_MODE')}}' == 'On'){
                AIZ.plugins.notify('info', '{{ translate('Data can not change in demo mode.') }}');
                return;
            }

            $('#confirm-unban').modal('show', {backdrop: 'static'});
            document.getElementById('confirmationunban').setAttribute('href' , url);
        }

        function bulk_delete() {
            var data = new FormData($('#sort_commission_packages')[0]);
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: "{{route('commission-packages.bulk_delete')}}",
                type: 'POST',
                data: data,
                cache: false,
                contentType: false,
                processData: false,
                success: function (response) {
                    if(response == 1) {
                        location.reload();
                    }
                }
            });
        }

        // Set Commission
        function set_commission(shop_id){
            var sellerIds = [];
            sellerIds.push(shop_id);
            $('#seller_ids').val(sellerIds);
            $('#set_seller_commission').modal('show', {backdrop: 'static'});
        }

        // Set seller bulk commission
        function set_bulk_commission(){
            var sellerIds = [];
            $(".check-one[name='id[]']:checked").each(function() {
                sellerIds.push($(this).val());
            });
            if(sellerIds.length > 0){
                $('#seller_ids').val(sellerIds);
                $('#set_seller_commission').modal('show', {backdrop: 'static'});
            }
            else{
                AIZ.plugins.notify('danger', '{{ translate('Please Select Seller first.') }}');
            }
        }


        // Edit seller custom followers
        function editCustomFollowers(shop_id, custom_followers){
            $('#shop_id').val(shop_id);
            $('#custom_followers').val(custom_followers);
            $('#edit_seller_custom_followers').modal('show', {backdrop: 'static'});
        }

    </script>
@endsection
