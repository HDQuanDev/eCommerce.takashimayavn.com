<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use Illuminate\Http\Request;
use App\Models\SellerWithdrawRequest;
use App\Models\User;
use Auth;

class SellerWithdrawRequestController extends Controller
{
    public function __construct()
    {
        // Staff Permission Check
        $this->middleware(['permission:view_seller_payout_requests'])->only('index');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function index()
    {
        $seller_withdraw_requests = SellerWithdrawRequest::latest()->paginate(15);
        return view('backend.sellers.seller_withdraw_requests.index', compact('seller_withdraw_requests'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $seller_withdraw_request = new SellerWithdrawRequest;
        $seller_withdraw_request->user_id = Auth::user()->shop->id;
        $seller_withdraw_request->amount = $request->amount;
        $seller_withdraw_request->message = $request->message;
        $seller_withdraw_request->status = '0';
        $seller_withdraw_request->viewed = '0';
        if ($seller_withdraw_request->save()) {
            flash(translate('Request has been sent successfully'))->success();
            return redirect()->route('withdraw_requests.index');
        } else {
            flash(translate('Something went wrong'))->error();
            return back();
        }
    }

    public function approve(Request $request)
    {
        $seller_withdraw_request = SellerWithdrawRequest::where('id', $request->seller_withdraw_request_id)->first();
        $seller_withdraw_request->reply = $request->reply;
        $seller_withdraw_request->status = '1';
        $seller_withdraw_request->viewed = '1';
        $seller_withdraw_request->save();

        $user = User::findOrFail($seller_withdraw_request->user_id);

        if ($user->balance >= $seller_withdraw_request->amount) {
            $user->balance -= $seller_withdraw_request->amount;
            $user->save();
        } else {
            flash(translate('Insufficient balance'))->error();
            return back();
        }

        $payment = new Payment();
        $payment->seller_id = $user->id;
        $payment->amount = $seller_withdraw_request->amount;
        $payment->payment_method = 'Withdraw';
        $payment->save();

        flash(translate('Request has been approved successfully'))->success();
        return back();
    }

    public function reject(Request $request)
    {
        $seller_withdraw_request = SellerWithdrawRequest::where('id', $request->seller_withdraw_request_id)->first();
        $seller_withdraw_request->reply = $request->reply;
        $seller_withdraw_request->status = '2';
        $seller_withdraw_request->viewed = '1';
        $seller_withdraw_request->save();

        flash(translate('Request has been rejected successfully'))->success();
        return back();
    }

    public function payment_modal(Request $request)
    {
        $user = User::findOrFail($request->id);
        $seller_withdraw_request = SellerWithdrawRequest::where('id', $request->seller_withdraw_request_id)->first();
        return view('backend.sellers.seller_withdraw_requests.payment_modal', compact('user', 'seller_withdraw_request'));
    }

    public function message_modal(Request $request)
    {
        $seller_withdraw_request = SellerWithdrawRequest::findOrFail($request->id);
        if (Auth::user()->user_type == 'seller') {
            return view('frontend.partials.withdraw_message_modal', compact('seller_withdraw_request'));
        } elseif (Auth::user()->user_type == 'admin' || Auth::user()->user_type == 'staff') {
            return view('backend.sellers.seller_withdraw_requests.withdraw_message_modal', compact('seller_withdraw_request'));
        }
    }
}
