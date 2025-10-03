<?php

namespace App\Console\Commands;

use App\Commodity;
use Illuminate\Console\Command;

class UpdateCommodityConditions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:update-commodity-conditions';

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
    Commodity::chunk(200, function ($commodities) {
        foreach ($commodities as $commodity) {
            $commodity->updateCondition();
        }
    });

    $this->info('Kondisi barang berhasil diperbarui!');
}
}
