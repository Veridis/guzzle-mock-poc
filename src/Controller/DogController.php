<?php

namespace App\Controller;

use App\Http\Client\DogClient;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route("/api")
 */
class DogController
{
    /**
     * @Route("/dogs")
     */
    public function index(DogClient $dogClient): Response
    {
        $psrResponse = $dogClient->retrieve();

        return new JsonResponse(
            (string) $psrResponse->getBody(),
            $psrResponse->getStatusCode(),
            [],
            true
        );
    }
}
