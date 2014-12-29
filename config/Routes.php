<?php

return [
    '/simfiles' => [
        'controller' => 'Simfile',
        'actions' => [
            'GET'=> 'list'
        ]
    ],
    
    '/simfiles/latest/simfile' => [
        'controller' => 'Simfile',
        'actions' => [
            'GET' => 'latestSimfile'
        ]
    ],
    
    '/simfiles/latest/pack' => [
        'controller' => 'Simfile',
        'actions' => [
            'GET' => 'latestPack'
        ]
    ],
    
    '/simfiles/popular' => [
        'controller' => 'Simfile',
        'actions' => [
            'GET' => 'popular'
        ]
    ],
    
    '/simfiles/upload' => [
        'controller' => 'Simfile',
        'actions' => [
            'POST' => 'upload'
        ]
    ],
    
    '/cache/update' => [
        'controller' => 'SimfileCache',
    ],
    
    '/user/auth' => [
        'controller' => 'UserAuth'
    ],
    
    '/user/:facebookId' => [
        'controller' => 'User',
        'actions' => [
            'GET' => 'getUser',
            'POST' => 'update'
         ]
    ],

    '/files/banner/:hash' => [
        'controller' => 'File',
        'actions' => [
            'GET' => 'serveBanner'
        ]
    ],
    
    '/files/pack/:hash' => [
        'controller' => 'File',
        'actions' => [
            'GET' => 'serveSimfileOrPack'
         ]
    ],
    
    '/files/simfile/:hash' => [
        'methods' => ['GET'],
        'controller' => 'File',
        'actions' => [
            'GET' => 'serveSimfileOrPack'
        ]
    ]
];
