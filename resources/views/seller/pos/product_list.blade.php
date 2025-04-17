@extends('seller.layouts.app')

@section('panel_content')
    <div class="aiz-titlebar mt-2 mb-4">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h1 class="h3">{{ translate('Danh sách sản phẩm từ POS') }}</h1>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h5 class="mb-0 h6">{{ translate('Sản phẩm từ POS') }}</h5>
            <div class="col-md-5">
                <div class="form-group mb-0">
                    <form class="" action="" method="GET">
                        <div class="input-group">
                            <input type="text" class="form-control form-control-sm" id="search" name="search" @isset($search) value="{{ $search }}" @endisset placeholder="{{ translate('Tìm kiếm sản phẩm') }}">
                            <div class="input-group-append">
                                <button class="btn btn-sm btn-primary" type="submit">
                                    <i class="las la-search la-rotate-270"></i>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="card-body">
            <table class="table aiz-table mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th width="30%">{{ translate('Tên') }}</th>
                        <th>{{ translate('Hình ảnh') }}</th>
                        <th>{{ translate('Danh mục') }}</th>
                        <th>{{ translate('Giá gốc') }}</th>
                        <th>{{ translate('Tồn kho') }}</th>
                        <th>{{ translate('Trạng thái') }}</th>
                        <th class="text-right">{{ translate('Tùy chọn') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($products as $key => $product)
                        <tr>
                            <td>{{ ($key+1) + ($products->currentPage() - 1)*$products->perPage() }}</td>
                            <td>
                                <a href="{{ route('product', $product->slug) }}" target="_blank" class="text-reset">
                                    <span>{{ $product->name }}</span>
                                </a>
                            </td>
                            <td>
                                <img src="{{ uploaded_asset($product->thumbnail_img) }}" alt="Image" class="w-50px">
                            </td>
                            <td>
                                @if ($product->category != null)
                                    <span>{{ $product->category->name }}</span>
                                @endif
                            </td>
                            <td>{{ number_format($product->unit_price, 2) }}</td>
                            <td>{{ $product->current_stock }}</td>
                            <td>
                                <label class="aiz-switch aiz-switch-success mb-0">
                                    <input onchange="update_published(this)" value="{{ $product->id }}" type="checkbox" <?php if($product->published == 1) echo "checked";?> >
                                    <span class="slider round"></span>
                                </label>
                            </td>
                            <td class="text-right">
                                <a class="btn btn-soft-primary btn-icon btn-circle btn-sm" href="{{route('seller.products.edit', ['id'=>$product->id, 'lang'=>env('DEFAULT_LANGUAGE')] )}}" title="{{ translate('Sửa') }}">
                                    <i class="las la-edit"></i>
                                </a>
                                <a href="#" class="btn btn-soft-danger btn-icon btn-circle btn-sm confirm-delete" data-href="{{route('seller.products.destroy', $product->id)}}" title="{{ translate('Xóa') }}">
                                    <i class="las la-trash"></i>
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="aiz-pagination">
                {{ $products->appends(request()->input())->links() }}
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script type="text/javascript">
        function update_published(el){
            if(el.checked){
                var status = 1;
            }
            else{
                var status = 0;
            }
            $.post('{{ route('seller.products.published') }}', {_token:'{{ csrf_token() }}', id:el.value, status:status}, function(data){
                if(data == 1){
                    AIZ.plugins.notify('success', '{{ translate('Trạng thái sản phẩm đã được cập nhật thành công') }}');
                }
                else{
                    AIZ.plugins.notify('danger', '{{ translate('Có lỗi xảy ra') }}');
                }
            });
        }
    </script>
@endsection
