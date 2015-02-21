<?php

namespace Services;

use Domain\Entities\StepMania\IPack;

interface ICacheUpdater
{
    public function insert(IPack $pack);
    public function update();
}
