<?php

namespace DataAccess\DataMapper;

use DataAccess\DataMapper\Helpers\AbstractPopulationHelper;
use ReflectionClass;
use Iterator;

class LazyLoadedEntities implements Iterator
{
    private $_maps;
    private $_entityName;
    private $_rows;
    private $_rowIndex = 0;
    private $_loadedEntitiesIndex = 0;
    private $_loadedEntities = array();
    private $_db;
    
    public function __construct(array $rows, $entityName, $maps, $db)
    {
        $this->_rows = $rows;
        $this->_entityName = $entityName;
        $this->_maps = $maps;
        $this->_db = $db;
    }
    
    public function current()
    {
        return $this->_loadedEntities[$this->_loadedEntitiesIndex];
    }
    
    public function key()
    {
        return $this->_loadedEntitiesIndex;
    }
    
    public function next()
    {
        $keys = array_keys($this->_loadedEntities);
        $pos = array_search($this->_loadedEntitiesIndex, $keys);
        if(isset($keys[$pos + 1]))
        {
            $this->_loadedEntitiesIndex = $keys[$pos+1];
        } else {
            $this->mapEntities(); //sets the loaded entites index
        }
    }
    
    public function rewind()
    {
        $this->_rowIndex = 0;
        $this->mapEntities();
    }
    
    public function valid()
    {
        //next will always load more entities when it runs out, therefore if
        //we have an empty loadEntities array, it means there were no more and we are done.
        if($this->_loadedEntities)
        {
            return true;
        }
        
        return false;
    }

    //unsets the current entities array, and maps in the next 10
    private function mapEntities()
    {
        $numToMap = 50;
        $tick = 0;
        
        unset($this->_loadedEentities);
        $this->_loadedEntities = array();
        
        $entityName = $this->_entityName;
        for($i = $this->_rowIndex; $i<$this->_rowIndex+$numToMap; $i++)
        {
            if(isset($this->_rows[$i]))
            {
                $row = $this->_rows[$i];
                $className = $this->_maps[$entityName]['class']; //the entity to instantiate and return
                $constructors = AbstractPopulationHelper::getConstrutorArray($this->_maps, $entityName, $row, $this->_db);

                if(count($constructors) == 0)
                {
                    $class = new $className;            
                } else {
                    $r = new ReflectionClass($className);
                    $class = $r->newInstanceArgs($constructors);
                }

                $class->setId($row['id']);
                $this->_loadedEntities[$row['id']] = $class;
                
                if($tick == 0) $this->_loadedEntitiesIndex = $row['id'];
                $tick++;
            }
        }
        
        $this->_rowIndex += $numToMap;
    }
}