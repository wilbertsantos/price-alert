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
        $coins = Alert::where('status', 'active')->get();
        $coinsList = [];
        foreach( $coins as $coin){
            if(!in_array(Str::replace('PERP', '', Str::upper($coin['coin'])),  $coinsList)){
                $coinsList[] = Str::replace('PERP', '', Str::upper($coin['coin']));
            }
        }
        $currentPrices = $binanceService->getCurrentPrices($coinsList);
        if(isset($currentPrices['error'])){
            $discordService->sendAlert("Error has been occured {$currentPrices['error']}");
            foreach ($coins as $alert) {
                $coin =  Str::replace('PERP', '', Str::upper($alert->coin));
                $currentPrice = $binanceService->getCurrentPrice($coin);
                //dump($currentPrice);
                if(isset($currentPrice['error'])){
                    $discordService->sendAlert("HOY @{$alert->author}! MAY MALI SA ALERT MO!!! Error has been occured {$currentPrice['error']}");
                    $alert->update(['status' => 'done']);
                }else{
                    Log::info("#{$alert->id}: Checking the Price of {$alert->coin} for the price {$alert->price}? current price = {$currentPrice['price']}");
                    if ($alert->condition == 'higher' && $currentPrice['price'] >= $alert->price) {
                        $discordService->sendAlert("@{$alert->author}! Price of {$alert->coin} has gone above {$alert->price}! {$alert->coin}'s current price = {$currentPrice['price']} @here {$alert->notes}");
                        //$emailService->sendAlert("Price of {$alert->coin} has gone above {$alert->price}!");
                        $alert->update(['status' => 'done']);
                        
                        Log::info("its higher! discord update sent");
                    } elseif ($alert->condition == 'lower' && $currentPrice['price'] <= $alert->price) {
                        $discordService->sendAlert("@{$alert->author}! Price of {$alert->coin} has gone below {$alert->price}! {$alert->coin}'s current price = {$currentPrice['price']} @here {$alert->notes}");
                        //$emailService->sendAlert("Price of {$alert->coin} has gone below {$alert->price}!");
                        $alert->update(['status' => 'done']);
                        Log::info("its lower! discord update sent");
                    }

                }
            }
        }else{
            //dump($currentPrices);
            foreach ($coins as $alert) {
                $coin =  Str::replace('PERP', '', Str::upper($alert->coin));
                $found = false;
                foreach($currentPrices as $prices){
                    $found = true;
                    if($prices['symbol'] === $coin){
                        Log::info("#{$alert->id}: Checking the Price of {$coin} for the price {$alert->price}? current price = {$prices['price']}");
                        if ($alert->condition == 'higher' && $prices['price'] >= $alert->price) {
                            $discordService->sendAlert("@{$alert->author}! Price of {$coin} has gone above {$alert->price}! {$coin}'s current price = {$prices['price']} @here {$alert->notes}");
                            //$emailService->sendAlert("Price of {$coin} has gone above {$alert->price}!");
                            $alert->update(['status' => 'done']);
                            
                            Log::info("its higher! discord update sent");
                        } elseif ($alert->condition == 'lower' && $prices['price'] <= $alert->price) {
                            $discordService->sendAlert("@{$alert->author}! Price of {$coin} has gone below {$alert->price}! {$coin}'s current price = {$prices['price']} @here {$alert->notes}");
                            //$emailService->sendAlert("Price of {$coin} has gone below {$alert->price}!");
                            $alert->update(['status' => 'done']);
                            Log::info("its lower! discord update sent");
                        }
                    }
                }
                if (!$found) {
                    echo 'Price for ' . $coin . ' not found.' . PHP_EOL;
                }
            }

        }
        
    }
}
