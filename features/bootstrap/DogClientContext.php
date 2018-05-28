<?php

use Behat\Behat\Context\Context;
use Behat\Mink\Driver\BrowserKitDriver;
use Behat\Mink\Session;
use Behat\MinkExtension\Context\RawMinkContext;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\HttpKernel\Client;
use Symfony\Component\HttpKernel\KernelInterface;

class DogClientContext extends RawMinkContext implements Context
{
    /** @var KernelInterface */
    private $kernel;

    /** @var MockHandler $mockHandler */
    private $mockHandler = null;

    /** @var HandlerStack $handlerStack */
    private $handlerStack = null;

    /** @var array */
    private $history = [];
    /** @var string */
    private $responsesBasePath;

    public function __construct(
        KernelInterface $kernel,
        MockHandler $mockHandler,
        HandlerStack $handlerStack,
        string $responsesBasePath
    ) {
        $this->responsesBasePath = $responsesBasePath;

        $this->mockHandler = $mockHandler;
        $this->handlerStack = $handlerStack;
        $this->kernel = $kernel;
    }

    /**
     * @BeforeScenario
     */
    public function theDogClientIsMocked()
    {
        $this->getMink()->registerSession('test', new Session(new BrowserKitDriver(new Client($this->kernel))));
        $this->getMink()->setDefaultSessionName('test');
        $this->history = [];
    }

    /**
     * @Given /^The DogClient Will Return the "([^"]*)" response$/
     */
    public function theDogClientWillReturn(string $filename)
    {
        $filepath = $this->responsesBasePath . $filename;
        if (!is_file($filepath)) {
            throw new \Exception("The response file '$filename' does not exists");
        }

        $mockedResponse = require $filepath;
        if (!$mockedResponse instanceof ResponseInterface) {
            throw new \Exception(sprintf('The response file should return a %s class', ResponseInterface::class));
        }

        $middleWareHistory = Middleware::history($this->history);
        $this->mockHandler->append($mockedResponse);
        $this->handlerStack->push($middleWareHistory);
    }
}
