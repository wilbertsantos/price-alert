<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;

class BinanceService
{
    protected $client;
    protected $baseUrl = 'https://api.binance.com';

    public function __construct()
    {
        $this->client = new Client();
    }
    //support the multiple symbols
    //https://api.binance.com/api/v3/ticker/price?symbols=["BTCUSDT","BNBUSDT"]
    public function getCurrentPrice($symbol)
    {
        $url = $this->baseUrl . '/api/v3/ticker/price?symbol=' . $symbol;
        //$response = $this->client->get($url);
        try {
            $response = $this->client->get($url);
        } catch (ClientException $e) {
            // Handle the error here, e.g. log it and return an error response
            $statusCode = $e->getResponse()->getStatusCode();
            $message = $e->getMessage();
            return ['error' => "Error $statusCode: $message"];
        }        
        return json_decode($response->getBody(), true);
    }

    public function getCurrentPrices($symbols)
    {
        $symbols = json_encode($symbols);
        $url = $this->baseUrl . '/api/v3/ticker/price?symbols='. $symbols;
        //$response = $this->client->get($url);
        try {
            $response = $this->client->get($url);
        } catch (ClientException $e) {
            // Handle the error here, e.g. log it and return an error response
            $statusCode = $e->getResponse()->getStatusCode();
            $message = $e->getMessage();
            return ['error' => "Error $statusCode: $message"];
        }

        return json_decode($response->getBody(), true);
    }

}
