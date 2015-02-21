<?php

namespace Services;

use Services\ICacheUpdater;
use Domain\Entities\StepMania\IPack;
use Domain\Util;

class CacheUpdater implements ICacheUpdater
{
    private $_json;
    
    public function __construct() {
        $this->_json = json_decode(file_get_contents('../SimfileCache/simfiles.json'), true);
    }
    
    public function insert(IPack $pack)
    {
        $this->_json['packs'][] = Util::packToArray($pack);
        return $this;
    }
    
    public function update()
    {
        usort($this->_json['packs'], function($a, $b)
        {
            return strcmp($a['title'], $b['title']);
        });
            
        file_put_contents('../SimfileCache/simfiles.json',json_encode($this->_json));
    }
}

