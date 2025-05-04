<?php

namespace App\Services;

use AizPackages\CombinationGenerate\Services\CombinationService;
use App\Models\ProductStock;
use App\Models\ProductStockPos;
use App\Utility\ProductUtility;
use Illuminate\Support\Facades\Log;

class ProductStockService
{
    public function store(array $data, $product)
    {
        $collection = collect($data);

        $options = ProductUtility::get_attribute_options($collection);

        //Generates the combinations of customer choice options
        $combinations = (new CombinationService())->generate_combination($options);

        $variant = '';
        if (count($combinations) > 0) {
            $product->variant_product = 1;
            $product->save();
            foreach ($combinations as $key => $combination) {
                $str = ProductUtility::get_combination_string($combination, $collection);
                Log::info("str stock: " . $str);
                $product_stock = new ProductStock();
                $product_stock->product_id = $product->id;
                $product_stock->variant = $str;
                $product_stock->price = request()['price_' . str_replace('.', '_', $str)];
                $product_stock->sku = request()['sku_' . str_replace('.', '_', $str)];
                $product_stock->qty = request()['qty_' . str_replace('.', '_', $str)];
                $product_stock->image = request()['img_' . str_replace('.', '_', $str)];
                $product_stock->save();
            }
        } else {
            unset($collection['colors_active'], $collection['colors'], $collection['choice_no']);
            $qty = $collection['current_stock'];
            $price = $collection['unit_price'];
            unset($collection['current_stock']);

            $data = $collection->merge(compact('variant', 'qty', 'price'))->toArray();

            ProductStock::create($data);
        }
    }

    public function store_pos($data, $product)
    {
        $product_stock = new ProductStockPos;
        $product_stock->product_id = $product->id;

        if (isset($data['unit_price']) && $data['unit_price'] != null) {
            $product_stock->price = $data['unit_price'];
        } else {
            $product_stock->price = 0; // Default to 0 instead of null to prevent constraint violation
        }

        $product_stock->variant = '';
        $product_stock->sku = $data['sku'];

        if (!empty($data['current_stock']) || $data['current_stock'] == "0") {
            $product_stock->qty = $data['current_stock'];
        } else {
            $product_stock->qty = 0;
        }

        $product_stock->save();
        return $product_stock;
    }

    public function product_duplicate_store($product_stocks , $product_new)
    {
        foreach ($product_stocks as $key => $stock) {
            $product_stock              = new ProductStock;
            $product_stock->product_id  = $product_new->id;
            $product_stock->variant     = $stock->variant;
            $product_stock->price       = $stock->price;
            $product_stock->sku         = $stock->sku;
            $product_stock->qty         = $stock->qty;
            $product_stock->save();
        }
    }
}
