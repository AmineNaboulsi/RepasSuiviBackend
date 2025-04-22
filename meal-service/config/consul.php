<?php

return [
    'enable' => env('CONSUL_ENABLE', false),
    'uri' => env('CONSUL_URI', 'http://localhost:8500'),
    'token' => env('CONSUL_TOKEN', ''),
    'scheme' => env('CONSUL_SCHEME', 'http'),
    'dc' => env('CONSUL_DC', 'dc1'),
    'path' => env('CONSUL_PATH', 'services'),
];