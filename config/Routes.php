<?php

return [
    '/simfiles' => [
        'methods' => ['GET'],
        'controller' => 'Simfile',
        'action' => 'list'
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
    ]
];
