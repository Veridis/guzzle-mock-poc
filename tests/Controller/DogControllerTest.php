<?php

namespace App\Tests\Controller;

use App\Tests\TestCase\GuzzleMockHandlerWebTestCase;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Response as PsrResponse;

class DogControllerTest extends GuzzleMockHandlerWebTestCase
{
    /**
     * @test
     */
    public function dogControllerWithError()
    {
        $mockedResponse = new PsrResponse(
            402,
            [],
            json_encode([
                'mock' => true,
                'error' => 'fail',
            ])
        );

        $middleWareHistory = Middleware::history($this->history);
        $this->mockHandler->append($mockedResponse);
        $this->handlerStack->push($middleWareHistory);

        $this->client->request('GET', '/api/dogs');
//        dump($this->history);
        $response = $this->client->getResponse();

        $this->assertEquals('{"mock":true,"error":"fail"}', $response->getContent());
        $this->assertEquals(402, $response->getStatusCode());
    }

    /**
     * @test
     */
    public function dogControllerWithSuccess()
    {
        $mockedResponse = new PsrResponse(
            200,
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
        $this->assertEquals(200, $response->getStatusCode());
    }
}
