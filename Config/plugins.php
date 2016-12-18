<?php

return [
    'HtmlSource' => [
        'class' => Plugin\HtmlSource\HtmlSource::class,
        'parameters' => [],
    ],
    'HtmlComment' => [
        'class' => Plugin\HtmlComment\HtmlComment::class,
        'parameters' => [
            'fields' => [
                'ShortDescription',
                'LongDescription',
            ]
        ],
    ],
];