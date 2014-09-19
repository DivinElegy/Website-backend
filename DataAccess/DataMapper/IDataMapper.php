<?php

namespace DataAccess\DataMapper;

use DataAccess\Queries\IQueryBuilder;
use Domain\Entities\IDivineEntity;

interface IDataMapper
{
    //TODO: Table is the wrong name. We actually give the implementation the entity name and it finds the table from the maps.
    
    //find in table based on constraints and return it as entity
    public function map($entityName, IQueryBuilder $queryBuilder);
    //insert/update entity in table
    public function save(IDivineEntity $entity);
    //remove entity from table
    public function remove(IDivineEntity $entity);
}