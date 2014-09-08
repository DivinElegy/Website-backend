<?php

namespace DataAccess;

use Domain\Entities\IDivineEntity;

interface IRepository
{
    public function find($id);
    public function save(IDivineEntity $entity);
    public function remove(IDivineEntity $entity);
}
    
