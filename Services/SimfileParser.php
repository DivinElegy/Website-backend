<?php

namespace Services;

use Exception;
use Services\ISimfileParser;
use Services\IStatusReporter;

class InvalidSmFileException extends Exception{}

class SimfileParser implements ISimfileParser
{
    
    private $_smFileLines;
    private $_statusReporter;
    
    public function __construct(IStatusReporter $statusReporter)
    {
        $this->_statusReporter = $statusReporter;
    }
        
    public function parse($simfileData)
    {
        $this->_smFileLines = explode(";", $simfileData);
    }
    
    public function banner()
    {
        return $this->extractKey('BANNER') ?: null;
    }
    
    public function title()
    {
        $title = $this->extractKey('TITLE');
        if(!$title) throw new InvalidSmFileException('Invalid SM file. TITLE missing');

        return $title;
    }
    
    public function artist()
    {
        $artist = $this->extractKey('ARTIST');
        //XXX: Artist can be null
        if(!$artist)return null;
        
        //XXX: UTF8 encode to deal with unusual character that crop up in weeaboo shit.
        return new \Domain\VOs\StepMania\Artist($artist);
    }
    
    public function stops()
    {
        $stops = $this->extractKey('STOPS');
        
        //XXX: SM files can be missing stops.
        //if($stops === false) throw new Exception ('Invalid SM file. STOPS missing');
        
        return (bool)$stops;
    }
    
    public function fgChanges()
    {
        $fgChanges = $this->extractKey('FGCHANGES');
        //XXX: Looks like fgChanges is allowed to be missing.
        //if($fgChanges === false) throw new Exception ('Invalid SM file. FGCHANGES missing');
        
        return (bool)$fgChanges;
    }
    
    public function bpm()
    {
        $displayBpm = $this->extractKey('DISPLAYBPM');
        
        if($displayBpm)
        {
            $bpmRange = explode(":",$displayBpm);
            $bpmRange[1] = isset($bpmRange[1]) && is_numeric($bpmRange[1]) ?: $bpmRange[0];
        }
        
        //XXX: Originally I had an else statement for this. But turns out some SM's have * as the display bpm
        //so I just check if we don't have a displayBPM OR the displayBPM is not numeric.
        if(!$displayBpm || !is_numeric($bpmRange[0]))
        {
            $bpms = $this->extractKey('BPMS');
            $bpmRange = $this->parseBpms($bpms);
        }
        
        //XXX: I have nfi why I made the BPM VO high-low instead of low-high in the constructor but yolo
        return new \Domain\VOs\StepMania\BPM($bpmRange[1], $bpmRange[0]);
    }
    
    public function bgChanges()
    {
        $bgChanges = $this->extractKey('BGCHANGES');
        
        //XXX: BGChanges can be missing
        //if($bgChanges === false) throw new Exception ('Invalid SM file. BGCHANGES missing');
        
        return (bool)$bgChanges;
    }
    
    public function bpmChanges() 
    {
        $bpms = $this->extractKey('BPMS');
        
        //XXX: BPMS can be missing.
        //if(!$bpms) throw new Exception ('Invalid SM file. BPMS missing');
        
        $bpmRange = $this->parseBpms($bpms);
        //XXX: We have bpm changes when the high and low bpms are different.
        return $bpmRange[0] != $bpmRange[1];
    }
    
    public function subtitle()
    {
        $subtitle = $this->extractKey('SUBTITLE');
        if(!$subtitle) return null;
        
        return $subtitle;
    }
    
    function steps()
    {
        if(empty($this->_smFileLines)) throw new Exception('SM file data not set.');
        $allSteps = array();

        foreach ($this->_smFileLines as $line)
        {
            $pos = strpos($line, '#NOTES:');
            if ($pos !== false)
            {
                $noteData = trim(substr($line, $pos + 9));
                $steps = $this->stepchartFromNoteData($noteData);

                //XXX: Sometimes we get a cabinet lights chart, those return false for getGame.
                //We don't want to store cabinet lights, so just ignore it.
                if($steps->getMode()->getGame()) $allSteps[] = $steps;
            }
        }
        
        if(empty($allSteps)) throw new InvalidSmFileException('Invalid Sm file. NOTES missing');
        return $allSteps;
    }
    
    private function stepchartFromNoteData($noteData)
    {
        $stepData = array_map('trim', explode(':', $noteData));
        return new \Domain\VOs\StepMania\StepChart(
            new \Domain\VOs\StepMania\DanceMode($stepData[0]),
            new \Domain\VOs\StepMania\Difficulty($stepData[2]),
            empty($stepData[1]) ? null : new \Domain\VOs\StepMania\StepArtist($stepData[1]),
            //XXX: Fuck you whoever made me do this. http://dev.mysql.com/doc/refman/5.5/en/integer-types.html
            //XXX: Originally I was using MySQL unsigned bigint max value, but PHP does not have unsigned ints so
            $stepData[3] <= 9223372036854775807 ? $stepData[3] : 9223372036854775807
        );
    }
    
    private function extractKey($key)
    {
        //Throw regular exception here, this has nothing to do with the SM file.
        if(empty($this->_smFileLines)) throw new Exception('SM file data not set.');
        
        foreach ($this->_smFileLines as $line)
        {
            $pos = strpos($line, '#' . $key . ':');
            if ($pos !== false) return trim(substr($line, $pos + strlen($key) + 2));
        }
        
        return false;
    }
    
    private function parseBpms($bpms)
    {
        $bpms = explode(",", $bpms);
        $bpmRange = array('high' => null, 'low' => null);

        foreach($bpms as $bpm)
        {
            $bpmMeasure = explode('=', $bpm);
            $bpmValue = floatval($bpmMeasure[1]);

            if(empty($bpmRange['low'])) $bpmRange['low'] = $bpmRange['high'] = $bpmValue;

            //XXX: Anything bigger or smaller than this cannot be stored by MySQLs bigint type http://dev.mysql.com/doc/refman/5.5/en/integer-types.html
            //fuck you if your simfile has bpms this high/low what's wrong with you.
            if($bpmValue <= 9223372036854775807 && $bpmValue >= -9223372036854775808)
            {
                $bpmRange['high'] = ($bpmValue > $bpmRange['high']) ? $bpmValue : $bpmRange['high'];
                $bpmRange['low'] = ($bpmValue < $bpmRange['low']) ? $bpmValue : $bpmRange['low'];
            }
        }
        
        return array($bpmRange['low'], $bpmRange['high']);
    }
}
