@php
    $total_items = count($cart);
@endphp

@if($total_items > 0)
    <div class="aiz-pos-cart-list mb-4">
        <ul class="list-group list-group-flush">
            @foreach($cart as $key => $cartItem)
                <li class="list-group-item px-0">
                    <div class="d-flex">
                        <span class="mr-2">
                            <img src="{{ uploaded_asset($cartItem['thumbnail_img']) }}"
                                 class="img-fit size-60px"
                                 onerror="this.onerror=null;this.src='{{ static_asset('assets/img/placeholder.jpg') }}';"
                            >
                        </span>
                        <span class="flex-grow-1">
                            <div class="text-truncate-2">{{ $cartItem['name'] }}</div>
                            <div class="d-flex">
                                <div class="mr-auto">
                                    <div class="fw-600 fs-16">
                                        {{ translate('Quantity') }}: {{ $cartItem['quantity'] }}
                                    </div>
                                </div>
                            </div>
                        </span>
                        <span class="">
                            <button type="button" class="btn btn-circle btn-icon btn-sm btn-soft-danger ml-2"
                                    onclick="removeFromCart('{{ $key }}')">
                                <i class="las la-trash-alt"></i>
                            </button>
                        </span>
                    </div>
                </li>
            @endforeach
        </ul>
    </div>

    <div class="py-3">
        <button type="button" class="btn btn-primary btn-block fw-700 fs-14 rounded-0" onclick="checkout()">
            {{ translate('Add Products') }} ({{ $total_items }} {{ translate('items') }})
        </button>
    </div>
@else
    <div class="text-center py-4">
        <p>{{ translate("Your cart is empty") }}</p>
    </div>
@endif
