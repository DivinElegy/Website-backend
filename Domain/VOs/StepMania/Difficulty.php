<?php

namespace Domain\VOs\StepMania;

use Domain\Exception\InvalidDifficultyException;

class Difficulty implements IDifficulty
{
    protected $stepManiaName;
    protected $itgName;
    
    private $_nameMap = array(
        'Light' => 'Novice',
        'Beginner' => 'Novice',
        'Easy' => 'Easy',
        'Medium' => 'Medium',
        'Hard' => 'Hard',
        'Challenge' => 'Expert',
        'Edit' => 'Edit'
    );
    
    public function __construct($stepManiaName) {
        if(array_key_exists($stepManiaName, $this->_nameMap)) {
            $this->stepManiaName = $stepManiaName;
            $this->itgName = $this->_nameMap[$stepManiaName];
        } else {
            throw new InvalidDifficultyException(sprintf('Invalid difficulty: %s', $stepManiaName));
        }  
    }
    
    public function getITGName() {
        return $this->itgName;
    }
    
    public function getStepManiaName() {
        return $this->stepManiaName;
    }
}
