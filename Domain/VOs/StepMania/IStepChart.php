<?php

namespace Domain\VOs\StepMania;

interface IStepChart
{
    public function getMode();
    public function getRating();
    public function getDifficulty();
    public function getArtist();        
}