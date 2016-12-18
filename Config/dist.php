<?php
return [
    'apiEndpoint' => '',
    'apiParameters' => [
        'token' => '',
        'code' => filter_input(INPUT_GET, 'code', FILTER_SANITIZE_STRING),
    ],
    'plugins' => [
        'HtmlSource' => [
            'class' => Plugin\HtmlSource\HtmlSource::class,
            'parameters' => [],
        ],
    ],
];
