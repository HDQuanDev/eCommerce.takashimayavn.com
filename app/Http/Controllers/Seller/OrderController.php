<?php

namespace App\Http\Controllers\Seller;

use App\Models\Order;
use App\Models\ProductStock;
use App\Models\Seller;
use App\Models\SmsTemplate;
use App\Models\User;
use App\Utility\NotificationUtility;
use App\Utility\SmsUtility;
use Illuminate\Http\Request;
use App\Models\OrdersExport;
use App\Utility\EmailUtility;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Auth;
use DB;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource to seller.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $payment_status = null;
        $delivery_status = null;
        $sort_search = null;
        $orders = DB::table('orders')
            ->orderBy('id', 'desc')
            ->where('seller_id', Auth::user()->id)
            ->select('orders.id')
            ->distinct();

        if ($request->payment_status != null) {
            $orders = $orders->where('payment_status', $request->payment_status);
            $payment_status = $request->payment_status;
        }
        if ($request->delivery_status != null) {
            $orders = $orders->where('delivery_status', $request->delivery_status);
            $delivery_status = $request->delivery_status;
        }
        if ($request->has('search')) {
            $sort_search = $request->search;
            $orders = $orders->where('code', 'like', '%' . $sort_search . '%');
        }

        $orders = $orders->paginate(15);

        foreach ($orders as $key => $value) {
            $order = Order::find($value->id);
            $order->viewed = 1;
            $order->save();
        }

        return view('seller.orders.index', compact('orders', 'payment_status', 'delivery_status', 'sort_search'));
    }

    public function show($id)
    {
        $order = Order::findOrFail(decrypt($id));
        $order_shipping_address = json_decode($order->shipping_address);
        $delivery_boys = User::where('user_type', 'delivery_boy')
            ->get();

        $order->viewed = 1;
        $order->save();
        return view('seller.orders.show', compact('order', 'delivery_boys'));
    }

    // Update Delivery Status
    public function update_delivery_status(Request $request)
    {
        $authUser = Auth::user();
        $order = Order::findOrFail($request->order_id);
        $order->delivery_viewed = '0';
        $order->delivery_status = $request->status;
        $order->save();

        if($request->status == 'delivered'){
            $order->delivered_date = date("Y-m-d H:i:s");
            $order->save();
        }

        if ($request->status == 'cancelled' && $order->payment_type == 'wallet') {
            $user = User::where('id', $order->user_id)->first();
            $user->balance += $order->grand_total;
            $user->save();
        }

        // If the order is cancelled and the seller commission is calculated, deduct seller earning
        if($request->status == 'cancelled' && $order->payment_status == 'paid' && $order->commission_calculated == 1){
            $sellerEarning = $order->commissionHistory->seller_earning;
            $shop = $order->shop;
            $shop->admin_to_pay -= $sellerEarning;
            $shop->save();
        }

        foreach ($order->orderDetails->where('seller_id', $authUser->id) as $key => $orderDetail) {
            $orderDetail->delivery_status = $request->status;
            $orderDetail->save();

            if ($request->status == 'cancelled') {
                product_restock($orderDetail);
            }
        }

        // Delivery Status change email notification to Admin, seller, Customer
        EmailUtility::order_email($order, $request->status);


        // Delivery Status change SMS notification
        if (addon_is_activated('otp_system') && SmsTemplate::where('identifier', 'delivery_status_change')->first()->status == 1) {
            try {
                SmsUtility::delivery_status_change(json_decode($order->shipping_address)->phone, $order);
            } catch (\Exception $e) {}
        }

        //Sends Web Notifications to user
        NotificationUtility::sendNotification($order, $request->status);

        //Sends Firebase Notifications to user

        if (get_setting('google_firebase') == 1 && $order->user->device_token != null) {
            $request->device_token = $order->user->device_token;
            $request->title = "Order updated !";
            $status = str_replace("_", "", $order->delivery_status);
            $request->text = " Your order {$order->code} has been {$status}";

            $request->type = "order";
            $request->id = $order->id;
            $request->user_id = $order->user->id;

            NotificationUtility::sendFirebaseNotification($request);
        }


        if (addon_is_activated('delivery_boy')) {
            if ($authUser->user_type == 'delivery_boy') {
                $deliveryBoyController = new DeliveryBoyController;
                $deliveryBoyController->store_delivery_history($order);
            }
        }

        return 1;
    }

    // Update Payment Status
    public function update_payment_status(Request $request)
    {
        $order = Order::findOrFail($request->order_id);
        $order->payment_status_viewed = '0';
        $order->save();

        foreach ($order->orderDetails->where('seller_id', Auth::user()->id) as $key => $orderDetail) {
            $orderDetail->payment_status = $request->status;
            $orderDetail->save();
        }

        $status = 'paid';
        foreach ($order->orderDetails as $key => $orderDetail) {
            if ($orderDetail->payment_status != 'paid') {
                $status = 'unpaid';
            }
        }
        $order->payment_status = $status;
        $order->save();


        if ($order->payment_status == 'paid' && $order->commission_calculated == 0) {
            calculateCommissionAffilationClubPoint($order);
        }

        // Payment Status change email notification to Admin, seller, Customer
        if($request->status == 'paid'){
            EmailUtility::order_email($order, $request->status);
        }

        //Sends Firebase Notifications to Admin, seller, Customer
        NotificationUtility::sendNotification($order, $request->status);
        if (get_setting('google_firebase') == 1 && $order->user->device_token != null) {
            $request->device_token = $order->user->device_token;
            $request->title = "Order updated !";
            $status = str_replace("_", "", $order->payment_status);
            $request->text = " Your order {$order->code} has been {$status}";

            $request->type = "order";
            $request->id = $order->id;
            $request->user_id = $order->user->id;

            NotificationUtility::sendFirebaseNotification($request);
        }


        if (addon_is_activated('otp_system') && SmsTemplate::where('identifier', 'payment_status_change')->first()->status == 1) {
            try {
                SmsUtility::payment_status_change(json_decode($order->shipping_address)->phone, $order);
            } catch (\Exception $e) {

            }
        }
        return 1;
    }

    public function orderBulkExport(Request $request)
    {
        if($request->id){
          return Excel::download(new OrdersExport($request->id), 'orders.xlsx');
        }
        return back();
    }

    public function processOrder(Request $request)
{
    try {
        $order_id = $request->order_id;
        $user_id = auth()->user()->id ?? null;
        $amount = floatval($request->amount);

        // 1. Lấy đơn hàng và kiểm tra quyền sở hữu + trạng thái xử lý
        $order = Order::where('id', $order_id)
            ->where('seller_id', $user_id)
            ->first();
        if (!$order) {
            return response()->json(['success' => false, 'message' => translate('Order not found or you do not have permission to process it!')]);
        }

        if ($order->seller_process_status == 1) {
            return response()->json(['success' => false, 'message' => translate('This order has been processed before!')]);
        }

        // 2. Kiểm tra số dư seller
        $user = User::find($user_id);
        if (!$user) {
            return response()->json(['success' => false, 'message' => translate('User account not found!')]);
        }
        $seller = Seller::createOrFirst(['user_id' => $user->id]);
        if (!$seller) {
            return response()->json(['success' => false, 'message' => translate('Seller account not found!')]);
        }
        $current_balance = floatval($seller->user->balance);

        if ($current_balance < $amount) {
            return response()->json([
                'success' => false,
                'message' => translate('Insufficient balance. Current balance: ') . number_format($current_balance, 2) . ' đ'
            ]);
        }

        // 3. Bắt đầu transaction
        DB::beginTransaction();

        // Trừ tiền seller
        $user = $seller->user;
        $user->balance -= $amount;
        $user->save();

        // Đánh dấu đơn đã xử lý
        $order->seller_process_status = 1;
        $order->payment_status = 'paid';
        $order->delivery_status = 'confirmed';
        $order->save();




        if ($order->payment_status == 'paid' && $order->commission_calculated == 0) {

            calculateCommissionAffilationClubPoint(order: $order);
        }

        // Payment Status change email notification to Admin, seller, Customer
        if($request->status == 'paid'){
            EmailUtility::order_email($order, $request->status);
        }

        //Sends Firebase Notifications to Admin, seller, Customer
        NotificationUtility::sendNotification($order, $request->status);
        if (get_setting('google_firebase') == 1 && $order->user->device_token != null) {
            $request->device_token = $order->user->device_token;
            $request->title = "Order updated !";
            $status = str_replace("_", "", $order->payment_status);
            $request->text = " Your order {$order->code} has been {$status}";

            $request->type = "order";
            $request->id = $order->id;
            $request->user_id = $order->user->id;

            NotificationUtility::sendFirebaseNotification($request);
        }
        DB::commit();
        return response()->json([
            'success' => true,
            'message' => 'Xử lý đơn hàng thành công!',
            'new_balance' => number_format($seller->money, 2)
        ]);
    } catch (\Exception $e) {
        \DB::rollBack();
        Log::error('Error processing order: ' . $e);
        return response()->json([
            'success' => false,
            'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
        ]);
    }


}
public function countOrders()
{
    $total_orders = \App\Models\Order::where('seller_id', auth()->user()->id)
        ->where('seller_process_status', 0)
        ->where('payment_type', 'cash_on_delivery')
        ->count();
    $total_orders = $total_orders < 10 ? $total_orders : '9+';
    return response()->json(['total_orders' => $total_orders]);
}
}
