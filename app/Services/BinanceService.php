<?php

namespace App\Services;

use GuzzleHttp\Client;

class BinanceService
{
    protected $client;
    protected $baseUrl = 'https://api.binance.com';

    public function __construct()
    {
        $this->client = new Client();
    }

    public function getCurrentPrice($symbol)
    {
        $url = $this->baseUrl . '/api/v3/ticker/price?symbol=' . $symbol;
        $response = $this->client->get($url);
        return json_decode($response->getBody(), true);
    }
}
