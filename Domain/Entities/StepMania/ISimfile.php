<?php

namespace Domain\Entities\StepMania;

use Domain\VOs\StepMania\IStepChart;

interface ISimfile
{
    public function getTitle();
    public function getArtist();
    public function getUploader();
    public function getBPM();
    public function hasBPMChanges();
    public function hasStops();
    public function hasFgChanges();
    public function hasBgChanges();
        
    public function addStepChart(IStepChart $stepChart);
    public function getSteps();
}