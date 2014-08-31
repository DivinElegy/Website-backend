<?php

namespace Domain\VOs;

use Domain\VOs\IName;

class Name implements IName
{
    private $_firstName;
    private $_lastName;
    
    public function __construct($firstName, $lastName = null)
    {
        $this->_firstName = $firstName;
        $this->_lastName = $lastName;
    }
    
    public function getFirstName() {
        return $this->_firstName;
    }
    
    public function getLastName() {
        return $this->_lastName;
    }
    
    public function getFullName() {
        return sprintf('%s %s', $this->_firstName, $this->_lastName);
    }
}