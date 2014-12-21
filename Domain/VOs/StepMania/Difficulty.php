<?php

namespace Domain\VOs\StepMania;

use Domain\Exception\InvalidDifficultyException;

class Difficulty implements IDifficulty
{
    protected $stepManiaName;
    protected $itgName;

    //XXX: Common names used in simfiles. We'll map them to standard names.
    //Taken from https://github.com/openitg/openitg/blob/master/src/Difficulty.cpp
    private $_namesToSmNames = array(
	"Beginner"  => 'Beginner',
	"Easy"	    => 'Easy',
	"Basic"	    => 'Easy',
	"Light"	    => 'Easy',
	"Medium"    => 'Medium',
	"Another"   => 'Medium',
	"Trick"	    => 'Medium',
	"Standard"  => 'Medium',
	"Difficult" => 'Medium',
	"Hard"      => 'Hard',
	"Ssr"       => 'Hard',
	"Maniac"    => 'Hard',
	"Heavy"     => 'Hard',
	"Smaniac"   => 'Challenge',
	"Challenge" => 'Challenge',
	"Expert"    => 'Challenge',
	"Oni"       => 'Challenge',
	"Edit"      => 'Edit'
    );
    
    private $_smNamesToItgNames = array(
        'Beginner' => 'Novice',
        'Easy' => 'Easy',
        'Medium' => 'Medium',
        'Hard' => 'Hard',
        'Challenge' => 'Expert',
        'Edit' => 'Edit'
    );

    public function __construct($name) {
        $ucName = ucfirst($name);
        if(array_key_exists($ucName, $this->_namesToSmNames)) {
            $this->stepManiaName = $this->_namesToSmNames[$ucName];
            $this->itgName = $this->_smNamesToItgNames[$this->stepManiaName];
        } else {
            throw new InvalidDifficultyException(sprintf('Invalid difficulty: %s', $name));
        }  
    }
    
    public function getITGName() {
        return $this->itgName;
    }
    
    public function getStepManiaName() {
        return $this->stepManiaName;
    }
}
