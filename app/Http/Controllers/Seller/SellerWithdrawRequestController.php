<?php

namespace App\Http\Controllers\Seller;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use App\Notifications\PayoutNotification;
use App\Models\SellerWithdrawRequest;
use App\Models\User;
use App\Utility\EmailUtility;
use Auth;

class SellerWithdrawRequestController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $seller_withdraw_requests = SellerWithdrawRequest::where('user_id', Auth::user()->id)->latest()->paginate(9);
        $total_withdraw_amount = Order::where('seller_id', Auth::user()->id)->where('seller_process_status', 0)->where('payment_type', 'cash_on_delivery')->sum('grand_total');
        $shop = Auth::user()->shop;
        return view('seller.money_withdraw_requests.index', compact('seller_withdraw_requests', 'total_withdraw_amount', 'shop'));
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
        $seller_withdraw_request = new SellerWithdrawRequest;
        $seller_withdraw_request->user_id = $seller->id;
        $seller_withdraw_request->amount = $request->amount;
        $seller_withdraw_request->message = $request->message;
        $seller_withdraw_request->status = '0';
        $seller_withdraw_request->viewed = '0';
        if ($seller_withdraw_request->save()) {
            // // Seller payout request web notification to admin
            // $users = User::findMany(User::where('user_type', 'admin')->first()->id);
            // $data = array();
            // $data['user'] = $seller;
            // $data['amount'] = $request->amount;
            // $data['status'] = 'pending';
            // $data['notification_type_id'] = get_notification_type('seller_payout_request', 'type')->id;
            // Notification::send($users, new PayoutNotification($data));

            // // Seller payout request email to admin & seller
            // $emailIdentifiers = ['seller_payout_request_email_to_admin','seller_payout_request_email_to_seller'];
            // EmailUtility::seller_payout($emailIdentifiers, $seller, $request->amount,  null);

            flash(translate('Request has been sent successfully'))->success();
        }
        else{
            flash(translate('Something went wrong'))->error();
        }
        return back();
    }
}
