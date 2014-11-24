<?php

namespace Domain\Entities\StepMania;

use Domain\VOs\StepMania\IStepChart;
use Domain\Entities\StepMania\IPack;
use Domain\Entities\IDivineEntity;

interface ISimfile extends IDivineEntity
{
    public function getTitle();
    public function getArtist();
    public function getUploader();
    public function getBPM();
    public function hasBPMChanges();
    public function hasStops();
    public function hasFgChanges();
    public function hasBgChanges();
    public function getBanner();
    public function getSimfile();
    public function addToPack(IPack $pack);
    public function addStepChart(IStepChart $stepChart);
    public function getSteps();
    public function getPackId();
}