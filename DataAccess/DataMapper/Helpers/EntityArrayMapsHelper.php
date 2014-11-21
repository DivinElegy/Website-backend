<?php

namespace DataAccess\DataMapper\Helpers;

use ReflectionClass;

class EntityArrayMapsHelper
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
        $entityArray = array();

        // in this case we look in another table for this row's id
        $join_id = $row['id'];        
        $statement = $db->prepare(sprintf('SELECT * from %s WHERE %s=%u',
            $table,
            strtolower($parent . '_id'),
            $join_id
        ));
        
        $statement->execute();
        $rows = $statement->fetchAll();
        
        foreach($rows as $row)
        {         
            $constructors = AbstractPopulationHelper::getConstrutorArray($maps, $this->_entityName, $row, $db);

            if(count($constructors) == 0)
            {
                $class = new $className;
            } else {
                $r = new ReflectionClass($className);
                $class = $r->newInstanceArgs($constructors);
            }
        
            $class->setId($row['id']);
            $entityArray[] =  $class;
        }
        
        return $entityArray;
    }
}