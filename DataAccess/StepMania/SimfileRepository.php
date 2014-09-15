<?php

namespace DataAccess\StepMania;

use DataAccess\StepMania\ISimfileRepository;
use DataAccess\DataMapper\IDataMapper;
use Domain\Entities\StepMania\ISimfile;

//TODO: Implement some sort of caching. Probably OK for now not to worry.
class SimfileRepository implements ISimfileRepository
{
    private $dataMapper;
    
    public function __construct(IDataMapper $dataMapper) {
        $this->dataMapper = $dataMapper;
    }
    
    public function find($id) {
        return $this->dataMapper->find($id, 'Simfile');
    }
    
    public function findRange($id, $limit)
    {
        return $this->dataMapper->findRange($id, 'Simfile', $limit);
    }
    
    public function save(ISimfile $entity) {
        $this->dataMapper->save($entity);
    }
    
    //TODO: Implement
    public function remove(ISimfile $entity) {
        ;
    }
}
