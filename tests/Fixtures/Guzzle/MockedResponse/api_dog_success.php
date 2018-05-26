<?php

return new \GuzzleHttp\Psr7\Response(
    200,
    [],
    json_encode([
        'mock' => true,
        'success' => true,
    ])
);
