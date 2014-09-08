<?php

namespace DataAccess\StepMania;

use Domain\Entities\StepMania\ISimfile;

interface ISimfileRepository
{
    public function find($id);
    public function save(ISimfile $entity);
    public function remove(ISimfile $entity);
}
    
