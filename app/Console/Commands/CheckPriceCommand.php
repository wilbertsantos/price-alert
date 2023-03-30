<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Alert;
use App\Services\BinanceService;
use App\Services\DiscordService;
use App\Services\EmailService;
use Illuminate\Support\Facades\Log;

class CheckPriceCommand extends Command
{
    protected $signature = 'price:check';
    protected $description = 'Check prices and send alerts';

    public function handle(BinanceService $binanceService, DiscordService $discordService, EmailService $emailService)
    {
        Log::info('Initializing Price Check:');
        $alerts = Alert::where('status', 'active')->get();

        foreach ($alerts as $alert) {
            $currentPrice = $binanceService->getCurrentPrice($alert->coin);
                       
            Log::info("Checking the Price of {$alert->coin} if gone above {$alert->price}? current price = {$currentPrice['price']}");
            if ($alert->condition == 'higher' && $currentPrice['price'] >= $alert->price) {
                $discordService->sendAlert("Price of {$alert->coin} has gone above {$alert->price}! {$alert->coin}'s current price = {$currentPrice['price']} @here {$alert->notes}");
                //$emailService->sendAlert("Price of {$alert->coin} has gone above {$alert->price}!");
                $alert->update(['status' => 'done']);
                
                Log::info("discord update sent");
            } elseif ($alert->condition == 'lower' && $currentPrice['price'] <= $alert->price) {
                $discordService->sendAlert("Price of {$alert->coin} has gone below {$alert->price}! {$alert->coin}'s current price = {$currentPrice['price']} @here {$alert->notes}");
                //$emailService->sendAlert("Price of {$alert->coin} has gone below {$alert->price}!");
                $alert->update(['status' => 'done']);
                Log::info("discord update sent");
            }
        }
        
        Log::info('Price Check Job Done!');
    }
}
