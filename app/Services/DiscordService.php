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

        return $response->status() === 204;
    }
}
