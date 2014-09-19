<?php

namespace DataAccess\StepMania;

use DataAccess\StepMania\ISimfileRepository;
use DataAccess\DataMapper\IDataMapper;
use DataAccess\Queries\StepMania\ISimfileQueryConstraints;
use Domain\Entities\StepMania\ISimfile;

//TODO: Implement some sort of caching. Probably OK for now not to worry.
class SimfileRepository implements ISimfileRepository
{
    private $_dataMapper;
    
    public function __construct(IDataMapper $dataMapper) {
        $this->_dataMapper = $dataMapper;
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
        //TODO: Should I inject a factory, and then make $constraints if it isn't given?
        if($constraints)
        {
            $queryString = $constraints->where('title', 'LIKE', "%%$title%%") //TODO: Should I make a like method that handles adding the %% ?
                                       ->applyTo('SELECT * from %s');
        } else {
            //It would avoid this, or rather I could put this in the constraints class
            $queryString = "SELECT * FROM %s WHERE title LIKE '%$title%'";
        }
        
        //is it better to pass in constraints object?
        //could have a default "select * from %s" in the constraints object which could be overwritten via a method.
        //-no more need for applyTo, just go $constratints->getQuery
        //maybe it should no longer be constraints but instead queryBuilder
        
        /**
         * have this class contain a queryBuilderFactory and then have constraintsClass 
         * go in through methods which act on the query, adding in constraints.
         */
        return $this->_dataMapper->map('Simfile', $queryString);
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
