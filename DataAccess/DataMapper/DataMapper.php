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
        
       // if($queries['TYPE'] == AbstractPopulationHelper::QUERY_TYPE_CREATE)
       // {
            unset($queries['TYPE']);
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
                $statement = $this->_db->prepare($query);
                $statement->execute();
            }
        //}
        
        echo '<pre>';
        print_r($queries);
        echo '</pre>';
    }
    
    //TODO: Implement
    public function remove(IDivineEntity $entity) {
        ;
    }
}
