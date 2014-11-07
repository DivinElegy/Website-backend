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
        
    public function __construct($simfileData)
    {
        $this->_smFileLines = explode("\n", $simfileData);
    }
    
    public function title()
    {
        $title = $this->extractKey('TITLE');
        if(!$title) throw new Exception ('Invalid SM file. Title missing');
        
        return $title;
    }
    
    public function stops()
    {
        return $this->extractKey('STOPS') ? 'Yes' : 'No';
    }
    
    public function steps($mode, $difficulty)
    {
        $steps = array();
        
        foreach ($this->_smFileLines as $index => $line) {
            if (strpos($line, '#NOTES') !== false) {
                $mode = substr(trim($lines[$index + 1]), 0, -1) == 'dance-single' ? 'single' : null;
                $notes[$mode][] = array(
                    'artist' => substr(trim($lines[$index + 2]), 0, -1),
                    'difficulty' => substr(trim($lines[$index + 3]), 0, -1),
                    'rating' => substr(trim($lines[$index + 4]), 0, -1)
                );
            }
        }

        return $steps;
    }
    
    private function extractKey($key) {
        foreach ($lines as $line) {
            $pos = strpos($line, '#' . $key . ':');
            if($pos !== false) return substr(trim($line), strlen($key) + 2, -1);
        }
    }

}
