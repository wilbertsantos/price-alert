<?php

namespace App\Services;

use GuzzleHttp\Client;

class ForexService
{
    protected $client;
    //protected $baseUrl = 'https://fcsapi.com/api-v3/forex/candle?symbol=all_forex&period=5m&access_key=0iNorbQ3bvAjq3sxHbsrGEr';
    protected $baseUrl = 'http://localhost:8000/fcsapi.json';
    

    public function __construct()
    {
        $this->client = new Client();
    }

    public function getCurrentPrice($symbol)
    {
        $url = $this->baseUrl;
        $response = $this->client->get($url);
        return json_decode($response->getBody(), true);
    }
    public function getallPrices()
    {
        $url = $this->baseUrl;
        $response = $this->client->get($url);
        return json_decode($response->getBody(), true);
    }
}
