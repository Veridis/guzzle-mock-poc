<?php

namespace App\Http\Client\Factory;

use GuzzleHttp\Client;

class DogGuzzleClientFactory
{
    public function createClient()
    {
        return new Client([
            'base_uri' => 'https://dog.ceo/',
        ]);
    }
}
