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
    'HtmlImages' => [
        'class' => Plugin\HtmlImages\HtmlImages::class,
        'parameters' => [
            'fields' => [
                'ShortDescription',
                'LongDescription',
            ]
        ],
    ],
	'ListingImages' => [
        'class' => Plugin\ListingImages\ListingImages::class,
        'parameters' => [
            'fields' => [
                'Images'
            ]
        ],
    ],
    'ImageInfo' => [
        'class' => Plugin\ImageInfo\ImageInfo::class,
        'parameters' => [
            'imageSources' => [
                'HtmlImages',
				'ListingImages'
            ]
        ],
    ],
];