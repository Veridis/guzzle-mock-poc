<?php

namespace App\Tests\TestCase;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use Symfony\Bundle\FrameworkBundle\Client;

class GuzzleMockHandlerWebTestCase extends WebTestCase
{
    /** @var Client */
    protected $client;

    /** @var MockHandler $mockHandler */
    protected $mockHandler;

    /** @var HandlerStack $handlerStack */
    protected $handlerStack;

    /** @var array */
    protected $history = [];

    public function setUp()
    {
        $this->client = static::createClient();

        $this->mockHandler = self::$kernel->getContainer()->get('app.http.client.guzzle_dog_mock_handler');
        $this->handlerStack = self::$kernel->getContainer()->get('app.http.client.guzzle_dog_handler_stack');
    }
}
