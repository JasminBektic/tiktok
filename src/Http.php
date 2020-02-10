<?php

namespace TikTok;

use GuzzleHttp\Client;

class Http extends Client
{
    /** @var Client */
    public $guzzleClient;

    public function __construct()
    {
        $this->guzzleClient = new parent([
            'base_uri' => $_ENV['SCRAPE_TARGET'],
            'headers'  => $this->headers()
        ]);
    }

    /**
     * Custom headers
     */
    private function headers() : array {
        return [
            'User-Agent'      => 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/73.0.3683.103 Safari/537.36',
            'Accept'          => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3',
            'Accept-Language' => 'en-US,en;q=0.9,bs;q=0.8,hr;q=0.7',
            'Accept-Encoding' => 'gzip, deflate, br'
        ];
    }
}