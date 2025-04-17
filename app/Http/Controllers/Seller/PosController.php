<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Auth;
use Session;
use DB;
use Carbon\Carbon;
use Str;

class PosController extends Controller
{
    public function index(Request $request)
    {
        $categories = Category::where('parent_id', 0)
            ->where('digital', 0)
            ->with('childrenCategories')
            ->get();

        // Lấy sản phẩm từ bảng products_admin thay vì products
        $products = DB::table('products_admin')
            ->where('published', 1)
            ->where('approved', 1);

        if ($request->has('category_id') && $request->category_id != null) {
            $category_ids = Category::where('parent_id', $request->category_id)->pluck('id')->toArray();
            $category_ids[] = $request->category_id;
            $products = $products->whereIn('category_id', $category_ids);
        }

        if ($request->has('search') && $request->search != null) {
            $products = $products->where('name', 'like', '%'.$request->search.'%');
        }

        $products = $products->paginate(16);

        return view('seller.pos.index', compact('categories', 'products'));
    }

    public function addToCart(Request $request)
    {
        $product = DB::table('products_admin')->where('id', $request->id)->first();
        if (!$product) {
            return ['success' => 0, 'message' => 'Sản phẩm không tồn tại'];
        }

        // Lấy thông tin stock từ product_stocks_pos
        $stocks = DB::table('product_stocks_pos')
            ->where('product_id', $product->id)
            ->get();

        if ($stocks->isEmpty()) {
            return ['success' => 0, 'message' => 'Sản phẩm không có thông tin tồn kho'];
        }

        // Tạm thời sử dụng stock đầu tiên (cho sản phẩm không có biến thể)
        $defaultStock = $stocks->first();

        if ($defaultStock->qty <= 0) {
            return ['success' => 0, 'message' => 'Sản phẩm đã hết hàng'];
        }

        $data = array();
        $data['id'] = $product->id;
        $data['name'] = $product->name;
        $data['price'] = $product->unit_price;
        $data['quantity'] = 1;
        $data['max_quantity'] = $defaultStock->qty; // Sử dụng qty từ product_stocks_pos
        $data['stock_id'] = $defaultStock->id; // Lưu ID của stock record
        $data['variant'] = $defaultStock->variant; // Lưu variant
        $data['category_id'] = $product->category_id;
        $data['brand_id'] = $product->brand_id;
        $data['thumbnail_img'] = $product->thumbnail_img;
        $data['photos'] = $product->photos;
        $data['description'] = $product->description;
        $data['attributes'] = $product->attributes;
        $data['choice_options'] = $product->choice_options;
        $data['colors'] = $product->colors;
        $data['variations'] = $product->variations;
        $data['purchase_price'] = $product->purchase_price;

        $cart = Session::get('pos.cart', []);

        // Kiểm tra sản phẩm đã có trong giỏ hàng chưa
        if(isset($cart[$product->id])) {
            return ['success' => 0, 'message' => 'Sản phẩm này đã có trong giỏ hàng'];
        }

        $cart[$product->id] = $data;
        Session::put('pos.cart', $cart);

        return ['success' => 1, 'message' => 'Sản phẩm đã được thêm vào giỏ hàng'];
    }

    public function updateQuantity(Request $request)
    {
        $cart = Session::get('pos.cart', []);

        if(isset($cart[$request->id])) {
            // Kiểm tra số lượng tồn kho trong product_stocks_pos
            $stock = DB::table('product_stocks_pos')
                ->where('id', $cart[$request->id]['stock_id'])
                ->first();

            if (!$stock) {
                return ['success' => 0, 'message' => 'Không tìm thấy thông tin tồn kho của sản phẩm'];
            }

            if ($request->quantity > $stock->qty) {
                return ['success' => 0, 'message' => 'Số lượng yêu cầu vượt quá số lượng hiện có (' . $stock->qty . ')'];
            }

            $cart[$request->id]['quantity'] = max(1, min($request->quantity, $stock->qty));
            $cart[$request->id]['max_quantity'] = $stock->qty;
            Session::put('pos.cart', $cart);
            return ['success' => 1, 'message' => 'Số lượng đã được cập nhật'];
        }

        return ['success' => 0, 'message' => 'Sản phẩm không có trong giỏ hàng'];
    }

    public function removeFromCart(Request $request)
    {
        $cart = Session::get('pos.cart', []);

        if(isset($cart[$request->id])) {
            unset($cart[$request->id]);
            Session::put('pos.cart', $cart);
            return ['success' => 1, 'message' => 'Sản phẩm đã được xóa khỏi giỏ hàng'];
        }

        return ['success' => 0, 'message' => 'Sản phẩm không có trong giỏ hàng'];
    }

    public function getCart()
    {
        $cart = Session::get('pos.cart', []);
        return view('seller.pos.cart', compact('cart'));
    }

    public function setDiscount(Request $request)
    {
        Session::put('pos.discount', $request->discount);
        return ['success' => 1];
    }

    public function setShipping(Request $request)
    {
        Session::put('pos.shipping', $request->shipping);
        return ['success' => 1];
    }

    public function checkout(Request $request)
    {
        $cart = Session::get('pos.cart', []);

        if(count($cart) == 0) {
            return ['success' => 0, 'message' => 'Giỏ hàng trống'];
        }

        $seller = Auth::user();

        // Kiểm tra giới hạn sản phẩm từ bảng shops
        $shop = DB::table('shops')->where('user_id', $seller->id)->first();

        if($shop) {
            $productCount = Product::where('user_id', $seller->id)->where('added_by', 'seller')->count();
            $uploadLimit = $shop->product_upload_limit;

            if($productCount + count($cart) > $uploadLimit) {
                return ['success' => 0,
                        'message' => 'Giới hạn tải lên của bạn là '.$uploadLimit.' sản phẩm. Hiện tại bạn có '.$productCount.' sản phẩm. Không thể thêm '.count($cart).' sản phẩm nữa.'];
            }
        } else {
            return ['success' => 0, 'message' => 'Bạn cần có cửa hàng trước khi thêm sản phẩm.'];
        }

        // Kiểm tra số lượng hàng trong kho của admin
        foreach($cart as $key => $cartItem) {
            $adminProduct = DB::table('products_admin')->where('id', $cartItem['id'])->first();
            if (!$adminProduct) {
                return ['success' => 0, 'message' => 'Sản phẩm không tồn tại'];
            }

            // Kiểm tra số lượng trong product_stocks_pos
            $productStock = DB::table('product_stocks_pos')
                ->where('id', $cartItem['stock_id'])
                ->first();

            if (!$productStock || $productStock->qty < $cartItem['quantity']) {
                $availableQty = $productStock ? $productStock->qty : 0;
                return ['success' => 0, 'message' => 'Sản phẩm "' . $adminProduct->name . '" chỉ còn ' . $availableQty . ' sản phẩm trong kho'];
            }
        }

        DB::beginTransaction();
        try {
            foreach($cart as $key => $cartItem) {
                $adminProduct = DB::table('products_admin')->where('id', $cartItem['id'])->first();
                if (!$adminProduct) {
                    continue;
                }

                $slug = Str::slug($cartItem['name']);

                // Kiểm tra và tạo slug duy nhất
                if(Product::where('slug', $slug)->exists()) {
                    $slug = $slug.'-'.Str::random(5);
                }

                // Tạo sản phẩm mới cho seller và copy tất cả thông tin từ products_admin
                $product = new Product;
                // Copy tất cả các trường từ products_admin sang products
                foreach($adminProduct as $field => $value) {
                    if ($field != 'id') {
                        $product->{$field} = $value;
                    }
                }

                // Ghi đè một số trường cụ thể
                $product->added_by = 'seller';
                $product->user_id = $seller->id;
                $product->current_stock = $cartItem['quantity'];
                $product->num_of_sale = 0;
                $product->rating = 0;
                $product->slug = $slug;
                $product->created_at = now();
                $product->updated_at = now();

                $product->save();

                // Clone dữ liệu từ product_categories
                $productCategories = DB::table('product_categories_pos')
                    ->where('product_id', $adminProduct->id)
                    ->get();

                foreach ($productCategories as $category) {
                    DB::table('product_categories')->insert([
                        'product_id' => $product->id,
                        'category_id' => $category->category_id
                    ]);
                }

                // Clone dữ liệu từ product_stocks
                $productStocks = DB::table('product_stocks_pos')
                    ->where('product_id', $adminProduct->id)
                    ->get();

                foreach ($productStocks as $stock) {
                    $stockData = (array) $stock;
                    unset($stockData['id']);
                    $stockData['product_id'] = $product->id;

                    // Nếu đây là stock record đang được mua, cập nhật số lượng
                    if($stock->id == $cartItem['stock_id']) {
                        $stockData['qty'] = $cartItem['quantity'];
                    } else {
                        $stockData['qty'] = 0; // Các biến thể không được mua sẽ có số lượng 0
                    }

                    $stockData['created_at'] = now();
                    $stockData['updated_at'] = now();

                    DB::table('product_stocks')->insert($stockData);
                }

                // Clone dữ liệu từ product_taxes
                $productTaxes = DB::table('product_taxes_pos')
                    ->where('product_id', $adminProduct->id)
                    ->get();

                foreach ($productTaxes as $tax) {
                    $taxData = (array) $tax;
                    unset($taxData['id']);
                    $taxData['product_id'] = $product->id;
                    $taxData['created_at'] = now();
                    $taxData['updated_at'] = now();

                    DB::table('product_taxes')->insert($taxData);
                }

                // Clone dữ liệu từ product_translations
                $productTranslations = DB::table('product_translations_pos')
                    ->where('product_id', $adminProduct->id)
                    ->get();

                foreach ($productTranslations as $translation) {
                    $translationData = (array) $translation;
                    unset($translationData['id']);
                    $translationData['product_id'] = $product->id;
                    $translationData['created_at'] = now();
                    $translationData['updated_at'] = now();

                    DB::table('product_translations')->insert($translationData);
                }

                // Cập nhật số lượng hàng còn lại của admin sau khi seller mua
                DB::table('product_stocks_pos')
                    ->where('id', $cartItem['stock_id'])
                    ->update([
                        'qty' => DB::raw('qty - ' . $cartItem['quantity']),
                        'updated_at' => now()
                    ]);
            }

            // Xóa giỏ hàng sau khi thêm sản phẩm thành công
            Session::forget('pos.cart');
            Session::forget('pos.discount');
            Session::forget('pos.shipping');

            DB::commit();

            return ['success' => 1, 'message' => 'Các sản phẩm đã được thêm thành công'];

        } catch(\Exception $e) {
            DB::rollback();
            return ['success' => 0, 'message' => 'Có lỗi xảy ra: ' . $e->getMessage()];
        }
    }
}
