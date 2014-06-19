<?php
namespace DivinElegy\BLL;

class Simfile
{
    //str - simfile title
    protected $title;
    
    //obj - reerence to song artist
    protected $artist;
    
    //obj - reference to uploader
    protected $uploader;
    
    //obj - reference to bpm object
    protected $bpm;
    
    //bool - does the chart have stops
    protected $stops;
    
    //bool - does the chart have fgChanges
    protected $fgChanges;
    
    //bool - does the charg have bgChanges
    protected $bgChanges;
    
    public function __construct($title)
    {
        $this->setTitle($title);
    }
    
    protected function setTitle($title) 
    {
        if (empty($title))
        {
            throw new \InvalidArgumentException('Simfile title cannot be empty');
        }
        
        $this->title = $title;
    }
    
    public function setArtist(Artist $artist)
    {
        $this->artist = $artist;
    }
}
