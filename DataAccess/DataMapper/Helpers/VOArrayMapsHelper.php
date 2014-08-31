<?php

namespace DataAccess\DataMapper\Helpers;

use ReflectionClass;

class VOArrayMapsHelper
{
    private $_voName;
    private $_tableName;
    private $_accessor;
    
    public function __construct($voName, $accessor, $tableName)
    {
        $this->_voName = $voName;
        
        if($tableName) {
            $this->_tableName = $tableName;
        } else {
            $this->_tableName = strtolower($voName);
        }
        
        if($accessor)
        {
            $this->_accessor = $accessor;
        } else
        {
            $this->_accessor = 'get'. str_replace('_', '', $voName);
        }
    }

    public function getAccessor()
    {
        return $this->_accessor;
    }
    
    public function getVOName()
    {
        return $this->_voName;
    }
    
    public function getTableName()
    {
        return $this->_tableName;
    }
    
    public function populate($maps, $db, $parent, $row)
    {
        $className = $maps[$this->_voName]['class'];
        $table = $maps[$this->_voName]['table'];
        $VOArray = array();
        
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
            $constructors = AbstractPopulationHelper::getConstrutorArray($maps, $this->_voName, $row, $db);
            
            if(count($constructors) == 0)
            {
                $VOArray[] = new $className;
            } else {
                $r = new ReflectionClass($className);
                $VOArray[] = $r->newInstanceArgs($constructors);
            }
        }
        
        return $VOArray;
    }
}