<?php

namespace DataAccess\DataMapper;

use Domain\Entities\IDivineEntity;

interface IDataMapper
{
    //find id in table and return it as an entity
    public function find($id, $table);
    //insert/update entity in table
    public function save(IDivineEntity $entity);
    //remove entity from table
    public function remove(IDivineEntity $entity);
}