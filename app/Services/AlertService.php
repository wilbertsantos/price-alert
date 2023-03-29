<?php

namespace App\Services;

use App\Models\Alert;
use App\Services\BinanceApiService;
use Illuminate\Support\Facades\Mail;
use App\Mail\AlertEmail;

class AlertService
{
    /**
     * Create a new alert.
     *
     * @param string $coin
     * @param float $price
     * @param string $condition
     * @return void
     */
    public function create(string $coin, float $price, string $condition)
    {
        $alert = new Alert();
        $alert->coin = $coin;
        $alert->price = $price;
        $alert->condition = $condition;
        $alert->status = 'active';
        $alert->save();
    }

    /**
     * Update an existing alert.
     *
     * @param int $id
     * @param string $coin
     * @param float $price
     * @param string $condition
     * @return void
     */
    public function update(int $id, string $coin, float $price, string $condition)
    {
        $alert = Alert::findOrFail($id);
        $alert->coin = $coin;
        $alert->price = $price;
        $alert->condition = $condition;
        $alert->save();
    }

    /**
     * Get all alerts.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAll()
    {
        return Alert::orderBy('active', 'desc')->get();
    }

    /**
     * Get active alerts.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getActive()
    {
        return Alert::where('status', 'active')->orderBy('active', 'desc')->get();
    }

    /**
     * Check all active alerts and send alerts if conditions are met.
     *
     * @return void
     */
    public function checkActiveAlerts()
    {
        $binanceApiService = new BinanceApiService();
        $alerts = $this->getActive();
        foreach ($alerts as $alert) {
            $symbol = $alert->coin . 'USDT';
            $currentPrice = $binanceApiService->getLatestPrice($symbol);
            if ($alert->condition == 'higher' && $currentPrice >= $alert->price) {
                $this->sendAlert($alert);
                $alert->status = 'done';
                $alert->save();
            } elseif ($alert->condition == 'lower' && $currentPrice <= $alert->price) {
                $this->sendAlert($alert);
                $alert->status = 'done';
                $alert->save();
            }
        }
    }

    /**
     * Send alert via email and Discord.
     *
     * @param Alert $alert
     * @return void
     */
    public function sendAlert(Alert $alert)
    {
        // send email alert
        $emailData = [
            'coin' => $alert->coin,
            'price' => $alert->price,
            'condition' => $alert->condition,
            'status' => $alert->status,
        ];
        Mail::to('wgs.g92@gmail.com')->send(new AlertEmail($emailData));

        // send Discord alert
        $webhookUrl = env('DISCORD_WEBHOOK_URL');
        $message = '@here Alert: ' . $alert->coin . ' ' . $alert->condition . ' ' . $alert->price;
        $data = [
            'content' => $message,
       
