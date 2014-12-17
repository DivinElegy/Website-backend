<?php

return [
    '/simfiles' => [
        'methods' => ['GET'],
        'controller' => 'Simfile',
        'action' => 'list'
    ],
    
    '/simfiles/latest/simfile' => [
        'methods' => ['GET'],
        'controller' => 'Simfile',
        'action' => 'latestSimfile'
    ],
    
    '/simfiles/latest/pack' => [
        'methods' => ['GET'],
        'controller' => 'Simfile',
        'action' => 'latestPack'
    ],
    
    '/simfiles/popular' => [
        'methods' => ['GET'],
        'controller' => 'Simfile',
        'action' => 'popular'
    ],
    
    '/simfiles/upload' => [
        'methods' => ['POST'],
        'controller' => 'Simfile',
        'action' => 'upload'
    ],
    
    '/cache/update' => [
        'methods' => ['GET'],
        'controller' => 'SimfileCache',
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
        'action' => 'serveSimfileOrPack'
    ],
    
    '/files/simfile/:hash' => [
        'method' => ['GET'],
        'controller' => 'File',
        'action' => 'serveSimfileOrPack'
    ]
];
