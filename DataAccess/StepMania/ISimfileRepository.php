<?php

namespace DataAccess\StepMania;

use DataAccess\IRepository;
use DataAccess\Queries\StepMania\ISimfileQueryConstraints;
use Domain\Entities\StepMania\ISimfile;

interface ISimfileRepository extends IRepository
{
    public function findByFileId($id);
    public function findByTitle($title, ISimfileQueryConstraints $constraints);
    public function findByArtist($artist, ISimfileQueryConstraints $constraints);
    public function findByBpm($high, $low, ISimfileQueryConstraints $constraints);
    public function findByStepArtist($artistName, ISimfileQueryConstraints $constraints);
    public function findByLightMeter($feet, ISimfileQueryConstraints $constraints);
    public function findByBeginnerMeter($feet, ISimfileQueryConstraints $constraints);
    public function findByMediumMeter($feet, ISimfileQueryConstraints $constraints);
    public function findByHardMeter($feet, ISimfileQueryConstraints $constraints);
    public function findByExpertMeter($feet, ISimfileQueryConstraints $constraints);
    public function save(ISimfile $entity);
    public function remove(ISimfile $entity);
}

    
