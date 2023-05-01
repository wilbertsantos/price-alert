<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class GenerateBinanceCoins extends Command
{
    protected $signature = 'binance:generate-coins';

    protected $description = 'Retrieve all USDT coins available for trading on Binance Spot and save them to a database table.';

    public function handle()
    {
        $response = Http::get('https://api.binance.com/api/v3/ticker/price');
        $prices = $response->json();

        $coins = collect();
        foreach ($prices as $price) {
            $symbol = $price['symbol'];
            if (str_ends_with($symbol, 'USDT')) {
                $coin = explode('-', $symbol)[0];
                $coins->push($coin);
            }
        }

        $coins = $coins->unique()->values();

        DB::table('spots')->truncate();

        foreach ($coins as $coin) {
            DB::table('spots')->insert([
                'coin' => $coin,
                'status' => 'active',
            ]);
        }
        DB::table('spots')->insert([
            [
                'coin' => 'AUDUSD',
                'status' => 'active'
            ],
            [
                'coin' => 'EURUSD',
                'status' => 'active'
            ],
            [
                'coin' => 'GBPUSD',
                'status' => 'active'
            ],
            [
                'coin' => 'USDJPY',
                'status' => 'active'
            ],
            [
                'coin' => 'USDCAD',
                'status' => 'active'
            ]
        ]);


        $this->info(count($coins) . ' USDT coins retrieved and saved to the "spot" table.');
    }
}
