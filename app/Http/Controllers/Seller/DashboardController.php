<?php

namespace App\Http\Controllers\Seller;

use App\Models\CommissionHistory;
use App\Models\Order;
use App\Models\ProductPos;
use App\Models\Review;
use Auth;
use Carbon\Carbon;
use DB;

class DashboardController extends Controller
{
    public function index()
    {
        $authUserId = auth()->user()->id;
        $data['this_month_pending_orders'] = Order::whereSellerId($authUserId)
            ->whereDeliveryStatus('pending')
            ->whereYear('created_at', Carbon::now()->year)
            ->whereMonth('created_at', Carbon::now()->month)
            ->count();
        $data['this_month_cancelled_orders'] = Order::whereSellerId($authUserId)
            ->whereDeliveryStatus('cancelled')
            ->whereYear('created_at', Carbon::now()->year)
            ->whereMonth('created_at', Carbon::now()->month)
            ->count();
        $data['this_month_on_the_way_orders'] = Order::whereSellerId($authUserId)
            ->whereDeliveryStatus('on_the_way')
            ->whereYear('created_at', Carbon::now()->year)
            ->whereMonth('created_at', Carbon::now()->month)
            ->count();
        $data['this_month_delivered_orders'] = Order::whereSellerId($authUserId)
            ->whereDeliveryStatus('delivered')
            ->whereYear('created_at', Carbon::now()->year)
            ->whereMonth('created_at', Carbon::now()->month)
            ->count();

        $data['this_month_sold_amount'] = Order::where('seller_id', Auth::user()->id)
            ->wherePaymentStatus('paid')
            ->whereYear('created_at', Carbon::now()->year)
            ->whereMonth('created_at', Carbon::now()->month)
            ->sum('grand_total');
        $data['previous_month_sold_amount'] = Order::where('seller_id', Auth::user()->id)
            ->wherePaymentStatus('paid')
            ->whereYear('created_at', Carbon::now()->year)
            ->whereMonth('created_at', (Carbon::now()->month - 1))
            ->sum('grand_total');

        $data['products'] = filter_products(ProductPos::where('user_id', Auth::user()->id)->orderBy('num_of_sale', 'desc'))->limit(12)->get();
        $data['last_7_days_sales'] = Order::where('created_at', '>=', Carbon::now()->subDays(7))
            ->where('seller_id', '=', Auth::user()->id)
            ->where('delivery_status', '=', 'delivered')
            ->select(DB::raw("sum(grand_total) as total, DATE_FORMAT(created_at, '%d %b') as date"))
            ->groupBy(DB::raw("DATE_FORMAT(created_at, '%Y-%m-%d')"))
            ->get()->pluck('total', 'date');


        $data['total_order'] = Order::where('seller_id', Auth::user()->id)->count();
        // doanh số
        $orders_commission_this_month = Order::where('seller_id', $authUserId)
            ->where('delivery_status', 'delivered')
            ->whereYear('created_at', Carbon::now()->year)
            ->whereMonth('created_at', Carbon::now()->month)
            ->with('commissionHistory')
            ->get();
        $orders_commission = Order::where('seller_id', $authUserId)
            ->where('delivery_status', 'delivered')
            ->with('commissionHistory')
            ->get();

        $commission_this_month = 0;
        $total_sales = 0;
        foreach ($orders_commission_this_month as $order) {
            $value = CommissionHistory::where('order_id', $order->id)->sum('admin_commission');
            $commission_this_month += $value;

        }
        foreach ($orders_commission as $order) {
            if($order->payment_type == 'cash_on_delivery') {
                $total_sales += $order->commissionHistory?->admin_commission + $order->commissionHistory?->seller_earning ?? 0;
            }else {
                $total_sales += $order->commissionHistory?->admin_commission ?? 0;
            }
        }
        $data['commission_this_month'] = $commission_this_month;
        $data['total_sales'] = $total_sales;
        return view('seller.dashboard', $data);
    }

    public function countSidebarNotification() {
        $conversations = get_seller_message_count();
        $total_orders = \App\Models\Order::where('seller_id', auth()->user()->id)
        ->where('seller_process_status', 0)
        ->where('payment_type', 'cash_on_delivery')
        ->count();
    $total_orders = $total_orders < 10 ? $total_orders : '9+';

    $reviews_count = Review::whereHas('product', function ($query) {
        $query->where('user_id', auth()->user()->id);
    })->where('viewed', 0)->count();
    $reviews_count = $reviews_count < 10 ? $reviews_count : '9+';
        return response()->json([
            'conversations' => $conversations,
            'total_orders' => $total_orders,
            'total_reviews' => $reviews_count
        ]);
    }
}
