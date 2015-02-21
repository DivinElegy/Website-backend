<?php

namespace Services;

use Services\ICacheUpdater;
use Domain\Entities\StepMania\IPack;
use DataAccess\StepMania\IPackRepository;
use Domain\Util;

class CacheUpdater implements ICacheUpdater
{
    private $_json;
    private $_packRepository;
    
    public function __construct(IPackRepository $packRepository) {
        $this->_json = json_decode(file_get_contents('../SimfileCache/simfiles.json'), true);
        $this->_packRepository = $packRepository;
    }
    
    public function insert(IPack $pack)
    {
        //XXX: Tricky, when the pack comes in it may not be fully populated
        //we are interested in keeping the cache in sync with the DB, so
        //reload it from the DB to ensure we have everything.
        $pack = $this->_packRepository->findById($pack->getId());
        $this->_json['packs'][] = Util::packToArray($pack);
        return $this;
    }
    
    public function update()
    {
        usort($this->_json['packs'], function($a, $b)
        {
            return strcasecmp($a['title'], $b['title']);
        });
            
        file_put_contents('../SimfileCache/simfiles.json',json_encode($this->_json));
    }
}

