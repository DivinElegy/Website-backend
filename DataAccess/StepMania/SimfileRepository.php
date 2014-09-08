<?php

namespace DataAccess\StepMania;

use DataAccess\IRepository;
use DataAccess\DataMapper\IDataMapper;
use Domain\Entities\IDivineEntity;

//TODO: Implement some sort of caching. Probably OK for now not to worry.
class SimfileRepository implements IRepository
{
    private $dataMapper;
    
    public function __construct(IDataMapper $dataMapper) {
        $this->dataMapper = $dataMapper;
    }
    
    public function find($id) {
        return $this->dataMapper->find($id, 'simfiles');
    }
    
    public function save(IDivineEntity $entity) {
        $this->dataMapper->save($entity);
    }
    
    //TODO: Implement
    public function remove(IDivineEntity $entity) {
        ;
    }
}
