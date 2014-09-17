<?php

namespace DataAccess\DataMapper;

use Domain\Entities\IDivineEntity;

interface IDataMapper
{
    //TODO: Table is the wrong name. We actually give the implementation the entity name and it finds the table from the maps.
    
    //find in table based on criteria in queryString
    public function find($entityName, $queryString);
    //find id in table and return it as an entity
    public function findById($id, $entityName);
    //find rows with id >= id and stop at limit
    public function findRange($id, $entityName, $limit);
    //insert/update entity in table
    public function save(IDivineEntity $entity);
    //remove entity from table
    public function remove(IDivineEntity $entity);
}