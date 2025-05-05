<?php

namespace App\Jobs;

use AizPackages\CombinationGenerate\Services\CombinationService;
use App\Models\ProductPos;
use App\Models\ProductStockPos;
use App\Utility\ProductUtility;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class FixPOSJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;

    public $productStockPos;

    /**
     * Create a new job instance.
     */
    public function __construct($productStockPos)
    {
        $this->productStockPos = $productStockPos;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $oldProductStockPos = $this->productStockPos;
            $productPos = ProductPos::where('id', $oldProductStockPos->product_id)->first();
            $attributes = json_decode($productPos->attributes, true);
            $colors = json_decode($productPos->colors, true);
            $choice_options = json_decode($productPos->choice_options, true);

            $data = [
                'colors_active' => count($colors) > 0 ? 1 : 0,
                'colors' => $colors,
                'choice_no' => count($attributes) > 0 ? array_keys(array_fill(0, count($attributes), 0)) : [],
                'unit_price' => $oldProductStockPos->price,
                'current_stock' => 999,
                'sku' => null,
                'product_id' => $oldProductStockPos->product_id,
            ];

            foreach ($data['choice_no'] as $key => $no) {
                $name = 'choice_options_' . $no;
                $choiceOptions = $choice_options[$key]['values'];
                $data[$name] = $choiceOptions;
            }

            $collection = collect($data);

            $options = self::get_attribute_options($collection);

            //Generates the combinations of customer choice options
            $combinations = (new CombinationService())->generate_combination($options);

            $variant = '';
            if (count($combinations) > 0) {
                foreach ($combinations as $key => $combination) {
                    $str = ProductUtility::get_combination_string($combination, $collection);
                    $product_stock = new ProductStockPos();
                    $product_stock->product_id = $productPos->id;
                    $product_stock->variant = $str;
                    $product_stock->price = $data['unit_price'];
                    $product_stock->sku = $data['sku'];
                    $product_stock->qty = $data['current_stock'];
                    $product_stock->image = null;
                    $product_stock->save();
                }
            } else {
                unset($collection['colors_active'], $collection['colors'], $collection['choice_no']);
                $qty = $collection['current_stock'];
                $price = $collection['unit_price'];
                unset($collection['current_stock']);

                $data = $collection->merge(compact('variant', 'qty', 'price'))->toArray();

                ProductStockPos::create($data);
            }

            ProductStockPos::where('id', $oldProductStockPos->id)->delete();
        } catch (\Throwable $th) {
            Log::error($th);
        }
    }

    public static function get_attribute_options($collection)
    {
        $options = array();
        if (
            isset($collection['colors_active']) &&
            $collection['colors_active'] &&
            isset($collection['colors']) &&
            is_array($collection['colors']) &&
            count($collection['colors']) > 0
        ) {
            array_push($options, $collection['colors']);
        }

        if (isset($collection['choice_no']) && $collection['choice_no']) {
            foreach ($collection['choice_no'] as $key => $no) {
                $name = 'choice_options_' . $no;
                $data = array();
                if ($collection[$name] && is_array($collection[$name])) {
                    foreach ($collection[$name] as $key => $eachValue) {
                        array_push($data, $eachValue);
                    }
                }
                array_push($options, $data);
            }
        }

        return $options;
    }
}
