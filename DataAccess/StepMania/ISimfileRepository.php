<?php

namespace DataAccess\StepMania;

use DataAccess\IRepository;
use DataAccess\Queries\StepMania\ISimfileQueryConstraints;
use Domain\Entities\StepMania\ISimfile;

interface ISimfileRepository extends IRepository
{
    public function findByTitle($title, ISimfileQueryConstraints $constraints);
    public function findByArtist($artist);
    public function findByBpm($high, $low);
    public function findByStepArtist($artistName);
    public function findByLightMeter($feet);
    public function findByBeginnerMeter($feet);
    public function findByMediumMeter($feet);
    public function findByHardMeter($feet);
    public function findByExpertMeter($feet);
    public function save(ISimfile $entity);
    public function remove(ISimfile $entity);
}

    
