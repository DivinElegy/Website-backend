<?php

namespace DataAccess\Queries\StepMania;

use DataAccess\Queries\IQueryConstraints;

interface ISimfileQueryConstraints extends IQueryConstraints
{
    public function hasFgChanges($bool);
    public function hasBgChanges($bool);
    public function stepsHaveRating($rating);
    public function hasDifficulty($difficulty);
}