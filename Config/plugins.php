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
            'GC64XBT' => 'Chrottebädli'
        ],
        'author' => 'BlaiNnn & frigidor',
        'runbydefault' => true,
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
            'GC6Z01H' => 'Local Cache',
        ],
        'author' => 'frigidor',
        'runbydefault' => true,
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
            'GC6J536' => 'Essen mit Stäbli',
        ],
        'author' => 'frigidor',
        'runbydefault' => true,
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
        'author' => 'BlaiNnn',
        'runbydefault' => true,
    ],
    'HtmlImages' => [
        'class' => Plugin\HtmlImages\HtmlImages::class,
        'parameters' => [
            'fields' => [
                'ShortDescription',
                'LongDescription',
            ]
        ],
        'description' => 'Finds images in the HTML source code.',
        'examples' => [
            'GC2T6E3' => 'Whirlpool',
        ],
        'author' => 'BlaiNnn',
        'runbydefault' => true,
    ],
    'ListingImages' => [
        'class' => Plugin\ListingImages\ListingImages::class,
        'parameters' => [
            'fields' => [
                'Images'
            ]
        ],
        'description' => 'Finds images attached to the listing.',
        'examples' => [
            'GC56NXQ' => 'Hard Targets: Plan B',
        ],
        'author' => 'BlaiNnn',
        'runbydefault' => true,
    ],
    'BackgroundImages' => [
        'class' => Plugin\BackgroundImages\BackgroundImages::class,
        'parameters' => [
            'fields' => [
                'Images'
            ]
        ],
        'description' => 'Finds background image of the listing.',
        'examples' => [
            'GC4F8CZ' => 'Pizzo del Prévat',
        ],
        'author' => 'frigidor',
        'runbydefault' => true,
    ],
    'ImageInfo' => [
        'class' => Plugin\ImageInfo\ImageInfo::class,
        'dependencies' => [
            'HtmlImages',
            'ListingImages',
            'HtmlLinks',
            'BackgroundImages',
        ],
        'parameters' => [
            'imageSources' => [
                'HtmlImages',
                'ListingImages',
                'HtmlLinks::Images',
                'BackgroundImages',
            ]
        ],
        'description' => 'Extracts exif data from images',
        'examples' => [
            'GC111YR' => 'Spoiler',
            'GC65H6Q' => 'Hikiashi spricht NoSQL',
            'GC2Q844' => 'Empty',
        ],
        'author' => 'BlaiNnn',
        'runbydefault' => true,
    ],
    'ImageFilters' => [
        'class' => Plugin\ImageFilters\ImageFilters::class,
        'dependencies' => [
            'ImageInfo',
        ],
        'parameters' => [
            'imageSources' => [
                'ImageInfo',
            ]
        ],
        'description' => 'Applies different filters on the found images',
        'examples' => [
            'GC6FP4Y' => 'De 25ger',
            'GC6FRYG' => '#000000',
            'GC3PK9V' => 'Unsichtbar?',
        ],
        'author' => 'BlaiNnn',
        'runbydefault' => true,
    ],
    'Youtube' => [
        'class' => Plugin\YoutubeSearch\YoutubeSearch::class,
        'parameters' => [
            'fields' => [
                'Code',
            ]
        ],
        'description' => 'Searches for Youtube-Videos',
        'examples' => [
            'GC6PR6G' => 'Bär@home',
        ],
        'author' => 'BlaiNnn',
        'runbydefault' => true,
    ],
    'LogImages with GPS' => [
        'class' => Plugin\LogImages\LogImages::class,
        'parameters' => [
            'fields' => []
        ],
        'description' => 'Searches for GPS-Coordinates in log images',
        'examples' => [
            'GC2HJD3' => 'Strömli #3',
        ],
        'author' => 'BlaiNnn',
        'runbydefault' => false,
    ],
];