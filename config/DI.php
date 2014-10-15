<?php

return [
    //values
    'datamapper.maps' => '../config/DataMaps.php',
    'router.maps' => '../config/Routes.php',
    'db.credentials' => '../config/db.php',
    'facebook.app' => '../config/FacebookApp.php',
    
    //entites
    'Domain\Entities\StepMania\ISimfile' => DI\object('Domain\Entities\StepMania\Simfile'),
    'Domain\Entities\IUserStepByStepBuilder' => DI\object('Domain\Entities\UserStepByStepBuilder'),
    'Domain\Entities\IUserBuilder' => DI\object('Domain\Entities\UserBuilder'),
    'Domain\Entities\IUserFactory' => DI\object('Domain\Entities\UserFactory'),
    
    //services
    'Services\Http\IHttpResponse' => DI\object('Services\Http\HttpResponse'),
    'Services\Http\IHttpRequest' => DI\object('Services\Http\HttpRequest'),
    'Services\Routing\IRouter' => DI\object('Services\Routing\Router')
        ->constructor(DI\link('router.maps')),
    'Services\Uploads\IUploadManager' => DI\object('Services\Uploads\UploadManager'),
    'Services\Uploads\IFileFactory' => DI\object('Services\Uploads\FileFactory'),
    'Services\IFacebookSessionFactory' => DI\object('Services\FacebookSessionFactory')
        ->constructor(DI\link('facebook.app')),
    
    //DA
    'DataAccess\StepMania\ISimfileRepository' => DI\object('DataAccess\StepMania\SimfileRepository'),
    'DataAccess\IUserRepository' => DI\object('DataAccess\UserRepository'),
    'DataAccess\IDatabaseFactory' => DI\object('DataAccess\DatabaseFactory')
        ->constructor(DI\link('db.credentials')),
    'DataAccess\DataMapper\IDataMapper' => DI\object('DataAccess\DataMapper\DataMapper')
        ->constructor(DI\link('datamapper.maps')),
    'DataAccess\Queries\IQueryBuilderFactory' => DI\object('DataAccess\Queries\QueryBuilderFactory'),
    
];
