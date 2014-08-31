<?php

namespace DataAccess\DataMapper\Helpers;

class VarcharMapsHelper
{    
    private $_mapIndex;
    private $_tableName;
    private $_accessor;
    
    public function __construct($mapIndex, $accessor, $tableName)
    {
        $this->_mapIndex = $mapIndex;
        
        if($tableName) {
            $this->_tableName = $tableName;
        } else {
            $this->_tableName = strtolower($mapIndex);
        }
        
        if($accessor)
        {
            $this->_accessor = $accessor;
        } else
        {
            $this->_accessor = 'get'. str_replace('_', '', $mapIndex);
        }
    }

    public function getTableName()
    {
        return $this->_tableName;
    }

    public function getColumnName()
    {
        return $this->_mapIndex;
    }        
    
    public function getAccessor()
    {
        return $this->_accessor;
    }
}