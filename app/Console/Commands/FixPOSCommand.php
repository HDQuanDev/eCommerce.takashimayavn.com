<?php

namespace App\Console\Commands;

use App\Jobs\FixPOSJob;
use App\Models\ProductPos;
use App\Models\ProductStockPos;
use Illuminate\Console\Command;

class FixPOSCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fix-pos';

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
        $productStockPos = ProductStockPos::where('variant' , '')->orWhere('variant' , null)->get();
        $this->info('Total product stock POS: ' . $productStockPos->count());
        foreach ($productStockPos as $productStockPos) {
            dispatch(new FixPOSJob($productStockPos));
        }
    }
}
