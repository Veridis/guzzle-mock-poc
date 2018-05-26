<?php

namespace App\Tests\Controller;

use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Response as PsrResponse;
use Symfony\Bundle\FrameworkBundle\Client as FrameworkClient;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;

class DogControllerTest extends WebTestCase
{
    /** @var ContainerInterface */
    private $container;

    /** @var FrameworkClient */
    private $client;

    /** @var MockHandler $mockHandler */
    private $mockHandler;

    /** @var HandlerStack $handlerStack */
    private $handlerStack;

    /**
     * @var array
     */
    private $history = [];

    public function setUp()
    {
        $this->client = static::createClient();
        $this->container = self::$kernel->getContainer();

        $this->mockHandler = self::$kernel->getContainer()->get('app.http.client.guzzle_dog_mock_handler');
        $this->handlerStack = self::$kernel->getContainer()->get('app.http.client.guzzle_dog_handler_stack');
    }

    /**
     * @test
     */
    public function dogControllerWithMockedRequest()
    {
        $mockedResponse = new PsrResponse(
            402,
            [],
            json_encode([
                'mock' => true,
            ])
        );

        $middleWareHistory = Middleware::history($this->history);
        $this->mockHandler->append($mockedResponse);
        $this->handlerStack->push($middleWareHistory);

        $this->client->request('GET', '/api/dogs');
//        dump($this->history);
        $response = $this->client->getResponse();

        $this->assertEquals('{"mock":true}', $response->getContent());
        $this->assertEquals(402, $response->getStatusCode());
    }
}
