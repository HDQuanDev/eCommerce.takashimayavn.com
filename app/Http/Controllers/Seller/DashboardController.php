<?php

namespace App\Http\Controllers\Seller;

use App\Models\CommissionHistory;
use App\Models\Order;
use App\Models\OrderDetail;
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

        $data['this_month_commission'] = CommissionHistory::whereHas('order', function ($q) use ($authUserId) {
            $q->where('seller_id', $authUserId)
                ->where('delivery_status', 'delivered')
                ->whereYear('created_at', Carbon::now()->year)
                ->whereMonth('created_at', Carbon::now()->month);
        })->sum('admin_commission');

        $data['previous_month_commission'] = CommissionHistory::whereHas('order', function ($q) use ($authUserId) {
            $q->where('seller_id', $authUserId)
                ->where('delivery_status', 'delivered')
                ->whereYear('created_at', Carbon::now()->year)
                ->whereMonth('created_at', Carbon::now()->month - 1);
        })->sum('admin_commission');

        $data['products'] = filter_products(ProductPos::where('user_id', Auth::user()->id)->orderBy('num_of_sale', 'desc'))->limit(12)->get();
        $data['last_7_days_sales'] = Order::where('created_at', '>=', Carbon::now()->subDays(7))
            ->where('seller_id', '=', Auth::user()->id)
            ->where('delivery_status', '=', 'delivered')
            ->select(DB::raw("sum(grand_total) as total, DATE_FORMAT(created_at, '%d %b') as date"))
            ->groupBy(DB::raw("DATE_FORMAT(created_at, '%Y-%m-%d')"))
            ->get()->pluck('total', 'date');

        $data['total_order'] = Order::where('seller_id', Auth::user()->id)->count();

        $total_sales = OrderDetail::whereHas('order', function ($q) use ($authUserId) {
            $q->where('seller_id', $authUserId)
                ->where('delivery_status', 'delivered');
        })
            ->selectRaw('SUM(price * quantity) as total')
            ->value('total');

        $data['total_sales'] = $total_sales;
        return view('seller.dashboard', $data);
    }

    public function countSidebarNotification()
    {
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
