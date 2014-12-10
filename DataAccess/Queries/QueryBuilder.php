<?php

namespace DataAccess\Queries;

use DataAccess\Queries\IQueryBuilder;

class QueryBuilder implements IQueryBuilder
{
    private $_queryString = 'SELECT * FROM %s';
    
    protected $_whereClauses = array();
    protected $_limitClause;
    protected $_joinClauses = array();
    protected $_orderClause;
    protected $_countClause;
    protected $_groupClause;
    
    public function buildQuery()
    {        
        $this->applyJoinClauses()
             ->applyWhereClauses()
             ->applyGroupClause()
             ->applyOrderClause()
             ->applyLimitClause()
             ->applyCountClause();
        
        return $this->_queryString;
    }
    
    public function setBaseQuery($baseQuery)
    {
        $this->_queryString = $baseQuery;
    }
    
    public function where($columnName, $operator, $value)
    {
        $this->_whereClauses[] = array('columnName' => $columnName, 'operator' => $operator, 'value' => $value);
        return $this;
    }
    
    public function limit($start, $end = null)
    {
        if($end) 
        {
            $this->_limitClause = sprintf(' LIMIT %u,%u', $start, $end);
            return $this;
        }
        
        $this->_limitClause = sprintf(' LIMIT %u', $start);
        
        return $this;
    }
    
    public function join($type, $tableA, $columnA, $tableB, $columnB)
    {
        $this->_joinClauses[] = sprintf(' %s JOIN %s ON %s.%s = %s.%s', $type, $tableB, $tableA, $columnA, $tableB, $columnB);
        return $this;
    }
    
    public function null($columnName)
    {
        return $this->where($columnName, 'is', null);
    }
    
    public function count($column, $as = null)
    {
        if($as)
        {
            $this->_countClause = sprintf(', count(%s) as %s', $column, $as);
        } else {
            $this->_countClause = sprintf(', count(%s)', $column);
        }
        
        return $this;
    }
    
    public function group($column)
    {
        $this->_groupClause = sprintf(' group by %s', $column);
        
        return $this;
    }
    
    private function applyCountClause()
    {
        $pos = strpos($this->_queryString, '*')+1;
        $this->_queryString = substr_replace($this->_queryString, $this->_countClause, $pos, 0);
        
        return $this;
    }
    
    private function applyGroupClause()
    {
        $this->_queryString .= $this->_groupClause;
        
        return $this;
    }
    
    private function applyJoinClauses()
    {
        foreach($this->_joinClauses as $joinClause)
        {
            $this->_queryString .= $joinClause;
        }
        
        return $this;
    }
    
    public function orderBy($column, $direction) 
    {
        $this->_orderClause = sprintf(' ORDER BY %s %s', $column, $direction);
        
        return $this;
    }
    
    private function applyWhereClauses()
    {
        if(!$this->_whereClauses) return $this;
        
        $this->_queryString .= ' WHERE ';
        
        foreach($this->_whereClauses as $whereClause)
        {
            switch(gettype($whereClause['value']))
            {
                case 'integer':
                    $this->_queryString .= sprintf("%s%s%u", $whereClause['columnName'], $whereClause['operator'], $whereClause['value']) . ' AND ';
                    break;
                case 'string':
                    $this->_queryString .= sprintf("%s %s '%s'", $whereClause['columnName'], $whereClause['operator'], $whereClause['value']) . ' AND ';
                    break;
                case 'NULL':
                    $this->_queryString .= sprintf("%s is null", $whereClause['columnName']) . ' AND ';
                    break;
            }
            
        }
        
        $this->_queryString = rtrim($this->_queryString, ' AND ');
        return $this;
    }
    
    private function applyLimitClause()
    {
        $this->_queryString .= $this->_limitClause;
        return $this;
    }
    
    private function applyOrderClause()
    {
        $this->_queryString .= $this->_orderClause;
        return $this;
    }
    
}