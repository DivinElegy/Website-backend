<?php

namespace DataAccess\StepMania;

use DataAccess\StepMania\IPackRepository;
use DataAccess\DataMapper\IDataMapper;
use DataAccess\Queries\IQueryBuilderFactory;
use Domain\Entities\StepMania\IPack;

//TODO: Implement some sort of caching. Probably OK for now not to worry.
class PackRepository implements IPackRepository
{
    private $_dataMapper;
    private $_queryBuilderFactory;
    
    public function __construct(IDataMapper $dataMapper, IQueryBuilderFactory $queryBuilderFactory) {
        $this->_dataMapper = $dataMapper;
        $this->_queryBuilderFactory = $queryBuilderFactory;
    }
    
    public function findById($id) {
        $queryBuilder = $this->_queryBuilderFactory->createInstance();
        $queryBuilder->where('id', '=', $id);
        
        $result = $this->_dataMapper->map('Pack', $queryBuilder);
        return reset($result);
    }
    
    public function findRange($id, $limit)
    {
        $queryBuilder = $this->_queryBuilderFactory->createInstance();
        $queryBuilder->where('id', '>=', $id)->limit($limit);
                
        return $this->_dataMapper->map('Pack', $queryBuilder);
    }
    
    public function save(IPack $entity) {
        return $this->_dataMapper->save($entity);
    }
    
    //TODO: Implement
    public function remove(IPack $entity) {
        ;
    }
    
    public function findByTitle($title)
    {
        $queryBuilder = $this->_queryBuilderFactory->createInstance();
        $queryBuilder->where('title', 'LIKE', "%%$title%%");
        
        return $this->_dataMapper->map('Pack', $queryBuilder);
    }
    
    public function findByContributor($artistName)
    {
        $queryBuilder = $this->_queryBuilderFactory->createInstance();
        $queryBuilder->join('inner', 'packs', 'id', 'simfiles', 'pack_id')
                     ->join('inner', 'simfiles', 'id', 'steps', 'simfile_id')
                     ->join('inner', 'steps', 'step_artist_id', 'step_artists', 'id')
                     ->where('tag', 'LIKE', "%%$artistName%%");
        
        return $this->_dataMapper->map('Pack', $queryBuilder);
    }
}
