<?php

namespace DataAccess\StepMania;

use Domain\Entities\StepMania\Simfile;

interface ISimfileRepository
{
    public function find($id);
    public function save(Simfile $simfile);
    public function remove(Simfile $simfile);
}
