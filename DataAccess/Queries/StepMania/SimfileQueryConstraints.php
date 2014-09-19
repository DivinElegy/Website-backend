<?php

namespace DataAccess\Queries\StepMania;

use DataAccess\Queries\IQueryBuilder;
use DataAccess\Queries\StepMania\ISimfileQueryConstraints;

class SimfileQueryConstraints implements ISimfileQueryConstraints
{        
    
    private $_queryBuilder;
    private $_fgChanges;
    private $_bgChanges;
    private $_stepRating;
    
    public function applyTo(IQueryBuilder $queryBuilder)
    {
        $this->_queryBuilder = $queryBuilder;
        
        $this->applyStepsRating()
             ->applyBgChanges()
             ->applyFgChanges();
    }
    
    public function hasFgChanges($bool)
    {
        $this->_fgChanges = (int)$bool;
        return $this;
    }
    public function hasBgChanges($bool)
    {
        $this->_bgChanges = (int)$bool;
        return $this;
    }
    
    public function stepsHaveRating($rating)
    {
        $this->_stepRating = $rating;
        return $this;
    }
    public function hasDifficulty($difficulty){}
    
    public function bpm($bpm)
    {
        return;
    }
    
    private function applyFgChanges()
    {
        if($this->_fgChanges) {
            $this->_queryBuilder->where('fg_changes', '=', $this->_fgChanges);
        }
        
        return $this;
    }
    
    private function applyBgChanges()
    {
        if($this->_bgChanges) {
            $this->_queryBuilder->where('bg_changes', '=', $this->_bgChanges);
        }
        
        return $this;
    }
    
    private function applyStepsRating()
    {
        if($this->_stepRating)
        {
            $this->_queryBuilder->join('INNER', 'simfiles', 'id', 'steps', 'simfile_id')
                                ->where('steps.rating', '=', $this->_stepRating);
        }
        
        return $this;
    }
}
