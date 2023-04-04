<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Alert;
use App\Services\BinanceService;
use App\Services\DiscordService;
use App\Services\EmailService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class CheckPriceCommand extends Command
{
    protected $signature = 'price:check';
    protected $description = 'Check prices and send alerts';

    public function handle(BinanceService $binanceService, DiscordService $discordService, EmailService $emailService)
    {
        $alerts = Alert::where('status', 'active')->get();

        foreach ($alerts as $alert) {
            $coin =  Str::replace('PERP', '', Str::upper($alert->coin));
            $currentPrice = $binanceService->getCurrentPrice($coin);
            //minimize the api call and get all symbols in 1 call
            //https://api.binance.com/api/v3/ticker/price?symbols=["BTCUSDT","BNBUSDT"]
            Log::info("Checking the Price of {$alert->coin} for the price {$alert->price}? current price = {$currentPrice['price']}");
            if ($alert->condition == 'higher' && $currentPrice['price'] >= $alert->price) {
                $discordService->sendAlert("Price of {$alert->coin} has gone above {$alert->price}! {$alert->coin}'s current price = {$currentPrice['price']} @here {$alert->notes}");
                //$emailService->sendAlert("Price of {$alert->coin} has gone above {$alert->price}!");
                $alert->update(['status' => 'done']);
                
                Log::info("its higher! discord update sent");
            } elseif ($alert->condition == 'lower' && $currentPrice['price'] <= $alert->price) {
                $discordService->sendAlert("Price of {$alert->coin} has gone below {$alert->price}! {$alert->coin}'s current price = {$currentPrice['price']} @here {$alert->notes}");
                //$emailService->sendAlert("Price of {$alert->coin} has gone below {$alert->price}!");
                $alert->update(['status' => 'done']);
                Log::info("its lower! discord update sent");
            }
        }
        
    }
}
