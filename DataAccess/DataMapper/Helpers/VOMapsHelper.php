<?php

namespace DataAccess\DataMapper\Helpers;

use Exception;
use ReflectionClass;

class VOMapsHelper
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
        
        // If the table we already have contains the id of a row we need in
        // another table        
        //if(isset($row[$this->_tableName . '_id'])) {
        if(array_key_exists($this->_tableName . '_id', $row)) { //this is a better choice as somtimes the array key is set, but is equal to null, and isset doesn't like that
            $join_id = $row[$this->_tableName . '_id'];
            $statement = $db->prepare(sprintf('SELECT * from %s WHERE id=%u',
                $table,
                $join_id));
            
            $statement->execute();
            $row = $statement->fetch();
        } elseif($maps[$this->_voName]['table'] != $maps[$parent]['table']) {            
            // in this case we look in another table for this row's id
            $join_id = $row['id'];
            $statement = $db->prepare(sprintf('SELECT * from %s WHERE %s=%u',
                $table,
                strtolower($parent . '_id'),
                $join_id
            ));
            
            $statement->execute();
            $row = $statement->fetch();
        }
        
        $constructors = AbstractPopulationHelper::getConstrutorArray($maps, $this->_voName, $row, $db);
                     
        if(count($constructors) == 0)
        {
            return new $className;
        } else {
            try {
                $r = new ReflectionClass($className);
                return $r->newInstanceArgs($constructors);
            } catch (Exception $e) {
                return null;
            }
        }
    }
}
