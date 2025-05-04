<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SellerPackage;
use App\Models\SellerPackagePayment;
use App\Models\Payment;
use App\Models\Seller;
use App\Models\Shop;
use App\Models\User;
use Str;
use Auth;
use Validator;

class SellerPackageController extends Controller
{
    public function index()
    {
        $seller_packages = SellerPackage::all();
        return view('backend.seller_packages.index', compact('seller_packages'));
    }

    public function create()
    {
        return view('backend.seller_packages.create');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'amount' => 'required|numeric|min:0',
            'product_upload_limit' => 'required|numeric|min:0',
            'duration' => 'required|numeric|min:0',
            'logo' => 'nullable|integer'
        ]);

        if ($validator->fails()) {
            flash(translate('Something went wrong'))->error();
            return redirect()->back()->withErrors($validator);
        }

        $seller_package = new SellerPackage;
        $seller_package->name = $request->name;
        $seller_package->amount = $request->amount;
        $seller_package->product_upload_limit = $request->product_upload_limit;
        $seller_package->duration = $request->duration;
        $seller_package->logo = $request->logo;
        $seller_package->status = 'active';

        if ($seller_package->save()) {
            flash(translate('Package has been inserted successfully'))->success();
            return redirect()->route('seller_packages.index');
        } else {
            flash(translate('Something went wrong'))->error();
            return back();
        }
    }

    public function show($id)
    {
        //
    }

    public function edit(Request $request, $id)
    {
        $seller_package = SellerPackage::findOrFail($id);

        return view('backend.seller_packages.edit', compact('seller_package'));
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'amount' => 'required|numeric|min:0',
            'product_upload_limit' => 'required|numeric|min:0',
            'duration' => 'required|numeric|min:0',
            'logo' => 'nullable|integer'
        ]);

        if ($validator->fails()) {
            flash(translate('Something went wrong'))->error();
            return redirect()->back()->withErrors($validator);
        }

        $seller_package = SellerPackage::findOrFail($id);
        $seller_package->name = $request->name;
        $seller_package->amount = $request->amount;
        $seller_package->product_upload_limit = $request->product_upload_limit;
        $seller_package->duration = $request->duration;
        $seller_package->logo = $request->logo;

        if ($seller_package->save()) {
            flash(translate('Package has been updated successfully'))->success();
            return redirect()->route('seller_packages.index');
        } else {
            flash(translate('Something went wrong'))->error();
            return back();
        }
    }

    public function destroy($id)
    {
        SellerPackage::destroy($id);
        flash(translate('Package has been deleted successfully'))->success();
        return redirect()->route('seller_packages.index');
    }

    public function update_status(Request $request)
    {
        $seller_package = SellerPackage::findOrFail($request->id);
        $seller_package->status = $request->status;

        if ($seller_package->save()) {
            return 1;
        }
        return 0;
    }

    // For Seller Package Purchase History
    public function purchase_history(Request $request)
    {
        $package_payments = SellerPackagePayment::orderBy('id', 'desc')->paginate(15);
        return view('backend.seller_packages.payment_history', compact('package_payments'));
    }

    // For Seller Package Purchase
    public function seller_packages_list()
    {
        $seller_packages = SellerPackage::where('status', 'active')->get();

        // Get purchase history for current seller
        $package_history = SellerPackagePayment::where('user_id', Auth::user()->id)
            ->orderBy('created_at', 'desc')
            ->with('seller_package') // Make sure you have this relationship defined in the model
            ->paginate(10);

        return view('seller.seller_packages.packages', compact('seller_packages', 'package_history'));
    }

    public function purchase_package(Request $request)
    {
        $shop = Shop::where('user_id', Auth::user()->id)->first();
        $seller_package = SellerPackage::findOrFail($request->seller_package_id);
        $user = Auth::user();

        // Kiểm tra số dư người bán có đủ để mua gói không
        if ($seller_package->amount > $user->balance) {
            flash(translate('Bạn không đủ số dư để mua gói này!'))->error();
            return back();
        }

        // Trừ số tiền từ tài khoản người bán
        $user->balance -= $seller_package->amount;

        // Cập nhật gói người bán
        $shop->seller_package_id = $seller_package->id;
        $shop->product_upload_limit = $seller_package->product_upload_limit;

        // Cập nhật thời hạn gói
        if ($shop->package_invalid_at != null) {
            // Nếu người bán đang có gói, thêm thời hạn vào gói hiện tại
            $shop->package_invalid_at = date('Y-m-d', strtotime($shop->package_invalid_at . ' +' . $seller_package->duration . ' days'));
        } else {
            // Nếu người bán chưa có gói, thiết lập thời hạn mới
            $shop->package_invalid_at = date('Y-m-d', strtotime('+' . $seller_package->duration . ' days'));
        }

        $shop->save();

        // Tạo mã giao dịch ngẫu nhiên
        $txn_code = 'PKG-' . Str::random(8) . '-' . date('YmdHis');

        // Lưu lịch sử thanh toán vào bảng payments
        $payment = new Payment;
        $payment->seller_id = Auth::user()->id;
        $payment->amount = $seller_package->amount;
        $payment->payment_method = 'wallet';
        $payment->txn_code = $txn_code;
        $payment->payment_details = json_encode([
            'package_id' => $seller_package->id,
            'package_name' => $seller_package->name,
            'amount' => $seller_package->amount,
            'product_upload_limit' => $seller_package->product_upload_limit,
            'duration' => $seller_package->duration,
            'payment_type' => 'package_purchase',
            'package_expire_at' => $shop->package_invalid_at
        ]);
        $payment->save();
        $user->save();

        // Tạo lịch sử giao dịch gói
        $seller_package_payment = new SellerPackagePayment;
        $seller_package_payment->user_id = Auth::user()->id;
        $seller_package_payment->seller_package_id = $seller_package->id;
        $seller_package_payment->payment_method = 'wallet';
        $seller_package_payment->payment_details = 'Đã thanh toán từ số dư balance';
        $seller_package_payment->txn_code = $txn_code;  // Lưu cùng mã giao dịch để có thể liên kết
        $seller_package_payment->approval = 1; // Đã được chấp nhận tự động
        $seller_package_payment->offline_payment = 0;
        $seller_package_payment->save();

        flash(translate('Gói đã được mua thành công.'))->success();
        return back();
    }

    public function approve_offline_payment(Request $request)
    {
        $package_payment = SellerPackagePayment::findOrFail($request->id);
        $package_payment->approval = 1;

        if ($package_payment->save()) {
            $shop = Shop::where('user_id', $package_payment->user_id)->first();
            $seller_package = SellerPackage::findOrFail($package_payment->seller_package_id);

            if ($shop) {
                $shop->seller_package_id = $package_payment->seller_package_id;
                $shop->product_upload_limit = $seller_package->product_upload_limit;

                if ($shop->package_invalid_at != null) {
                    $shop->package_invalid_at = date('Y-m-d', strtotime($shop->package_invalid_at . ' +' . $seller_package->duration . ' days'));
                } else {
                    $shop->package_invalid_at = date('Y-m-d', strtotime('+' . $seller_package->duration . ' days'));
                }

                $shop->save();
            }

            return 1;
        }
        return 0;
    }
}
