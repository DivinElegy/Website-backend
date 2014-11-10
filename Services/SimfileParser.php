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
        //XXX: Should I explode on ';' instead? That seems like it might be a more reliable delimiter
        $this->_smFileLines = explode(";", $simfileData);
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
        
        return $artist;
    }
    
    public function stops()
    {
        $stops = $this->extractKey('STOPS') ? 'Yes' : 'No';
        if(!$stops) throw new Exception ('Invalid SM file. STOPS missing');
        
        return $stops;
    }
    
    public function fgChanges()
    {
        $fgChanges = $this->extractKey('FGCHANGES') ? 'Yes' : 'No';
        if(!$fgChanges) throw new Exception ('Invalid SM file. FGCHANGES missing');
        
        return $fgChanges;
    }
    
    public function bgChanges()
    {
        $bgChanges = $this->extractKey('BGCHANGES') ? 'Yes' : 'No';
        if(!$bgChanges) throw new Exception ('Invalid SM file. BGCHANGES missing');
        
        return $bgChanges;
    }
    
    public function bpmChanges() 
    {
        $bmpChanges = $this->extractKey('BPMS') ? 'Yes' : 'No';
        if(!$bpmChanges) throw new Exception ('Invalid SM file. BPMS missing');
        
        return $bpmChanges;
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
                $allSteps = array_merge($allSteps, $this->stepsArrayFromNoteData($noteData));
            }
        }

        if(empty($allSteps)) throw new Exception('Invalid Sm file. NOTES missing');
        return $allSteps;
    }

    private function stepsArrayFromNoteData($noteData)
    {
        $stepData = array_map('trim', explode(':', $noteData));
        $steps = array();
        $mode = $stepData[0] == 'dance-single' ? 'single' : null;
        $steps[$mode][] = array(
            'artist' => $stepData[1],
            'difficulty' => $stepData[2],
            'rating' => $stepData[3]
        );
        
        return $steps;
    }
    
    private function extractKey($key)
    {
        if(empty($this->_smFileLines)) throw new Exception('SM file data not set.');
        
        foreach ($this->_smFileLines as $line)
        {
            $pos = strpos($line, '#' . $key . ':');
            if ($pos !== false) return trim(substr($line, $pos + strlen($key) + 2));
        }
    }
}
