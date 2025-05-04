@extends('backend.layouts.app')

@section('content')
    <div class="aiz-titlebar text-left mt-2 mb-3">
        <div class="row align-items-center">
            <div class="col-auto">
                <h1 class="h3">{{ translate('Ads Packages') }}</h1>
            </div>
            @if (auth()->user()->can('add_ads_package'))
                <div class="col text-right">
                    <button onclick="show_create_package_modal()" class="btn btn-circle btn-info">
                        <span>{{ translate('Add New Package') }}</span>
                    </button>
                </div>
            @endif
        </div>
    </div>

    <div class="card">
        <form class="" id="sort_commission_packages" action="" method="GET">
            <div class="card-header row gutters-5">
                <div class="col">
                    <h5 class="mb-md-0 h6">{{ translate('Ads Packages') }}</h5>
                </div>

                <div class="dropdown mb-2 mb-md-0">
                    <button class="btn border dropdown-toggle" type="button" data-toggle="dropdown">
                        {{ translate('Bulk Action') }}
                    </button>
                    <div class="dropdown-menu dropdown-menu-right">
                        <a class="dropdown-item confirm-alert" href="javascript:void(0)"
                            data-target="#bulk-delete-modal">{{ translate('Delete selection') }}</a>
                    </div>
                </div>

                <div class="col-md-2 ml-auto">
                    <select class="form-control aiz-selectpicker" name="status" id="status"
                        onchange="sort_commission_packages()">
                        <option value="">{{ translate('Filter by Status') }}</option>
                        <option value="active"
                            @isset($status) @if ($status == 'active') selected @endif @endisset>
                            {{ translate('Active') }}</option>
                        <option value="inactive"
                            @isset($status) @if ($status == 'inactive') selected @endif @endisset>
                            {{ translate('Inactive') }}</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <div class="form-group mb-0">
                        <input type="text" class="form-control" id="search"
                            name="sort_search"@isset($sort_search) value="{{ $sort_search }}" @endisset
                            placeholder="{{ translate('Type name & Enter') }}">
                    </div>
                </div>
            </div>
            <div class="card-body">
                <table class="table aiz-table mb-0">
                    <thead>
                        <tr>
                            <th>
                                @if (auth()->user()->can('delete_ads_package'))
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
                            <th>{{ translate('Name') }}</th>
                            <th>{{ translate('Price') }}</th>
                            <th>{{ translate('Reach') }}</th>
                            <th>{{ translate('Status') }}</th>
                            <th width="10%">{{ translate('Options') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($packages as $package)
                            <tr>
                                <td>
                                    @if (auth()->user()->can('delete_ads_package'))
                                        <div class="form-group">
                                            <div class="aiz-checkbox-inline">
                                                <label class="aiz-checkbox">
                                                    <input type="checkbox" class="check" name="id[]"
                                                        value="{{ $package->id }}">
                                                    <span class="aiz-square-check"></span>
                                                </label>
                                            </div>
                                        </div>
                                    @else
                                        {{ $package->id }}
                                    @endif
                                </td>
                                <td>{{ $package->name }}</td>
                                <td>{{ number_format(convert_price($package->price), 2) }} {{ currency_symbol() }}</td>
                                <td>{{ $package->reach }}</td>
                                <td>
                                    <label class="aiz-switch aiz-switch-success mb-0">
                                        <input @can('approve_seller') onchange="update_status(this)" @endcan
                                            value="{{ $package->id }}" type="checkbox" <?php if ($package->status == 'active') {
                                                echo 'checked';
                                            } ?>
                                            @cannot('approve_seller') disabled @endcan>
                                        <span class="slider round"></span>
                                    </label>
                                </td>
                                <td class="text-right">
                                    <a class="btn btn-soft-primary btn-icon btn-circle btn-sm" href="javascript:void(0);"
                                        onclick="show_edit_package_modal({{ $package->id }})"
                                        title="{{ translate('Edit') }}">
                                        <i class="las la-edit"></i>
                                    </a>
                                    <a href="#" class="btn btn-soft-danger btn-icon btn-circle btn-sm confirm-delete"
                                        data-href="{{ route('commission-packages.destroy', $package->id) }}"
                                        title="{{ translate('Delete') }}">
                                        <i class="las la-trash"></i>
                                    </a>
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
@endsection

@section('script')
    <script type="text/javascript">
        $(document).on("change", ".check-all", function() {
            if (this.checked) {
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

        function show_create_package_modal() {
            $.get('{{ route('ads-packages.create') }}', {
                _token: '{{ @csrf_token() }}'
            }, function(data) {
                $('#create_package_modal #create_package_modal_content').html(data);
                $('#create_package_modal').modal('show', {
                    backdrop: 'static'
                });
            });
        }

        function show_edit_package_modal(id) {
            $.get('{{ route('ads-packages.edit', '') }}/' + id, function(data) {

                $('#create_package_modal_content').html(data);
                $('#create_package_modal').modal('show');
            });
        }

        function update_status(el) {
            if (el.checked) {
                var status = 'active';
            } else {
                var status = 'inactive';
            }
            $.post('{{ route('ads-packages.update_status') }}', {
                _token: '{{ csrf_token() }}',
                id: el.value,
                status: status
            }, function(data) {
                if (data == 1) {
                    AIZ.plugins.notify('success', '{{ translate('Status updated successfully') }}');
                } else {
                    AIZ.plugins.notify('danger', '{{ translate('Something went wrong') }}');
                }
            });
        }

        function sort_commission_packages(el) {
            $('#sort_commission_packages').submit();
        }

        function bulk_delete() {
            var data = new FormData($('#sort_commission_packages')[0]);
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: "{{ route('ads-packages.bulk_delete') }}",
                type: 'POST',
                data: data,
                cache: false,
                contentType: false,
                processData: false,
                success: function(response) {
                    if (response == 1) {
                        location.reload();
                    }
                }
            });
        }
    </script>
@endsection
