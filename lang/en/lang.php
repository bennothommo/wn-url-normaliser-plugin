<?php
return [
    'plugin' => [
        'name' => 'URL Normaliser',
        'description' => 'Normalises the URLs for your website and prevents duplicate content.'
    ],
    'nav' => [
        'normalise' => [
            'label' => 'Normalise URLs',
            'description' => 'Apply URL normalisation preferences to prevent duplicate content.'
        ]
    ],
    'settings' => [
        'tabs' => [
            'urlSettings' => 'URL Settings'
        ],
        'mode' => [
            'label' => 'Normalisation Mode',
            'options' => [
                'canon' => 'Add a Canonical URL link tag',
                'redirect' => 'Redirect non-conforming URLs'
            ],
        ],
        'www_prefix' => [
            'label' => 'Domain prefix preference',
            'options' => [
                'none' => 'None (do not apply a preference)',
                'www' => 'Force domain to start with \'www\' prefix',
                'notWww' => 'Force domain to exclude \'www\' prefix'
            ],
            'comment' => 'This settings allows you to force the domain portion of your URLs on your site to either start with <strong>www</strong> or not.'
        ],
        'trailing_slash' => [
            'label' => 'Trailing slash preference',
            'options' => [
                'none' => 'None (do not apply a preference)',
                'yes' => 'Force URLs to end with a trailing slash',
                'no' => 'Force URLs to not end with a trailing slash'
            ],
            'comment' => 'This settings allows you to force URLs to end with a trailing slash or not. Note that this will not apply to URLs that end with a file extension.'
        ],
        'ignore' => [
            'label' => 'URL Paths to ignore',
            'comment' => 'You can provide a list of paths relative to the root URL in which no normalisation will occur. To include all subfolders and files, use the wildcard character <strong>*</strong>.'
        ]
    ],
    'permission' => [
        'label' => 'Manage URL normalisation preferences'
    ]
];
