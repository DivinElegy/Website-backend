<?php

namespace Domain\Entities;

use Domain\Entities\IUser;
use Domain\Entities\AbstractEntity;
use Domain\VOs\ICountry;
use Domain\VOs\IName;

class User extends AbstractEntity implements IUser
{
    private $_id;
    private $_country;
    private $_displayName;
    private $_name;
    private $_tags;
    private $_yearsStepArtist;
    private $_facebookId;
    private $_authToken;
    
    public function __construct(
        ICountry $country,
        $displayName,
        IName $name,
        array $tags,
        $facebookId,
        $authToken = null
    ) {
        $this->_country = $country;
        $this->_displayName = $displayName;
        $this->_name = $name;
        $this->_tags = $tags;
        $this->_facebookId = $facebookId;
        $this->_authToken = $authToken;
    }
        
    public function getCountry() {
        return $this->_country;
    }
    
    public function getDisplayName() {
        return $this->_displayName;
    }
    
    public function getName() {
        return $this->_name;
    }
    
    public function getTags() {
        return $this->_tags;
    }
    
    public function getFacebookId()
    {
        return $this->_facebookId;
    }
    
    public function setFacebookId($id)
    {
        $this->_facebookId = $id;
    }
    
    public function getAuthToken()
    {
        return $this->_authToken;
    }
    
    public function setAuthToken($token)
    {
        $this->_authToken = $token;
    }
    
    public function getYearsStepArtist() {
        return $this->_yearsStepArtist;
    }
}
