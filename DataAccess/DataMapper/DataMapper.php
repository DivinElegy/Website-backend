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
        $mergeMap = array();
        $flattened = array();
        
        foreach($queries as $index => $query)
        {
            $this_table = $query['table'];
            $this_columns = $query['columns'];

            
            for($i = $index+1; $i<count($queries); $i++)
            {
                if(
                    $queries[$i]['table'] == $this_table &&
                    !array_key_exists($i, $mergeMap) &&
                    !isset($query['id'])) //only merge create queries, updates are fine to run multiple times
                {
                    //XXX: This whole biz is tricky. Basically the problem is that when creating a new simfile,
                    //the datamapper spews out a bunch of create queries. When parsing a simfile for example, there can
                    //be huge redundency - it may produce 5 queries that all create the same step artist, for example.
                    //We attempt to flatten equivalent queries. Originally I was basing it purely on the table name or something,
                    //but that is not enough. In the case of steps, it ends up mergin all the steps together, so we need to
                    //check if the arrays are equal as well, which is what this does.
                    if($this_columns === $queries[$i]['columns'])
                    {
                        //need to keep track of what we merged as future queries might reference the old ids.
                        $mergeMap[$i] = $index;
                    }
                    
                    //XXX: Another thing that might happen is we have to create queries running on the same table, but with unique columns.
                    //In this case, we can take the columns of one and put it into the other. Otherwise we create two records when we really
                    //should have only one. An example of this is when a user is created, a query to add the country to users_meta is run,
                    //and then _another_ to add firstname, lastname and user_id. It should really all be done in one query.
                    
                    //Make sure both queries are for the same table, and the both relate back to the main query
                    if($this_table == $queries[$i]['table'] && in_array('%MAIN_QUERY_ID%', $this_columns) && in_array('%MAIN_QUERY_ID%', $queries[$i]['columns']))
                    {
                        $this_column_names = array_keys($this_columns);
                        $other_column_names = array_keys($queries[$i]['columns']);
                        $combine = true;
                        foreach($this_column_names as $column_name)
                        {
                            if($this_columns[$column_name] != '%MAIN_QUERY_ID%' && in_array($column_name, $other_column_names))
                            {
                                $combine = false;
                            }
                        }
                        
                        if($combine)
                        {
                            $this_columns = array_merge($this_columns, $queries[$i]['columns']);
                            $mergeMap[$i] = $index;
                        }
                    }
                }
            }

            if(!array_key_exists($index, $mergeMap)) {
                $prepared = isset($query['prepared']) ? $query['prepared'] : null;
                $id = isset($query['id']) ? $query['id'] : null;

                $flattened[] = array(
                    'columns' => $this_columns,
                    'table' => $this_table,
                    'prepared' => $prepared,
                    'id' => $id
                );
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
                    //$refIndex = $index+1; This was being used as the index for idMap below. I have nfi why I was adding 1.
                    $idMap['INDEX_REF_' . $index] = $this->_db->lastInsertId();
                    
                    foreach($mergeMap as $oldIndex => $mergedIndex) {
                        if($mergedIndex == $index) {
                            $idMap['INDEX_REF_' . $oldIndex] = $idMap['INDEX_REF_' . $index];
                        }
                    }
                    
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
                $statement = $this->_db->prepare($query);
                $statement->execute();
            }
        //}
        
        if(!$entity->getId()) $entity->setId(end($idMap));
        
        return $entity;
    }
    
    //TODO: Implement
    public function remove(IDivineEntity $entity) {
        ;
    }
}
