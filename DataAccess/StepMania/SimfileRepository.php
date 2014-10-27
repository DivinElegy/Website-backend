<?php

namespace DataAccess\StepMania;

use DataAccess\StepMania\ISimfileRepository;
use DataAccess\DataMapper\IDataMapper;
use DataAccess\Queries\IQueryBuilderFactory;
use DataAccess\Queries\IQueryBuilder;
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
        $queryBuilder = $this->_queryBuilderFactory->createInstance();
        $queryBuilder->where('id', '=', $id);
                
        return $this->_dataMapper->map('Simfile', $queryBuilder);
    }
    
    public function findRange($id, $limit)
    {
        $queryBuilder = $this->_queryBuilderFactory->createInstance();
        $queryBuilder->where('id', '>=', $id)->limit($limit);
                
        return $this->_dataMapper->map('Simfile', $queryBuilder);
    }
    
    public function save(ISimfile $entity) {
        $this->_dataMapper->save($entity);
    }
    
    //TODO: Implement
    public function remove(ISimfile $entity) {
        ;
    }
    
    private function applyConstraintsAndReturn(ISimfileQueryConstraints $constraints = NULL, IQueryBuilder $queryBuilder)
    {
        if($constraints)
        {
            $constraints->applyTo($queryBuilder);
        }
        
        return $this->_dataMapper->map('Simfile', $queryBuilder);
    }
    
    public function findByTitle($title, ISimfileQueryConstraints $constraints = NULL)
    {
        $queryBuilder = $this->_queryBuilderFactory->createInstance();
        $queryBuilder->where('title', 'LIKE', "%%$title%%");
        
        return $this->applyConstraintsAndReturn($constraints, $queryBuilder);
    }
    
    public function findByArtist($artist, ISimfileQueryConstraints $constraints = NULL)
    {
        $queryBuilder = $this->_queryBuilderFactory->createInstance();
        $queryBuilder->where('artist', 'LIKE', "%%$artist%%");
        
        return $this->applyConstraintsAndReturn($constraints, $queryBuilder);
    }
    
    public function findByBpm($high, $low = null, ISimfileQueryConstraints $constraints = NULL)
    {
        $queryBuilder = $this->_queryBuilderFactory->createInstance();
        $queryBuilder->where('bpm_high', '=', $high);
                
        if($low)
        {
            $queryBuilder->where('bpm_low', '=', $low);
        }
        
        return $this->applyConstraintsAndReturn($constraints, $queryBuilder);
    }
    
    public function findByStepArtist($artistName, ISimfileQueryConstraints $constraints = null)
    {
        $queryBuilder = $this->_queryBuilderFactory->createInstance();
        $queryBuilder->join('inner', 'simfiles', 'id', 'steps', 'simfile_id')
                     ->join('inner', 'steps', 'step_artist_id', 'step_artists', 'id')
                     ->where('tag', 'LIKE', "%%$artistName%%");
        
        return $this->applyConstraintsAndReturn($constraints, $queryBuilder);
    }
    
    private function findByDifficultyAndRating($difficulty, $rating, ISimfileQueryConstraints $constraints = null)
    {
        $queryBuilder = $this->_queryBuilderFactory->createInstance();
        $queryBuilder->join('inner', 'simfiles', 'id', 'steps', 'simfile_id')
                     ->where('difficulty', '=', $difficulty)
                     ->where('rating', '=', $rating);
        
        return $this->applyConstraintsAndReturn($constraints, $queryBuilder);
    }
    
    public function findByLightMeter($feet, ISimfileQueryConstraints $constraints = null)
    {
        return $this->findByDifficultyAndRating('light', $feet, $constraints);
    }
    
    public function findByBeginnerMeter($feet, ISimfileQueryConstraints $constraints = null)
    {
        return $this->findByDifficultyAndRating('beginner', $feet, $constraints);
    }
    
    public function findByMediumMeter($feet, ISimfileQueryConstraints $constraints = null)
    {
        return $this->findByDifficultyAndRating('medium', $feet, $constraints);
    }
    
    public function findByHardMeter($feet, ISimfileQueryConstraints $constraints = null)
    {
        return $this->findByDifficultyAndRating('challenge', $feet, $constraints);
    }
    
    public function findByExpertMeter($feet, ISimfileQueryConstraints $constraints = null)
    {
        return $this->findByDifficultyAndRating('expert', $feet, $constraints);
    }
}
