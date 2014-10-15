<?php

namespace Domain\Entities;

use Domain\VOs\ICountry;
use Domain\VOs\IName;
use Domain\Entities\IUserFactory;
use Domain\Entities\IUserBuilder;

class UserBuilder implements IUserBuilder
{
    private $_userFactory;
    private $_country;
    private $_displayName;
    private $_name;
    private $_tags;
    private $_facebookId;
    private $_yearsStepArtist;
    
    public function __construct(IUserFactory $userFactory)
    {
        $this->_userFactory = $userFactory;
    }
    
    public function With_Country(ICountry $country) {
        $this->_country = $country;
        return $this;
    }
    
    public function With_DisplayName($name) {
        $this->_displayName = $name;
        return $this;
    }
    
    public function With_Name(IName $name) {
        $this->_name = $name;
        return $this;
    }
    
    public function With_Tags(array $tags) {
        $this->_tags = $tags;
        return $this;
    }
    
    public function With_FacebookId($id) {
        $this->_facebookId = $id;
        return $this;
    }
    
    public function With_YearsStepArtist($years) {
        $this->_yearsStepArtist = $years;
        return $this;
    }
    
    public function build() {
        return $this->_userFactory
                    ->createInstance($this->_country,
                                     $this->_displayName,
                                     $this->_name,
                                     $this->_tags,
                                     $this->_facebookId);
    }
}