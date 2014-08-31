<?php

namespace Domain\Entities;

use Domain\Entities\IDivineEntity;

abstract class AbstractEntity implements IDivineEntity
{
    protected $id;
    
    public function setId($id) {
        if(isset($this->id)) {
        //    throw new Exception('ID already set.');
        }
        
        $this->id = $id;
    }
    
    public function getId() {
        return $this->id;
    }
}
