<?php

return [
    'HtmlSource' => [
        'class' => Plugin\HtmlSource\HtmlSource::class,
        'parameters' => [
            'fields' => [
                'ShortDescription',
                'LongDescription',
            ]
        ],
        'description' => 'Extracts and displays the HTML source code.',
        'examples' => [
            'GC40' => 'Geocache',
        ],
    ],
    'HtmlComment' => [
        'class' => Plugin\HtmlComment\HtmlComment::class,
        'parameters' => [
            'fields' => [
                'ShortDescription',
                'LongDescription',
            ]
        ],
        'description' => 'Searches for comments in the HTML source code.',
        'examples' => [
            'GC68P2Q' => 'Login',
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
        'description' => 'Finds and displays images in the HTML source code.',
        'examples' => [
            'GC2T6E3' => 'Whirlpool',
        ],
    ],
	'ListingImages' => [
        'class' => Plugin\ListingImages\ListingImages::class,
        'parameters' => [
            'fields' => [
                'Images'
            ]
        ],
        'description' => 'Finds and displays images attached to the listing.',
        'examples' => [
            'GC56NXQ' => 'Hard Targets: Plan B',
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
        'examples' => [
            'GC111YR' => 'Spoiler',
            'GC65H6Q' => 'Hikiashi spricht NoSQL',
        ],
    ],
];