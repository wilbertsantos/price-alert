<?php

namespace App\Services;

use Illuminate\Contracts\Config\Repository;
use Illuminate\Support\Facades\Http;

class DiscordService
{
    protected $botToken;
    protected $webhookUrl;

    public function __construct(Repository $config)
    {
        $this->botToken = env('DISCORD_BOT_TOKEN');
        $this->webhookUrl = env('DISCORD_WEBHOOK');
    }

    public function sendAlert($message)
{
    $response = Http::post($this->webhookUrl, [
        'content' => $message,
    ], [
        'Authorization' => 'Bot '.$this->botToken,
    ]);

    if ($response->successful()) {
        return true;
    } else {
        $errorMessage = 'Failed to send alert: '.$response->status().' '.$response->reason();
        Log::error($errorMessage);
        return $errorMessage;
    }
}

}
