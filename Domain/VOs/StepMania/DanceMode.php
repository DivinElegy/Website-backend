<?php

namespace Domain\VOs\StepMania;

use Domain\Exception\InvalidDanceModeException;

class DanceMode implements IDanceMode
{
    protected $stepManiaName;
    protected $prettyName;
    
    //XXX: Known step types, taken from https://github.com/stepmania/stepmania/blob/master/src/GameManager.cpp
    private $_nameMap = array(
        'dance-single' => 'Single',
        'dance-double' => 'Double',
        'dance-couple' => 'Couple',
        'dance-solo' => 'Solo',
        'dance-threepanel' => 'Three Panel',
        'dance-routine' => 'Routine',
        'pump-single'  => 'Single',
        'pump-double'  => 'Double',
        'pump-couple' => 'Couple',
        'pump-halfdouble' => 'Half Double',
        'pump-routine' => 'Routine',
        'kb7-single' => 'Single',
        'ez2-single'  => 'Single',
        'ez2-double'  => 'Double',
        'ez2-real' => 'Real',
        'para-single'  => 'Single',
        'para-versus' => 'Versus', //Not in the list but I have seen it in files
        'ds3ddx-single' => 'Single',
        'bm-single' => 'Single', //Not in the list but I have seen it in files
        'bm-single5' => 'Single 5 Key',
        'bm-single7' => 'Single 7 Key',
        'bm-double' => 'Double', //Not in the list, and I haven't seen it, but I feel like it must exist lol
        'bm-double5' => 'Double 5 Key',
        'bm-double7' => 'Double 7 Key',
        'bm-versus5' => 'Versus 5 Key',
        'bm-versus7' => 'Versus 7 Key',
        //Seems like bm may have been called iidx or was changed to iidx at some point so...
        'iidx-single' => 'Single', //Not in the list but I have seen it in files
        'iidx-single5' => 'Single 5 Key',
        'iidx-single7' => 'Single 7 Key',
        'iidx-double' => 'Double', //Not in the list, and I haven't seen it, but I feel like it must exist lol
        'iidx-double5' => 'Double 5 Key',
        'iidx-double7' => 'Double 7 Key',
        'iidx-versus5' => 'Versus 5 Key',
        'iidx-versus7' => 'Versus 7 Key',
        'maniax-single' => 'Single',
        'maniax-double' => 'Double',
        'techno-single4' => 'Single 4 Panel',
        'techno-single5' => 'Single 5 Panel',
        'techno-single8' => 'Single 8 Panel',
        'techno-double4' => 'Double 4 Panel',
        'techno-double5' => 'Double 5 Panel',
        'techno-double8' => 'Double 8 Panel',
        'pnm-five' => 'Five Key',
        'pnm-nine' => 'Nine Key',
        'lights-cabinet' => 'Cabinet Lights'
    );
    
    private $_smGameNameToNiceName = array(
        'dance' => 'In The Groove',
        'pump' => 'Pump It Up',
        'ez2' => 'EZ2Dancer',
        'para' => 'ParaParaParadise',
        'bm' => 'Beatmania',
        'iidx' => 'Beatmania',
        'maniax' => 'Dance Maniax',
        'techno' => 'TechnoMotion',
        'pnm' => 'Pop\'n Music',
        'ds3ddx' => 'Dance Station 3DDX',
        'kb7' => 'Keybeat',
        'lights' => false
    );
            
    public function __construct($stepManiaName)
    {
        if(array_key_exists($stepManiaName, $this->_nameMap)) {
            $this->stepManiaName = $stepManiaName;
            $this->prettyName = $this->_nameMap[$stepManiaName];
        } else {
            throw new InvalidDanceModeException(sprintf('Invalid dance mode %s', $stepManiaName));
        }        
    }
    
    public function getStepManiaName()
    {
        return $this->stepManiaName;
    }
    
    public function getPrettyName()
    {
        return $this->prettyName;
    }
    
    public function getGame()
    {
        $game = explode('-', $this->stepManiaName)[0];
        return $this->_smGameNameToNiceName[$game];
    }
}
