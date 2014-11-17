<?php

namespace DataAccess\DataMapper;

use Domain\Entities\IDivineEntity;
use DataAccess\IDatabaseFactory;
use DataAccess\DataMapper\IDataMapper;
use DataAccess\Queries\IQueryBuilder;
use DataAccess\DataMapper\Helpers\AbstractPopulationHelper;
use ReflectionClass;

class DataMapper implements IDataMapper
{
    private $_db;
    private $_maps;
    
    public function __construct($maps, IDatabaseFactory $databaseFactory)
    {
        $this->_db = $databaseFactory->createInstance();        
        $this->_maps = include $maps;
    }
    
    public function map($entityName, IQueryBuilder $queryBuilder)
    {
        $queryString = $queryBuilder->buildQuery();
        
        $statement = $this->_db->prepare(sprintf($queryString,
            $this->_maps[$entityName]['table']
        ));
        
        $statement->execute();
        $rows = $statement->fetchAll();
        
        $entities = array();
        
        foreach($rows as $row)
        {
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
            $entities[$row['id']] = $class;
        }

        return $entities;
    }
        
    public function save(IDivineEntity $entity)
    {
        $queries = AbstractPopulationHelper::generateUpdateSaveQuery($this->_maps, $entity, $entity->getId(), $this->_db);
        
        $flattened = array();
        $flattened_tables = array();
        foreach($queries as $index => $query)
        {
            $this_table = $query['table'];
            $this_columns = $query['columns'];
            
            for($i = $index+1; $i<count($queries); $i++)
            {
                if($queries[$i]['table'] == $this_table && !in_array($queries[$i]['table'], $flattened_tables) && !isset($query['id'])) //only merge create queries, updates are fine to run multiple times
                {
                    $this_columns = array_merge($this_columns, $queries[$i]['columns']);
                }
            }
            
            if(!in_array($this_table, $flattened_tables))
            {
                $flattened_tables[] = $this_table;
                $prepared = isset($query['prepared']) ? $query['prepared'] : null;
                $id = isset($query['id']) ? $query['id'] : null;
                $flattened[] = array('columns' => $this_columns, 'table' => $this_table, 'prepared' => $prepared, 'id' => $id);
            }
        }
        
        $queries = array();
        
        foreach($flattened as $info)
        {
            if(isset($info['id']))
            {
                $query = $info['prepared'];
                $query = substr($query, 0, -2);
                $query .= sprintf(' WHERE id=%u', $info['id']);
            } else {
                $query = sprintf('INSERT INTO %s (%s) VALUES (%s)',
                $info['table'],
                implode(', ', array_keys($info['columns'])),
                implode(', ', $info['columns']));
            }
            
            $queries[] = $query;
        }
        
       // if($queries['TYPE'] == AbstractPopulationHelper::QUERY_TYPE_CREATE)
       // {
            $idMap = [];
            foreach($queries as $index => $query)
            {                                
                $runQuery = true;
                if (preg_match_all('/'.preg_quote('%').'(.*?)'.preg_quote('%').'/s', $query, $matches)) {
                    foreach($matches[1] as $index_ref)
                    {
                        if($index_ref != 'MAIN_QUERY_ID')
                        {
                            $index_id = str_replace('INDEX_REF_', '', $index_ref);
                            $query = str_replace('%INDEX_REF_' . $index_id . '%', $idMap['INDEX_REF_' . $index_id], $query);
                        } else {
                            $runQuery = false;
                        }
                    }
                }

                if($runQuery)
                {
                    $statement = $this->_db->prepare($query);
                    $statement->execute();
                    $refIndex = $index+1;
                    $idMap['INDEX_REF_' . $refIndex] = $this->_db->lastInsertId();
                    unset($queries[$index]);
                } else {
                    //update query so that other references are resolved.
                    $queries[$index] = $query;
                }
            }
            
            //at this point we have queries left that depend on the main query id
            foreach($queries as $query)
            {
                $query = str_replace('%MAIN_QUERY_ID%', end($idMap), $query);
                echo $query;
                $statement = $this->_db->prepare($query);
                $statement->execute();
            }
        //}
            
        $entity->setId(end($idMap));
        
        return $entity;
    }
    
    //TODO: Implement
    public function remove(IDivineEntity $entity) {
        ;
    }
}
