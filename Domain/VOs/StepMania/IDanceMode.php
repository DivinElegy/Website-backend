<?php

namespace Domain\VOs\StepMania;

interface IDanceMode
{
    public function getStepManiaName();
    public function getPrettyName();
    public function getGame();
}
