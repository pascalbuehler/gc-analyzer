<?php

return [
    'ListingBasics' => [
        'class' => Plugin\ListingBasics\ListingBasics::class,
        'parameters' => [
            'fields' => [
                'ShortDescription',
                'LongDescription',
            ]
        ],
        'description' => 'Display the cache basics',
        'examples' => [
            'GC40' => 'Geocache',
        ],
        'author' => 'BlaiNnn'
    ],
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
        'author' => 'frigidor'
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
        'author' => 'frigidor'
    ],
    'HtmlLinks' => [
        'class' => Plugin\HtmlLinks\HtmlLinks::class,
        'parameters' => [
            'fields' => [
                'ShortDescription',
                'LongDescription',
            ]
        ],
        'description' => 'Searches for links in the HTML source code. Prints additional info, if link is a geochecker-url. Checks if the link is pointing to an image.',
        'examples' => [
            'GC6WTA0' => 'Wetter-Anzeige (Nightcache)',
			'GC6G1P7' => 'Soundcheck! track two',
			'GC549K3' => 'Kulturgut Kirchenlied RG 247',
        ],
        'author' => 'BlaiNnn'
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
        'author' => 'BlaiNnn'
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
        'author' => 'BlaiNnn'
    ],
    'ImageInfo' => [
        'class' => Plugin\ImageInfo\ImageInfo::class,
        'dependencies' => [
            'HtmlImages',
            'ListingImages',
			'HtmlLinks',
        ],
        'parameters' => [
            'imageSources' => [
                'HtmlImages',
				'ListingImages',
                'HtmlLinks::Images',
            ]
        ],
        'description' => 'Extracts exif data from images',
        'examples' => [
            'GC111YR' => 'Spoiler',
            'GC65H6Q' => 'Hikiashi spricht NoSQL',
        ],
        'author' => 'BlaiNnn'
    ],
];