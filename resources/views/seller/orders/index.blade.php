@extends('seller.layouts.app')

@section('panel_content')

    <div class="card">
        <form class="" action="" id="sort_orders" method="GET">
            <div class="card-header row gutters-5">
                <div class="col text-center text-md-left">
                    <h5 class="mb-md-0 h6">{{ translate('Orders') }}</h5>
                </div>

                <div class="dropdown mb-2 mb-md-0">
                    <button class="btn border dropdown-toggle" type="button" data-toggle="dropdown">
                        {{ translate('Bulk Action') }}
                    </button>
                    <div class="dropdown-menu dropdown-menu-right">
                        <a class="dropdown-item" href="javascript:void(0)" onclick="order_bulk_export()">{{ translate('Export') }}</a>
                    </div>
                </div>
                <div class="col-md-3 ml-auto">
                    <select class="form-control aiz-selectpicker"
                        data-placeholder="{{ translate('Filter by Payment Status') }}" name="payment_status"
                        onchange="sort_orders()">
                        <option value="">{{ translate('Filter by Payment Status') }}</option>
                        <option value="paid"
                            @isset($payment_status) @if ($payment_status == 'paid') selected @endif @endisset>
                            {{ translate('Paid') }}</option>
                        <option value="unpaid"
                            @isset($payment_status) @if ($payment_status == 'unpaid') selected @endif @endisset>
                            {{ translate('Unpaid') }}</option>
                    </select>
                </div>

                <div class="col-md-3 ml-auto">
                    <select class="form-control aiz-selectpicker"
                        data-placeholder="{{ translate('Filter by Payment Status') }}" name="delivery_status"
                        onchange="sort_orders()">
                        <option value="">{{ translate('Filter by Deliver Status') }}</option>
                        <option value="pending"
                            @isset($delivery_status) @if ($delivery_status == 'pending') selected @endif @endisset>
                            {{ translate('Pending') }}</option>
                        <option value="confirmed"
                            @isset($delivery_status) @if ($delivery_status == 'confirmed') selected @endif @endisset>
                            {{ translate('Confirmed') }}</option>
                        <option value="on_the_way"
                            @isset($delivery_status) @if ($delivery_status == 'on_the_way') selected @endif @endisset>
                            {{ translate('On The Way') }}</option>
                        <option value="delivered"
                            @isset($delivery_status) @if ($delivery_status == 'delivered') selected @endif @endisset>
                            {{ translate('Delivered') }}</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <div class="from-group mb-0">
                        <input type="text" class="form-control" id="search" name="search"
                            @isset($sort_search) value="{{ $sort_search }}" @endisset
                            placeholder="{{ translate('Type Order code & hit Enter') }}">
                    </div>
                </div>
            </div>


            @if (count($orders) > 0)
                <div class="card-body p-3">
                    <table class="lmt-table">
                        <thead>
                            <tr>
                                <th class="lmt-th-head">
                                    <div class="form-group">
                                        <div class="aiz-checkbox-inline">
                                            <label class="aiz-checkbox">
                                                <input type="checkbox" class="check-all">
                                                <span class="aiz-square-check"></span>
                                            </label>
                                        </div>
                                    </div>
                                </th>
                                <th>{{ translate('Order Code') }}</th>
                                <th data-breakpoints="lg">{{ translate('Num. of Products') }}</th>
                                <th data-breakpoints="lg">{{ translate('Customer') }}</th>
                                <th data-breakpoints="md">{{ translate('Amount') }}</th>
                                <th data-breakpoints="lg">{{ translate('Delivery Status') }}</th>
                                <th data-breakpoints="lg">{{ translate('Payment method') }}</th>
                                <th>{{ translate('Payment Status') }}</th>
                                <th class="text-right">{{ translate('Options') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($orders as $key => $order_id)
                                @php
                                    $order = \App\Models\Order::find($order_id->id);
                                @endphp
                                @if ($order != null)
                                    <tr>
                                        <td >
                                            <div class="form-group">
                                                <div class="aiz-checkbox-inline">
                                                    <label class="aiz-checkbox">
                                                        <input type="checkbox" class="check-one" name="id[]"
                                                            value="{{ $order->id }}">
                                                        <span class="aiz-square-check"></span>
                                                    </label>
                                                </div>
                                            </div>
                                        </td>
                                        <td data-text="{{ translate('Order Code') }}">
                                            <a href="#{{ $order->code }}"
                                                onclick="show_order_details({{ $order->id }})">{{ $order->code }}</a>
                                                <br>
                                             @if($order->seller_process_status == 0 && $order->payment_type == 'cash_on_delivery')
                                                <a href="javascript:void(0)"
                                                class="process-order-btn small text-primary"
                                                data-id="<?= $order->id ?>"
                                                data-order-code="<?= $order->code ?>"
                                                data-amount="<?= $order->grand_total ?>">
                                                 <i class="las la-check-circle"></i> {{ translate('Process Order') }}
                                             </a>
                                            @endif
                                            @if (addon_is_activated('pos_system') && $order->order_from == 'pos')
                                                <span class="badge badge-inline badge-danger">{{ translate('POS') }}</span>
                                            @endif
                                        </td>
                                        <td data-text="{{ translate('Num. of Products') }}">
                                            {{ count($order->orderDetails->where('seller_id', Auth::user()->id)) }}
                                        </td>
                                        <td data-text="{{ translate('Customer') }}">
                                            @if ($order->user_id != null)
                                                {{ optional($order->user)->name }}
                                            @else
                                                {{ translate('Guest') }} ({{ $order->guest_id }})
                                            @endif
                                        </td>
                                        <td data-text="{{ translate('Amount') }}">
                                            {{ single_price($order->grand_total) }}
                                        </td>
                                        <td data-text="{{ translate('Delivery Status') }}">
                                            @php
                                                $status = $order->delivery_status;
                                            @endphp
                                            {{ translate(ucfirst(str_replace('_', ' ', $status))) }}
                                        </td>
                                        <td>
                                            @php
                                                $payment_method = $order->payment_type;
                                            @endphp
                                            {{ translate(ucfirst(str_replace('_', ' ', $payment_method))) }}
                                        </td>
                                        <td>
                                            @if ($order->payment_status == 'paid')
                                                <span class="badge badge-inline badge-success">{{ translate('Paid') }}</span>
                                            @else
                                                <span class="badge badge-inline badge-danger">{{ translate('Unpaid') }}</span>
                                            @endif
                                        </td>
                                        <td class="text-right" data-text="{{ translate('Options') }}">
                                            <div class="">
                                                @if (addon_is_activated('pos_system') && $order->order_from == 'pos')
                                                    <a class="btn btn-soft-success btn-icon btn-circle btn-sm"
                                                        href="{{ route('seller.invoice.thermal_printer', $order->id) }}"
                                                        target="_blank" title="{{ translate('Thermal Printer') }}">
                                                        <i class="las la-print"></i>
                                                    </a>
                                                @endif
                                                <a href="{{ route('seller.orders.show', encrypt($order->id)) }}"
                                                    class="btn btn-soft-info btn-icon btn-circle btn-sm"
                                                    title="{{ translate('Order Details') }}">
                                                    <i class="las la-eye"></i>
                                                </a>
                                                <a href="{{ route('seller.invoice.download', $order->id) }}"
                                                    class="btn btn-soft-warning btn-icon btn-circle btn-sm"
                                                    title="{{ translate('Download Invoice') }}">
                                                    <i class="las la-download"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                    <div class="aiz-pagination">
                        {{ $orders->links() }}
                    </div>
                </div>
            @endif
        </form>
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

        function sort_orders(el) {
            $('#sort_orders').submit();
        }

        function order_bulk_export (){
            var url = '{{route('seller.order-bulk-export')}}';
            $("#sort_orders").attr("action", url);
            $('#sort_orders').submit();
            $("#sort_orders").attr("action", '');
        }
        $(document).on('click', '.process-order-btn', function() {
                var orderId = $(this).data('id');
                var orderCode = $(this).data('order-code');
                var amount = $(this).data('amount');
                var $processBtn = $(this);
                // Find the payment status badge for this order row
                var $paymentStatusBadge = $processBtn.closest('tr').find('td:nth-child(8) span.badge');
                // Find the delivery status badge for this order row
                var $deliveryStatusBadge = $processBtn.closest('tr').find('td:nth-child(7) span.badge');

                // Confirm before processing
                if (confirm('Bạn có chắc bạn muốn xử lý đơn hàng ' + orderCode + ' không?\nĐiều này sẽ đánh dấu đơn đặt hàng là đã được giao.')) {
                    // Show loading indicator
                    $processBtn.html('<i class="las la-spinner la-spin"></i> Processing...');

                    // Send Ajax request
                    $.ajax({
                        url: "{{ route('seller.process-order') }}",
                        type: 'POST',
                        data: {
                            order_id: orderId,
                            amount: amount,
                            _token: '{{ csrf_token() }}'
                        },
                        dataType: 'json',
                        success: function(response) {
                            if (response.success) {
                                // Remove the process button
                                $processBtn.fadeOut(function() {
                                    $(this).remove();
                                });

                                // Update payment status badge to "Paid"
                                $paymentStatusBadge.removeClass('badge-danger').addClass('badge-success');
                                $paymentStatusBadge.text('Paid');

                                // Update delivery status badge to "Delivered"
                                $deliveryStatusBadge.removeClass('badge-warning badge-info badge-primary badge-secondary').addClass('badge-primary');
                                $deliveryStatusBadge.text('Confirmed');

                                // Show success message
                                AIZ.plugins.notify('success', response.message);
                            } else {
                                // Show error message
                                AIZ.plugins.notify('danger', response.message);
                                // Reset button
                                $processBtn.html('<i class="las la-check-circle"></i> Process Order');
                            }
                        },
                        error: function() {
                            // Show error message
                            AIZ.plugins.notify('danger', 'An error occurred while processing the order.');
                            // Reset button
                            $processBtn.html('<i class="las la-check-circle"></i> Process Order');
                        }
                    });
                }
            });
    </script>
@endsection