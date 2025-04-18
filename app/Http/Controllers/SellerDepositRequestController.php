<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SellerDepositRequest;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class SellerDepositRequestController extends Controller
{
    public function __construct()
    {
        // Staff Permission Check
        $this->middleware(['permission:view_seller_deposit_requests'])->only('index');
    }
    public function index()
    {
        $seller_deposit_requests = SellerDepositRequest::latest()->paginate(15);
        return view('backend.sellers.seller_deposit_requests.index', compact('seller_deposit_requests'));
    }

    public function store(Request $request)
    {
        $seller_deposit_request = new SellerDepositRequest;
        $seller_deposit_request->user_id = Auth::user()->shop->id;
        $seller_deposit_request->amount = $request->amount;
        $seller_deposit_request->message = $request->message;
        $seller_deposit_request->status = '0';
        $seller_deposit_request->viewed = '0';
        if ($seller_deposit_request->save()) {
            flash(translate('Request has been sent successfully'))->success();
            return redirect()->route('deposit_requests.index');
        } else {
            flash(translate('Something went wrong'))->error();
            return back();
        }
    }

    public function approve(Request $request)
    {
        $seller_deposit_request = SellerDepositRequest::where('id', $request->seller_deposit_request_id)->first();
        $seller_deposit_request->status = '1';
        $user = User::findOrFail($seller_deposit_request->user_id);
        $user->balance += $seller_deposit_request->amount;

        if ($seller_deposit_request->save() && $user->save()) {
            flash(translate('Request has been approved successfully'))->success();
            return redirect()->route('deposit_requests_all');
        } else {
            flash(translate('Something went wrong'))->error();
            return back();
        }
    }

    public function reject(Request $request)
    {
        $seller_deposit_request = SellerDepositRequest::where('id', $request->seller_deposit_request_id)->first();
        $seller_deposit_request->status = '2';

        if ($seller_deposit_request->save()) {
            flash(translate('Request has been rejected successfully'))->success();
            return redirect()->route('deposit_requests_all');
        } else {
            flash(translate('Something went wrong'))->error();
            return back();
        }
    }

    public function message_modal(Request $request)
    {
        $seller_deposit_request = SellerDepositRequest::findOrFail($request->id);
        if (Auth::user()->user_type == 'seller') {
            return view('frontend.partials.deposit_message_modal', compact('seller_deposit_request'));
        } elseif (Auth::user()->user_type == 'admin' || Auth::user()->user_type == 'staff') {
            return view('backend.sellers.seller_deposit_requests.deposit_message_modal', compact('seller_deposit_request'));
        }
    }
}
