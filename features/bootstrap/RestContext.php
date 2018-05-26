<?php

declare(strict_types=1);

use Behat\Behat\Context\Context;
use Behatch\Context\RestContext as BaseRestContext;
use Behatch\HttpCall\Request;
use Symfony\Component\HttpKernel\KernelInterface;

class RestContext extends BaseRestContext implements Context
{
    /**
     * @var KernelInterface
     */
    private $kernel;

    public function __construct(KernelInterface $kernel, Request $request)
    {
        parent::__construct($request);
        $this->kernel = $kernel;
    }
}
