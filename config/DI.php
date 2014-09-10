<?php

return [
    //values
    'datamapper.maps' => '../config/DataMaps.php',
    
    'Domain\Entities\StepMania\ISimfile' => DI\object('Domain\Entities\StepMania\Simfile'),
    'Services\Http\IHttpResponse' => DI\object('Services\Http\HttpResponse'),
    'Services\Http\IHttpRequest' => DI\object('Services\Http\HttpRequest'),
    'DataAccess\StepMania\ISimfileRepository' => DI\object('DataAccess\StepMania\SimfileRepository'),
    'DataAccess\DataMapper\IDataMapper' => DI\object('DataAccess\DataMapper\DataMapper')
        ->constructor(DI\link('datamapper.maps')),   
];
