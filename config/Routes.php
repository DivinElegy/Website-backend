<?php

return [
    '/simfiles' => [
        'methods' => ['GET'],
        'controller' => 'Simfile',
        'action' => 'list'
    ],
    
    '/simfiles/argTest/:testarg' => [
        'methods' => ['GET'],
        'controller' => 'Simfile',
        'action' => 'test'
    ]
];
