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
        'force_https' => [
            'label' => 'Force HTTPS?',
            'comment' => 'If checked, the normalised URLs will always use a secure address.'
        ],
        'normalise_nav' => [
            'label' => 'Normalise URLs in navigation menus?',
            'comment' => 'If checked, normalisation will be applied to all internal links in a Static Menu. This will
            only apply if the Static Pages plugin is installed.',
        ],
        'www_prefix' => [
            'label' => 'Domain prefix preference',
            'options' => [
                'none' => 'None (do not apply a preference)',
                'www' => 'Force URLs to start with \'www\' prefix',
                'notWww' => 'Force URLs to exclude \'www\' prefix'
            ]
        ],
        'trailing_slash' => [
            'label' => 'Trailing slash preference',
            'options' => [
                'none' => 'None (do not apply a preference)',
                'yes' => 'Force URLs to end with a trailing slash',
                'no' => 'Force URLs to not end with a trailing slash'
            ],
            'comment' => 'Please note that this will not apply to URLs that end with a file extension.'
        ],
        'ignore' => [
            'label' => 'Paths to ignore',
            'comment' => 'You can provide a list of paths relative to the root URL in which no normalisation will occur. To include all subfolders and files, use the wildcard character <strong>*</strong>.'
        ]
    ],
    'permission' => [
        'label' => 'Manage URL normalisation preferences'
    ]
];
