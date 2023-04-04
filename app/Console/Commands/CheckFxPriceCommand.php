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

            //dump($fxprices["response"]);


                foreach ($alerts as $currency) {
                    $found = false;
                    foreach ($fxprices["response"] as $price) {
                        if ($price['s'] === $currency['coin']) {
                            $found = true;
                        dump($found);
                        dump($currency);
                        dump($price);
                            if ($currency['condition'] == 'higher') {
                                if ($price['h'] > $currency['price']) {
                                
                                    $discordService->sendAlert("Price of {$currency->coin} has gone above {$currency->price}! {$currency->coin}'s current price = {$price['h']} @here {$currency->notes}");
                                    //$emailService->sendAlert("Price of {$currency->coin} has gone above {$currency->price}!");
                                    $currency->update(['status' => 'done']);
                                    
                                    Log::info("its higher! discord update sent");
                                }
                            } else {
                                if ($price['l'] < $currency['price']) {
                                    $discordService->sendAlert("Price of {$currency->coin} has gone below {$currency->price}! {$currency->coin}'s current price = {$price['h']} @here {$currency->notes}");
                                    //$emailService->sendAlert("Price of {$alert->coin} has gone below {$alert->price}!");
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
