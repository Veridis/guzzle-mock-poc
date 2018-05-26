# guzzle-mock-poc
POC for mocking guzzle client

## Running the tests

```text
$ composer install

$ vendor/bin/phpunit
$ vendor/bin/behat
```

## Environments configuration
### prod

In `prod` environment, the `app.http.client.guzzle_dog_client` will be configured to send request to the [https://dog.ceo/dog-api/]() api

```yaml
# config/services.yaml
services:
    # ...
    
    # this is the guzzle client. It is created with a factory
    app.http.client.guzzle_dog_client:
        class: GuzzleHttp\Client
        factory: 'App\Http\Client\Factory\DogGuzzleClientFactory:createClient'
    
    # This is the service that uses the guzzle client
    App\Http\Client\DogClient:
        arguments:
            $client: '@app.http.client.guzzle_dog_client'
```

### test

In `test environment, the `app.http.client.guzzle_dog_client` is overriden. 
We will Mock the response with the `GuzzleHttp\Handler\MockHandler`

```yaml
# config/services_test.yaml
services:
    _defaults:
        public: true

    app.http.client.guzzle_dog_mock_handler:
        class: GuzzleHttp\Handler\MockHandler

    app.http.client.guzzle_dog_handler_stack:
        class: GuzzleHttp\HandlerStack
        arguments: ['@app.http.client.guzzle_dog_mock_handler']

    # we override the guzzle client to inject him the handler stack, which have a mock handler
    app.http.client.guzzle_dog_client:
        class: GuzzleHttp\Client
        arguments:
            - { handler: '@app.http.client.guzzle_dog_handler_stack' }
```

## Functional testing
### phpunit

#### The `GuzzleMockHandlerWebTestCase`

```php
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
```

#### Mocking the response

```php
<?php 

/**
 * @test
 */
public function something()
{
    $mockedResponse = new PsrResponse(
        402,
        [],
        json_encode([
            'mock' => true,
            'error' => 'fail'
        ])
    );

    $middleWareHistory = Middleware::history($this->history);
    $this->mockHandler->append($mockedResponse);
    $this->handlerStack->push($middleWareHistory);

    $this->client->request('GET', '/api/dogs');
    // dump($this->history);
    $response = $this->client->getResponse();

    $this->assertEquals('{"mock":true,"error":"fail"}', $response->getContent());
    $this->assertEquals(402, $response->getStatusCode());
}

```

### behat

#### The `DogClientContext`

```php
<?php

use Behat\Behat\Context\Context;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Response as PsrResponse;
use Symfony\Component\HttpKernel\KernelInterface;

class DogClientContext implements Context
{
    /** @var KernelInterface */
    private $kernel;

    /** @var MockHandler $mockHandler */
    private $mockHandler = null;

    /** @var HandlerStack $handlerStack */
    private $handlerStack = null;

    /** @var array */
    private $history = [];

    public function __construct(KernelInterface $kernel)
    {
        $this->kernel = $kernel;
    }

    /**
     * @Given /^The DogClient Is Mocked$/
     */
    public function theDogClientIsMocked()
    {
        $this->mockHandler = $this->kernel->getContainer()->get('app.http.client.guzzle_dog_mock_handler');
        $this->handlerStack = $this->kernel->getContainer()->get('app.http.client.guzzle_dog_handler_stack');
    }

    /**
     * @Given /^The DogClient Will Return a Mocked Response$/
     */
    public function theDogClientWillReturn()
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
    }
}
```

#### Mocking the response in the fixture

```gherkin
Feature:
  I want to test a mock guzzle client

  Scenario: I want to test a mock guzzle client
    Given The DogClient Is Mocked
      And The DogClient Will Return a Mocked Response
      And I send a "GET" request to "/api/dogs"
    Then the response status code should not be 200
      And the response status code should be 402
      And the response should be equal to
      """
      {"mock":true}
      """

```