<?php

namespace DataAccess\Queries\StepMania;

use DataAccess\Queries\QueryConstraints;
use DataAccess\Queries\StepMania\ISimfileQueryConstraints;

class SimfileQueryConstraints extends QueryConstraints implements ISimfileQueryConstraints
{        
    public function hasFgChanges($bool)
    {
        return $this->where('fg_changes', '=', (int)$bool);
    }
    public function hasBgChanges($bool)
    {
        return $this->where('bg_changes', '=', (int)$bool);
    }
    
    public function stepsHaveRating($rating)
    {
        return $this->join('INNER', 'simfiles', 'id', 'steps', 'simfile_id')
                    ->where('steps.rating', '=', $rating);
    }
    public function hasDifficulty($difficulty){}
    
    public function bpm($bpm)
    {
        return;
    }
}
