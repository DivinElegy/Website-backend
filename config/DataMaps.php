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
            'banner' => DataAccess\Entity('File', 'getBanner', 'banner_file'),
            'simfile' => DataAccess\Entity('File', 'getSimfile', 'simfile_file'),
            'packId' => DataAccess\Int('pack_id', 'getPackId'),
            'steps' => DataAccess\VOArray('StepChart', 'getSteps')
        ]
    ],
    
    'Pack' => [
        'class' => 'Domain\Entities\StepMania\Pack',
        'table' => 'packs',
        'maps' => [
            'title' => DataAccess\Varchar('title'),
            'uploader' => DataAccess\Entity('User', 'getUploader'),
            'simfiles' => DataAccess\EntityArray('Simfile', 'getSimfiles'),
            'banner' => DataAccess\Entity('File', 'getBanner', 'banner_file'),
            'file' => DataAccess\Entity('File', 'getFile')
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
            'facebookId' => DataAccess\Varchar('facebook_id')
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
    ],
    
    'File' => [
        'class' => 'Domain\Entities\File',
        'table' => 'files',
        'maps' => [
            'hash' => DataAccess\Varchar('hash'),
            'path' => DataAccess\Varchar('path'),
            'filename' => DataAccess\Varchar('filename'),
            'mimetype' => DataAccess\Varchar('mimetype'),
            'size' => DataAccess\Int('size'),
            'uploadDate' => DataAccess\Int('uploaded', 'getUploadDate'),
            'mirrors' => DataAccess\VOArray('FileMirror', 'getMirrors')
        ]
    ],
    
    'FileMirror' => [
        'class' => 'Domain\VOs\FileMirror',
        'table' => 'mirrors',
        'maps' => [
            'uri' => DataAccess\Varchar('uri'),
            'source' => DataAccess\Varchar('source')
        ]
    ],
    
    'Download' => [
        'class' => 'Domain\Entities\Download',
        'table' => 'downloads',
        'maps' => [
            'user' => DataAccess\Entity('User'),
            'file' => DataAccess\Entity('File'),
            'timestamp' => DataAccess\Int('timestamp'),
            'ip' => DataAccess\Varchar('ip')
        ]
    ]
];
