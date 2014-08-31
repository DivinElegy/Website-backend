<?php

namespace DataAccess\DataMapper\Helpers;

use ReflectionClass;

class EntityMapsHelper
{
    private $_entityName;
    private $_tableName;
    private $_accessor;
    
    public function __construct($entityName, $accessor, $tableName)
    {
        $this->_entityName = $entityName;
        
        if($tableName) {
            $this->_tableName = $tableName;
        } else {
            $this->_tableName = strtolower($entityName);
        }
        
        if($accessor)
        {
            $this->_accessor = $accessor;
        } else
        {
            $this->_accessor = 'get'. str_replace('_', '', $entityName);
        }
    }
    
    public function getEntityName()
    {
        return $this->_entityName;
    }
    
    public function getAccessor()
    {
        return $this->_accessor;
    }
    
    public function getTableName()
    {
        return $this->_tableName;
    }
    
    public function populate($maps, $db, $parent, $row)
    {
        $className = $maps[$this->_entityName]['class'];
        $table = $maps[$this->_entityName]['table'];

        // If the table we already have contains the id of a row we need in
        // another table
        if(isset($row[$this->_tableName . '_id'])) {
            $join_id = $row[$this->_tableName . '_id'];
            $statement = $db->prepare(sprintf('SELECT * from %s WHERE id=%u',
                $table,
                $join_id));
            $statement->execute();
            $row = $statement->fetch();
        }
        
        $constructors = AbstractPopulationHelper::getConstrutorArray($maps, $this->_entityName, $row, $db);

        if(count($constructors) == 0)
        {
            $class = new $className;
        } else {
            $r = new ReflectionClass($className);
            $class = $r->newInstanceArgs($constructors);
        }
        
        $class->setId($row['id']);
        return $class;
    }
}

