<?php

return new \GuzzleHttp\Psr7\Response(
    402,
    [],
    json_encode([
        'mock' => true,
        'error' => 'fail'
    ])
);
