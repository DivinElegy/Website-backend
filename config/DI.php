<?php

return [
    //values
    'datamapper.maps' => '../config/DataMaps.php',
    'router.maps' => '../config/Routes.php',
    'db.credentials' => '../config/db.php',
    'facebook.app' => '../config/FacebookApp.php',
    
    //entites
    'Domain\Entities\StepMania\ISimfileFactory'           => DI\object('Domain\Entities\StepMania\SimfileFactory'),
    'Domain\Entities\StepMania\ISimfileBuilder'           => DI\object('Domain\Entities\StepMania\SimfileBuilder'),
    'Domain\Entities\StepMania\ISimfileStepByStepBuilder' => DI\object('Domain\Entities\StepMania\SimfileStepByStepBuilder'),

    'Domain\Entities\StepMania\IPackFactory'              => DI\object('Domain\Entities\StepMania\PackFactory'),
    'Domain\Entities\StepMania\IPackBuilder'              => DI\object('Domain\Entities\StepMania\PackBuilder'),
    'Domain\Entities\StepMania\IPackStepByStepBuilder'    => DI\object('Domain\Entities\StepMania\PackStepByStepBuilder'),

    'Domain\Entities\IUserFactory'                        => DI\object('Domain\Entities\UserFactory'),
    'Domain\Entities\IUserBuilder'                        => DI\object('Domain\Entities\UserBuilder'),
    'Domain\Entities\IUserStepByStepBuilder'              => DI\object('Domain\Entities\UserStepByStepBuilder'),

    'Domain\Entities\IFileFactory'                        => DI\object('Domain\Entities\FileFactory'),
    'Domain\Entities\IFileBuilder'                        => DI\object('Domain\Entities\FileBuilder'),
    'Domain\Entities\IFileStepByStepBuilder'              => DI\object('Domain\Entities\FileStepByStepBuilder'),

    //services
    'Services\Http\IHttpResponse'                         => DI\object('Services\Http\HttpResponse'),
    'Services\Http\IHttpRequest'                          => DI\object('Services\Http\HttpRequest'),
    'Services\Routing\IRouter'                            => DI\object('Services\Routing\Router')
                                                                ->constructor(DI\link('router.maps')),
    'Services\Uploads\IUploadManager'                     => DI\object('Services\Uploads\UploadManager'),
    'Services\IUserSession'                               => DI\object('Services\UserSession'),
    'Services\Uploads\IFileFactory'                       => DI\object('Services\Uploads\FileFactory'),
    'Services\IFacebookSessionFactory'                    => DI\object('Services\FacebookSessionFactory')
                                                                ->constructor(DI\link('facebook.app')),
    'Services\ISimfileParser'                             => DI\object('Services\SimfileParser'),
    'Services\IZipParser'                                 => DI\object('Services\ZipParser'),
    'Services\IBannerExtracter'                           => DI\object('Services\BannerExtracter'),
    'Services\ISMOMatcher'                                => DI\object('Services\SMOMatcher'),
    
    //DA
    'DataAccess\StepMania\ISimfileRepository'             => DI\object('DataAccess\StepMania\SimfileRepository'),
    'DataAccess\StepMania\IPackRepository'                => DI\object('DataAccess\StepMania\PackRepository'),
    'DataAccess\IDownloadRepository'                      => DI\object('DataAccess\DownloadRepository'),
    'DataAccess\IUserRepository'                          => DI\object('DataAccess\UserRepository'),
    'DataAccess\IFileRepository'                          => DI\object('DataAccess\FileRepository'),
    'DataAccess\IDatabaseFactory'                         => DI\object('DataAccess\DatabaseFactory')
                                                                ->constructor(DI\link('db.credentials')),
    'DataAccess\DataMapper\IDataMapper'                   => DI\object('DataAccess\DataMapper\DataMapper')
                                                                ->constructor(DI\link('datamapper.maps')),
    'DataAccess\Queries\IQueryBuilderFactory'             => DI\object('DataAccess\Queries\QueryBuilderFactory'),
    
];
