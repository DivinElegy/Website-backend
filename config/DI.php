<?php

return [
    //values
    'datamapper.maps' => '../config/DataMaps.php',
    'router.maps' => '../config/Routes.php',
    'db.credentials' => '../config/db.php',
    
    //entites
    'Domain\Entities\StepMania\ISimfile' => DI\object('Domain\Entities\StepMania\Simfile'),
    
    //services
    'Services\Http\IHttpResponse' => DI\object('Services\Http\HttpResponse'),
    'Services\Http\IHttpRequest' => DI\object('Services\Http\HttpRequest'),
    'Services\Routing\IRouter' => DI\object('Services\Routing\Router')
        ->constructor(DI\link('router.maps')),
    
    //DA
    'DataAccess\StepMania\ISimfileRepository' => DI\object('DataAccess\StepMania\SimfileRepository'),
    'DataAccess\DataMapper\IDataMapper' => DI\object('DataAccess\DataMapper\DataMapper')
        ->constructor(DI\link('datamapper.maps'), DI\link('db.credentials')),
    'DataAccess\Queries\IQueryBuilderFactory' => DI\object('DataAccess\Queries\QueryBuilderFactory')
];
