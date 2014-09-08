<?php

return [
    //values
    'datamapper.maps' => '../config/DataMaps.php',
    
    'Domain\Entities\StepMania\ISimfile' => DI\object('Domain\Entities\StepMania\Simfile'),
    'Services\IHttpResponse' => DI\object('Services\HttpResponse'),
    'DataAccess\StepMania\ISimfileRepository' => DI\object('DataAccess\StepMania\SimfileRepository'),
    'DataAccess\DataMapper\IDataMapper' => DI\object('DataAccess\DataMapper\DataMapper')
        ->constructor(DI\link('datamapper.maps')),   
];
