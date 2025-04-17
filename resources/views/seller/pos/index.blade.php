@extends('seller.layouts.app')

@section('panel_content')
<div class="aiz-titlebar mt-2 mb-4">
    <div class="row align-items-center">
        <div class="col-md-6">
            <h1 class="h3">{{ translate('POS System') }}</h1>
        </div>
    </div>
</div>

<div class="row gutters-10">
    <div class="col-lg-8">
        <div class="card mb-3">
            <div class="card-body">
                <form class="" action="" method="GET">
                    <div class="row gutters-5">
                        <div class="col-md-3">
                            <select name="category_id" class="form-control aiz-selectpicker" data-live-search="true" onchange="this.form.submit()">
                                <option value="">{{ translate('All Categories') }}</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}" @if(request('category_id') == $category->id) selected @endif>{{ $category->getTranslation('name') }}</option>
                                    @foreach ($category->childrenCategories as $childCategory)
                                        <option value="{{ $childCategory->id }}" @if(request('category_id') == $childCategory->id) selected @endif>-- {{ $childCategory->getTranslation('name') }}</option>
                                    @endforeach
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-7">
                            <div class="input-group">
                                <input type="text" class="form-control" id="search" name="search" placeholder="{{ translate('Search product') }}" value="{{ request('search') }}">
                                <div class="input-group-append">
                                    <button class="btn btn-light" type="submit">
                                        <i class="las la-search la-rotate-270"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div class="card">
            <div class="card-body">
                <div class="row gutters-10">
                    @foreach($products as $key => $product)
                        <div class="col-lg-3 col-md-4 col-6">
                            <div class="card mb-3 shadow-sm product-card overflow-hidden">
                                <div class="card-body p-2">
                                    <div class="product-img">
                                        <img
                                            class="img-fit h-110px lazyload mx-auto"
                                            src="{{ static_asset('assets/img/placeholder.jpg') }}"
                                            data-src="{{ uploaded_asset($product->thumbnail_img) }}"
                                            alt="{{ $product->name }}"
                                            onerror="this.onerror=null;this.src='{{ static_asset('assets/img/placeholder.jpg') }}';"
                                        >
                                    </div>
                                    <div class="product-info">
                                        <div class="fs-14 fw-600 text-truncate-2 mb-1">{{ $product->name }}</div>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div class="fw-700 fs-16 text-primary">{{ format_price($product->unit_price) }}</div>
                                            <div class="badge badge-inline badge-secondary">{{ translate('Stock') }}: {{ $product->current_stock }}</div>
                                        </div>
                                    </div>
                                    <div class="mt-2">
                                        <button type="button" class="btn btn-soft-primary btn-sm w-100 rounded-0 add-to-cart fw-600"
                                                onclick="addToCart({{ $product->id }})">
                                            <i class="las la-shopping-cart"></i>
                                            {{ translate('Add to cart') }}
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="aiz-pagination mt-3">
                    {{ $products->appends(request()->input())->links() }}
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card mb-3">
            <div class="card-body">
                <div class="d-flex justify-content-between mb-2">
                    <div class="fw-600 fs-15">{{ translate('Cart') }}</div>
                    <div>
                        <button class="btn btn-sm btn-soft-primary" onclick="clearCart()">{{ translate('Clear cart') }}</button>
                    </div>
                </div>
                <div id="cart-details">
                    <!-- Cart items will be loaded here -->
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script type="text/javascript">
    $(document).ready(function(){
        getCart();
    });

    function addToCart(id) {
        $.ajax({
            type: "POST",
            url: '{{ route("seller.pos.addToCart") }}',
            data: {
                id: id,
                _token: '{{ csrf_token() }}'
            },
            success: function(data) {
                if (data.success == 1) {
                    AIZ.plugins.notify('success', data.message);
                    getCart();
                } else {
                    AIZ.plugins.notify('danger', data.message);
                }
            }
        });
    }

    function getCart() {
        $.ajax({
            type: "GET",
            url: '{{ route("seller.pos.getCart") }}',
            success: function(data) {
                $('#cart-details').html(data);
            }
        });
    }

    function updateQuantity(id, quantity) {
        $.ajax({
            type: "POST",
            url: '{{ route("seller.pos.updateQuantity") }}',
            data: {
                id: id,
                quantity: quantity,
                _token: '{{ csrf_token() }}'
            },
            success: function(data) {
                if (data.success == 1) {
                    AIZ.plugins.notify('success', data.message);
                    getCart();
                } else {
                    AIZ.plugins.notify('danger', data.message);
                }
            }
        });
    }

    function removeFromCart(id) {
        $.ajax({
            type: "POST",
            url: '{{ route("seller.pos.removeFromCart") }}',
            data: {
                id: id,
                _token: '{{ csrf_token() }}'
            },
            success: function(data) {
                if (data.success == 1) {
                    AIZ.plugins.notify('success', data.message);
                    getCart();
                } else {
                    AIZ.plugins.notify('danger', data.message);
                }
            }
        });
    }

    function setDiscount() {
        var discount = $('#discount_input').val();
        $.ajax({
            type: "POST",
            url: '{{ route("seller.pos.setDiscount") }}',
            data: {
                discount: discount,
                _token: '{{ csrf_token() }}'
            },
            success: function(data) {
                getCart();
            }
        });
    }

    function setShipping() {
        var shipping = $('#shipping_input').val();
        $.ajax({
            type: "POST",
            url: '{{ route("seller.pos.setShipping") }}',
            data: {
                shipping: shipping,
                _token: '{{ csrf_token() }}'
            },
            success: function(data) {
                getCart();
            }
        });
    }

    function clearCart() {
        if (confirm('{{ translate("Are you sure you want to clear the cart?") }}')) {
            var cart = document.getElementById('cart-details');
            cart.innerHTML = '<div class="text-center py-4"><p>{{ translate("Your cart is empty") }}</p></div>';
            $.ajax({
                type: "POST",
                url: '{{ route("seller.pos.removeFromCart") }}',
                data: {
                    id: 'all',
                    _token: '{{ csrf_token() }}'
                },
                success: function(data) {
                    getCart();
                }
            });
        }
    }

    function checkout() {
        $.ajax({
            type: "POST",
            url: '{{ route("seller.pos.checkout") }}',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function(data) {
                if (data.success == 1) {
                    AIZ.plugins.notify('success', data.message);
                    getCart();
                    setTimeout(function(){
                        location.reload();
                    }, 1000);
                } else {
                    AIZ.plugins.notify('danger', data.message);
                }
            }
        });
    }
</script>
@endsection
