<?php

return [
    '/simfiles' => [
        'methods' => ['GET'],
        'controller' => 'Simfile',
        'action' => 'list'
    ],
    
    //TODO: test controller, delete later
    '/downloadtest' => [
        'methods' => ['GET'],
        'controller' => 'downloadTest'
    ],
    
    '/simfiles/upload' => [
        'methods' => ['POST'],
        'controller' => 'Simfile',
        'action' => 'upload'
    ],
            
    '/simfiles/argTest/:testarg' => [
        'methods' => ['GET'],
        'controller' => 'Simfile',
        'action' => 'test'
    ],
    
    '/user/auth' => [
        'method' => ['GET'],
        'controller' => 'UserAuth'
    ],
    
    '/user/:facebookId' => [
        'method' => ['GET'],
        'controller' => 'User',
        'action' => 'getUser'
    ],
    
    '/files/banner/:hash' => [
        'method' => ['GET'],
        'controller' => 'File',
        'action' => 'serveBanner'
    ],
    
    '/files/pack/:hash' => [
        'method' => ['GET'],
        'controller' => 'File',
        'action' => 'servePack'
    ]
];
