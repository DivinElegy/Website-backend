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
    
    //XXX: Test, delete later
    '/simfiles/pack' => [
        'methods' => ['GET'],
        'controller' => 'PackTest',
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
