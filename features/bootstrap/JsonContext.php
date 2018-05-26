<?php

declare(strict_types=1);

use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\PyStringNode;
use Behatch\Json\Json;
use Behatch\Context\JsonContext as BaseJsonContext;
use Behatch\HttpCall\Request;

class JsonContext extends BaseJsonContext implements Context
{
    /**
     * @var Request
     */
    private $request;

    public function __construct(
        Behatch\HttpCall\HttpCallResultPool $httpCallResultPool,
        string $evaluationMode = 'javascript',
        Behatch\HttpCall\Request $request
    ) {
        parent::__construct($httpCallResultPool, $evaluationMode);

        $this->request = $request;
    }

    /**
     * @Then the JSON response should be paginated
     */
    public function theJsonResponseShouldBePaginated()
    {
        $jsonResponse = json_decode($this->request->getContent(), true);

        $this->assertArrayHasKey('pagination', $jsonResponse);
        $this->assertArrayHasKey('page', $jsonResponse['pagination']);
        $this->assertArrayHasKey('pages', $jsonResponse['pagination']);
        $this->assertArrayHasKey('items', $jsonResponse['pagination']);
        $this->assertArrayHasKey('limit', $jsonResponse['pagination']);
        $this->assertArrayHasKey('links', $jsonResponse['pagination']);
        $this->assertArrayHasKey('first', $jsonResponse['pagination']['links']);
        $this->assertArrayHasKey('last', $jsonResponse['pagination']['links']);
        $this->assertArrayHasKey('goto', $jsonResponse['pagination']['links']);

        // Previous & next can be NULL
    }

    /**
     * @Then the JSON response should be normalized
     */
    public function theJsonResponseShouldBeNormalized()
    {
        $jsonResponse = json_decode($this->request->getContent(), true);

        $this->assertArrayHasKey('content', $jsonResponse);
    }

    /**
     * @Then the JSON response should contain error message :errorMessage
     */
    public function theJsonResponseShouldContainErrorMessage($errorMessage)
    {
        $jsonResponse = json_decode($this->request->getContent(), true);

        $this->assertArrayHasKey('error', $jsonResponse);
        $this->assertArrayHasKey('message', $jsonResponse['error']);
        $this->assertEquals($errorMessage, $jsonResponse['error']['message']);
    }

    /**
     * @Then the JSON response should contain error code :errorCode
     */
    public function theJsonResponseShouldContainErrorCode($errorCode)
    {
        $jsonResponse = json_decode($this->request->getContent(), true);

        $this->assertArrayHasKey('error', $jsonResponse);
        $this->assertArrayHasKey('app_code', $jsonResponse['error']);
        $this->assertEquals($errorCode, $jsonResponse['error']['app_code']);
    }

    /**
     * @Then the JSON response should be equal to
     */
    public function theJsonResponseShouldBeEqualTo(PyStringNode $expected)
    {
        $this->assertEquals($this->request->getContent(), $expected->getRaw());
    }

    /**
     * @Then the node :node JSON should be equal to:
     */
    public function theNodeJsonShouldBeEqualTo($node, PyStringNode $content)
    {
        $actual = $this->inspector->evaluate($this->getJson(), $node);

        try {
            $expected = new Json($content);
            $actual = json_encode($actual);
        }
        catch (\Exception $e) {
            throw new \Exception('The expected JSON is not a valid');
        }

        $this->assertSame(
            (string) $expected,
            (string) $actual,
            "The json is equal to:\n". $actual
        );
    }
}
