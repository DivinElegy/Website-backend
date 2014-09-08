<?php
require_once('../vendor/autoload.php');

use Domain\Entities\StepMania\SimfileFactory;
use Domain\Entities\StepMania\SimfileBuilder;
use Domain\Entities\StepMania\SimfileStepByStepBuilder;
use Domain\VOs\StepMania\BPM;
use Domain\VOs\StepMania\StepChart;
use Domain\VOs\StepMania\DanceMode;
use Domain\VOs\StepMania\Difficulty;
use Domain\VOs\StepMania\StepArtist;
use Domain\VOs\StepMania\Artist;
use Domain\ConstantsAndTypes\SimfileConstants;

$containerBuilder = new DI\ContainerBuilder();
$containerBuilder->addDefinitions('../config/DI.php');
$containerBuilder->useAutowiring(true);

$container = $containerBuilder->build();


$indexController = $container->get('Controllers\IndexController');
$indexController->getAction();

/* @var $foo Domain\Entities\Foo */
//$foo = $container->get('Domain\Entities\Foo');
//$foo->returnMe();
//
//$DataMapper = new \DataAccess\DataMapper\DataMapper('../config/DataMaps.php');
//$user = $DataMapper->find(1,'User');
//
//$simfileFactory = new SimfileFactory();
//$simfileBuilder = new SimfileBuilder($simfileFactory);
//$simfileStepByStepBuilder = new SimfileStepByStepBuilder($simfileBuilder);
//
//$danceMode = new DanceMode('dance-single');
//$difficulty = new Difficulty('challenge');
//$stepArtist = new StepArtist('Someone new fuck');
//$artist = new Artist('A brand new awesome artist!');
//$rating = '10';
//
//$bpm = new BPM('256', '128');
//$stepChart = new StepChart($danceMode,
//    $difficulty,
//    $stepArtist,
//    $rating);
//
//$steps = array($stepChart);
//
//
//$simfile = $simfileStepByStepBuilder->With_Title('Brand New Simfile')
//                                    ->With_Artist($artist)
//                                    ->With_Uploader($user)
//                                    ->With_BPM($bpm)
//                                    ->With_BpmChanges(SimfileConstants::NO_BPM_CHANGES)
//                                    ->With_Stops(SimfileConstants::NO_STOPS)
//                                    ->With_FgChanges(SimfileConstants::NO_FG_CHANGES)
//                                    ->With_BgChanges(SimfileConstants::NO_BG_CHANGES)
//                                    ->With_Steps($steps)
//                                    ->build();
//
//
////$user->setId(NULL);
//
//$simfile = $DataMapper->find(1, 'Simfile');
//$simfile->addStepChart($stepChart);
//$DataMapper->save($simfile);




//$stepchart = $simfile->getSteps();
//$stepchart = $stepchart[0];
//$maps = include '../config/DataMaps.php';
//
//$DataMapper->save($user);

