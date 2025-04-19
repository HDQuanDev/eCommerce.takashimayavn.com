<?php

namespace App\Http\Controllers\Seller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use App\Notifications\PayoutNotification;
use App\Models\SellerDepositRequest;
use App\Models\User;
use App\Models\V2PaymentMethod;
use App\Utility\EmailUtility;
use Auth;

class SellerDepositRequestController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $seller_deposit_requests = SellerDepositRequest::where('user_id', Auth::user()->id)->latest()->paginate(9);
        $total_deposit_amount = SellerDepositRequest::where('user_id', Auth::user()->id)->sum('amount');
        $payment_methods = V2PaymentMethod::where('active', 1)->get();
        return view('seller.money_deposit_requests.index', compact('seller_deposit_requests', 'total_deposit_amount', 'payment_methods'));
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $seller = auth()->user();
        $seller_deposit_request = new SellerDepositRequest;
        $seller_deposit_request->user_id = $seller->id;
        $seller_deposit_request->amount = $request->amount;
        $seller_deposit_request->message = $request->message;
        $seller_deposit_request->status = '0';
        $seller_deposit_request->viewed = '0';
        $seller_deposit_request->payment_method_id = $request->payment_method_id;

        if ($seller_deposit_request->save()) {

            // Seller payout request web notification to admin
            $users = User::findMany(User::where('user_type', 'admin')->first()->id);
            $data = array();
            $data['user'] = $seller;
            $data['amount'] = $request->amount;
            $data['status'] = 'pending';
            $data['notification_type_id'] = get_notification_type('seller_deposit_request', 'type')->id;
            Notification::send($users, new PayoutNotification($data));

            // Seller payout request email to admin & seller
            $emailIdentifiers = ['seller_deposit_request_email_to_admin', 'seller_deposit_request_email_to_seller'];
            EmailUtility::seller_payout($emailIdentifiers, $seller, $request->amount,  V2PaymentMethod::find($request->payment_method_id)->card_name);

            flash(translate('Request has been sent successfully'))->success();
            return redirect()->route('seller.money_deposit_requests.index');
        } else {
            flash(translate('Something went wrong'))->error();
            return back();
        }
    }
}
