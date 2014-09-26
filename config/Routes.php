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
    ]
];
