@extends('backend.layouts.app')

@section('content')
<div class="card">
    <div class="card-header">
        <h5 class="mb-0 h6">{{translate('Seller Packages Payments')}}</h5>
    </div>
    <div class="card-body">
        <table class="table aiz-table mb-0">
            <thead>
                <tr>
                    <th>#</th>
                    <th>{{translate('Seller')}}</th>
                    <th>{{translate('Package')}}</th>
                    <th>{{translate('Amount')}}</th>
                    <th>{{translate('Payment Method')}}</th>
                    <th>{{translate('Payment Status')}}</th>
                    <th>{{translate('Date')}}</th>
                    <th class="text-right">{{translate('Options')}}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($package_payments as $key => $payment)
                    <tr>
                        <td>{{ ($key+1) + ($package_payments->currentPage() - 1)*$package_payments->perPage() }}</td>
                        <td>
                            @if($payment->user != null)
                                {{ $payment->user->name }}
                            @endif
                        </td>
                        <td>
                            @if($payment->seller_package != null)
                                {{ $payment->seller_package->name }}
                            @endif
                        </td>
                        <td>
                            @if($payment->seller_package != null)
                                {{ format_price($payment->seller_package->amount) }}
                            @endif
                        </td>
                        <td>{{ ucfirst(str_replace('_', ' ', $payment->payment_method)) }}</td>
                        <td>
                            @if ($payment->approval == 0)
                                <span class="badge badge-inline badge-info">{{translate('Pending')}}</span>
                            @elseif ($payment->approval == 1)
                                <span class="badge badge-inline badge-success">{{translate('Approved')}}</span>
                            @endif
                        </td>
                        <td>{{ $payment->created_at->diffForHumans() }}</td>
                        <td class="text-right">
                            @if ($payment->offline_payment && $payment->approval == 0)
                                <a href="#" class="btn btn-sm btn-success confirm-approve" data-href="{{route('offline_seller_package_payments.approve', $payment->id)}}" title="{{ translate('Approve') }}">
                                    {{translate('Approve')}}
                                </a>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div class="aiz-pagination">
            {{ $package_payments->appends(request()->input())->links() }}
        </div>
    </div>
</div>
@endsection

@section('modal')
    <div class="modal fade" id="confirm-approve">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title h6">{{translate('Confirmation')}}</h5>
                    <button type="button" class="close" data-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>{{translate('Do you want to approve this payment?')}}</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-dismiss="modal">{{translate('Cancel')}}</button>
                    <a type="button" id="approve-link" class="btn btn-primary">{{translate('Approve')}}</a>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script type="text/javascript">
        $(document).on("click", ".confirm-approve", function(e){
            e.preventDefault();
            $("#approve-link").attr("href", $(this).data("href"));
            $("#confirm-approve").modal("show");
        });
    </script>
@endsection
