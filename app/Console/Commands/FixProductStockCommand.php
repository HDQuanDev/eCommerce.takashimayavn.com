<?php

namespace App\Console\Commands;

use App\Jobs\FixPOSJob;
use App\Jobs\FixProductStockJob;
use App\Models\ProductStock;
use Illuminate\Console\Command;

class FixProductStockCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fix-product-stock';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $productStocks = ProductStock::where('variant' , '')->orWhere('variant' , null)->get();
        $this->info('Total product stock: ' . $productStocks->count());
        foreach ($productStocks as $productStock) {
            dispatch(new FixProductStockJob($productStock));
        }
    }
}
