<?php

namespace Domain\Entities\StepMania;

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
        
    public function addStepChart(StepChart $stepChart);
    public function getSteps();
}