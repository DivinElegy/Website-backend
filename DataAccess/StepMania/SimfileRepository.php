<?php

namespace DataAccess\StepMania;

use DataAccess\StepMania\ISimfileRepository;
use DataAccess\DataMapper\IDataMapper;
use DataAccess\Queries\IQueryBuilderFactory;
use DataAccess\Queries\StepMania\ISimfileQueryConstraints;
use Domain\Entities\StepMania\ISimfile;

//TODO: Implement some sort of caching. Probably OK for now not to worry.
class SimfileRepository implements ISimfileRepository
{
    private $_dataMapper;
    private $_queryBuilderFactory;
    
    public function __construct(IDataMapper $dataMapper, IQueryBuilderFactory $queryBuilderFactory) {
        $this->_dataMapper = $dataMapper;
        $this->_queryBuilderFactory = $queryBuilderFactory;
    }
    
    public function findById($id) {
        return $this->_dataMapper->map(
            'Simfile',
            'SELECT * FROM %s WHERE id=' . $id
        );
    }
    
    public function findRange($id, $limit)
    {
        return $this->_dataMapper->findRange(
            'Simfile',
            'SELECT * FROM %s WHERE id>=' . $id . ' LIMIT ' . $limit
        );
    }
    
    public function save(ISimfile $entity) {
        $this->_dataMapper->save($entity);
    }
    
    //TODO: Implement
    public function remove(ISimfile $entity) {
        ;
    }
    
    public function findByTitle($title, ISimfileQueryConstraints $constraints = NULL)
    {
        $queryBuilder = $this->_queryBuilderFactory->createInstance();
        $queryBuilder->where('title', 'LIKE', "%%$title%%");
        
        if($constraints)
        {
            $constraints->applyTo($queryBuilder);
        }

        return $this->_dataMapper->map('Simfile', $queryBuilder);
    }
    
    public function findByArtist($artist){}
    public function findByBpm($high, $low){}
    public function findByStepArtist($artistName){}
    public function findByLightMeter($feet){}
    public function findByBeginnerMeter($feet){}
    public function findByMediumMeter($feet){}
    public function findByHardMeter($feet){}
    public function findByExpertMeter($feet){}
}
