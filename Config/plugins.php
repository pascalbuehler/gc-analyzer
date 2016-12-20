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
    'ImagesInListing' => [
        'class' => Plugin\ImagesInListing\ImagesInListing::class,
        'parameters' => [
            'fields' => [
                'ShortDescription',
                'LongDescription',
            ]
        ],
    ],
];