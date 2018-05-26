<?php

namespace App\Http\Client;

use GuzzleHttp\Client;
use Psr\Http\Message\ResponseInterface;

class DogClient
{
    /**
     * @var Client
     */
    private $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function retrieve(): ResponseInterface
    {
        return $this->client->get('api/breeds/image/random');
    }
}
