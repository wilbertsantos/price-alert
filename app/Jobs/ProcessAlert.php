<?php

namespace App\Jobs;

use App\Models\Alert;
use App\Services\BinanceService;
use App\Services\DiscordService;
use App\Services\EmailService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessAlert implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $alert;

    /**
     * Create a new job instance.
     *
     * @param Alert $alert
     */
    public function __construct(Alert $alert)
    {
        $this->alert = $alert;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $binance = new BinanceService();
        $price = $binance->getCurrentPrice($this->alert->coin);

        if (!$price) {
            return;
        }

        if (($this->alert->condition === 'higher' && $price >= $this->alert->price) || 
            ($this->alert->condition === 'lower' && $price <= $this->alert->price)) {
            // send alert to Discord
            $discord = new DiscordService();
            $discord->sendAlert($this->alert->user_id, $this->alert->coin, $price);

            // send alert to email
            $email = new EmailService();
            $email->sendAlert($this->alert->user->email, $this->alert->coin, $price);

            // mark alert as done
            $this->alert->update(['status' => 'done']);
        }
    }
}
