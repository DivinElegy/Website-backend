<?php

namespace DataAccess\StepMania;

use DataAccess\IRepository;
use Domain\Entities\StepMania\IPack;

interface IPackRepository extends IRepository
{
    public function findByFileId($id);
    public function findByTitle($title);
    public function findByContributor($contributor);
    public function save(IPack $entity);
    public function remove(IPack $entity);
}

    
