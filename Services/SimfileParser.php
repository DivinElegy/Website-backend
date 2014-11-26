<?php

namespace Services;

use Services\ISimfileParser;
use Exception;

class SimfileParser implements ISimfileParser
{
    
//        'light' => 'Novice',
//        'beginner' => 'Novice',
//        'easy' => 'Easy',
//        'medium' => 'Medium',
//        'hard' => 'Hard',
//        'challenge' => 'Expert',
//        'edit' => 'Edit'
    
    private $_smFileLines;
        
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
        if(!$title) throw new Exception ('Invalid SM file. TITLE missing');
        
        return $title;
    }
    
    public function artist()
    {
        $artist = $this->extractKey('ARTIST');
        if(!$artist) throw new Exception ('Invalid SM file. ARTIST missing');
        
        return new \Domain\VOs\StepMania\Artist($artist);
    }
    
    public function stops()
    {
        $stops = $this->extractKey('STOPS');
        if($stops === false) throw new Exception ('Invalid SM file. STOPS missing');
        
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
            $bpmRange[1] = @$bpmRange[1] ?: $bpmRange[0];
        } else {
            $bpms = $this->extractKey('BPMS');
            $bpmRange = $this->parseBpms($bpms);
        }
        
        //I have nfi why I made the BPM VO high-low instead of low-high in the constructor but yolo
        return new \Domain\VOs\StepMania\BPM($bpmRange[1], $bpmRange[0]);
    }
    
    public function bgChanges()
    {
        $bgChanges = $this->extractKey('BGCHANGES');
        if($bgChanges === false) throw new Exception ('Invalid SM file. BGCHANGES missing');
        
        return (bool)$bgChanges;
    }
    
    public function bpmChanges() 
    {
        $bpms = $this->extractKey('BPMS');
        if(!$bpms) throw new Exception ('Invalid SM file. BPMS missing');
        
        $bpmRange = $this->parseBpms($bpms);
        //XXX: We have bpm changes when the high and low bpms are different.
        return $bpmRange[0] != $bpmRange[1];
    }
    
    public function subtitle()
    {
        $subtitle = $this->extractKey('SUBTITLE');
        if(!$subtitle) throw new Exception ('Invalid SM file. SUBTITLE missing');
        
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
                $allSteps[] = $this->stepchartFromNoteData($noteData);
            }
        }
        
        if(empty($allSteps)) throw new Exception('Invalid Sm file. NOTES missing');
        return $allSteps;
    }
    
    private function stepchartFromNoteData($noteData)
    {
        $stepData = array_map('trim', explode(':', $noteData));
        return new \Domain\VOs\StepMania\StepChart(
            new \Domain\VOs\StepMania\DanceMode($stepData[0]),
            new \Domain\VOs\StepMania\Difficulty($stepData[2]),
            empty($stepData[1]) ? null : new \Domain\VOs\StepMania\StepArtist($stepData[1]),
            $stepData[3]
        );
    }
    
    private function extractKey($key)
    {
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

                $bpmRange['high'] = ($bpmValue > $bpmRange['high']) ? $bpmValue : $bpmRange['high'];
                $bpmRange['low'] = ($bpmValue < $bpmRange['low']) ? $bpmValue : $bpmRange['low'];
        }
        
        return array($bpmRange['low'], $bpmRange['high']);
    }
}
