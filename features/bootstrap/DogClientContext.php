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
