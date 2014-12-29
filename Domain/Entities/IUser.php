<?php

namespace Domain\Entities;

use Domain\VOs\ICountry;

interface IUser
{
    public function setId($id);
    public function getId();
    public function getName();
    public function getTags();
    public function getCountry();
    public function getDisplayName();
    public function getYearsStepArtist();
    public function getFacebookId();
    public function setFacebookId($id);
    public function getQuota();
    
    public function setDisplayName($displayName);
    public function setCountry(ICountry $country);
}
