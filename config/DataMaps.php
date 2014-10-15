<?php

//rely on the convention that any reference column is named [parent_table]_id
//or the other way around, the column is labled [child_table]_id

// so either we will be looking for the id of the parent table, or we will
// be given the id for the child table

return [
    'Simfile' => [
        'class' => 'Domain\Entities\StepMania\Simfile',
        'table' => 'simfiles',
        'maps' => [
            //entity => table
            'title' => DataAccess\Varchar('title'),
            'artist' => DataAccess\VO('Artist'),
            'uploader' => DataAccess\Entity('User', 'getUploader'),
            'bpm' => DataAccess\VO('BPM'),
            'bpmChanges' => DataAccess\Int('bpm_changes', 'hasBPMChanges'),
            'stops' => DataAccess\Int('stops', 'hasStops'),
            'fgChanges' => DataAccess\Int('fg_changes', 'hasFgChanges'),
            'bgChanges' => DataAccess\Int('bg_changes', 'hasBgChanges'),
            'steps' => DataAccess\VOArray('StepChart', 'getSteps')
        ]
    ],
    
    'BPM' => [
        'class' => 'Domain\VOs\StepMania\BPM',
        'table' => 'simfiles',
        'maps' => [
            'high' => DataAccess\Int('bpm_high', 'getHigh'),
            'low' => DataAccess\Int('bpm_low', 'getLow')
        ]
    ],
    
    'User' => [
        'class' => 'Domain\Entities\User',
        'table' => 'users',
        'maps' => [
            'country' => DataAccess\VO('Country'),
            'displayName' => DataAccess\Varchar('display_name'),
            'name' => DataAccess\VO('Name'),
            'tags' => DataAccess\VOArray('Tag', 'getTags'), // TODO: Make VarcharArray class
            'facebookId' => DataAccess\Varchar('facebook_id'),
            'authToken' => DataAccess\Varchar('auth_token')
        ]
    ],
    
    'Name' => [
        'class' => 'Domain\VOs\Name',
        'table' => 'users_meta',
        'maps' => [
            'firstname' => DataAccess\Varchar('firstname'),
            'lastname' => DataAccess\Varchar('lastname')
        ]
    ],
        
    'Country' => [
        'class' => 'Domain\VOs\Country',
        'table' => 'users_meta',
        'maps' => [
            'country' => DataAccess\Varchar('country', 'getCountryName')
        ]        
    ],
    
    'Tag' => [
        'class' => 'Domain\VOs\StepMania\Tag',
        'table' => 'step_artists',
        'maps' => [
            'tag' => DataAccess\Varchar('tag')
        ]
    ],
    
    'Artist' => [
        'class' => 'Domain\VOs\StepMania\Artist',
        'table' => 'artists',
        'maps' => [
            'name' => DataAccess\Varchar('name')
        ]
    ],
    
    'StepChart' => [
        'class' => 'Domain\VOs\StepMania\StepChart',
        'table' => 'steps',
        'maps' => [
            'mode' => DataAccess\VO('DanceMode', 'getMode'),
            'difficulty' => DataAccess\VO('Difficulty'),
            'artist' => DataAccess\VO('StepArtist', 'getArtist', 'step_artist'),
            'rating' => DataAccess\Int('rating')
        ]
    ],
    
    'DanceMode' => [
        'class' => 'Domain\VOs\StepMania\DanceMode',
        'table' => 'steps',
        'maps' => [
            'stepManiaName' => DataAccess\Varchar('mode', 'getStepManiaName')
        ]
    ],
    
    'StepArtist' => [
        'class' => 'Domain\VOs\StepMania\StepArtist',
        'table' => 'step_artists',
        'maps' => [
            'tag' => DataAccess\Varchar('tag')
        ]
    ],
    
    'Difficulty' => [
        'class' => 'Domain\VOs\StepMania\Difficulty',
        'table' => 'steps',
        'maps' => [
            'stepManiaName' => DataAccess\Varchar('difficulty', 'getStepManiaName')
        ]
    ]
];
