<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Alert;
use App\Services\ForexService;
use App\Services\DiscordService;
use App\Services\EmailService;
use Illuminate\Support\Facades\Log;

class CheckFxPriceCommand extends Command
{
    protected $signature = 'fxprice:check';
    protected $description = 'Check prices and send alerts';

    public function handle(ForexService $forexservice, DiscordService $discordService, EmailService $emailService)
    {
        $alerts = Alert::where('status', 'active')->where('type', 'forex')->get();
        
            $fxprices = $forexservice->getallPrices();

            dump($fxprices);


            foreach ($alerts as $currency) {
                $found = false;
                foreach ($fxprices["rates"] as $pair => $data) {
                    if ($pair === $currency['coin']) {
                        $found = true;
                        Log::info("Checking the Price of {$pair} for the price {$currency['price']}? current price = {$data['rate']}");
                        if ($currency['condition'] == 'higher') {
                            if ($data['rate'] > $currency['price']) {
                                $discordService->sendAlert("Price of {$currency['coin']} has gone above {$currency['price']}! {$currency['coin']}'s current price = {$data['rate']} @here {$currency['notes']}");
                                //$emailService->sendAlert("Price of {$currency['coin']} has gone above {$currency['price']}!");
                                $currency->update(['status' => 'done']);
                                Log::info("its higher! discord update sent");
                            }
                        } else {
                            if ($data['rate'] < $currency['price']) {
                                $discordService->sendAlert("Price of {$currency['coin']} has gone below {$currency['price']}! {$currency['coin']}'s current price = {$data['rate']} @here {$currency['notes']}");
                                //$emailService->sendAlert("Price of {$currency['coin']} has gone below {$currency['price']}!");
                                $currency->update(['status' => 'done']);
                                Log::info("its lower! discord update sent");
                            }
                        }
                        break;
                    }
                }
                if (!$found) {
                    echo 'Price for ' . $currency['coin'] . ' not found.' . PHP_EOL;
                }
            }





        
    }
}
