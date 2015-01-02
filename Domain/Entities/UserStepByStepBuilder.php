<?php

namespace Domain\Entities;

use Domain\VOs\ICountry;
use Domain\VOs\IName;

interface IUserStepByStepBuilder
{
    public function With_DisplayName($name);
}

interface IUserStepByStepBuilder_With_DisplayName
{
    public function With_Name(IName $name);
}

interface IUserStepByStepBuilder_With_Name
{
    public function With_Tags(array $tags);
}

interface IUserStepByStepBuilder_With_Tags
{
    public function With_FacebookId($id);
}

interface IUserStepByStepBuilder_With_FacebookId
{
    public function With_Quota($quota);
}

interface IUserStepByStepBuilder_With_Quota
{
    public function With_YearsStepArtist($years); //not going to make this mandatory as it is kind of a joke
    public function With_Country(ICountry $country = null);
    public function build();
}
    
abstract class AbstractUserStepByStepBuilder
{
    protected $_userBuilder;
    
    public function __construct(IUserBuilder $builder)
    {
        $this->_userBuilder = $builder;
    }
}

class UserStepByStepBuilder extends AbstractUserStepByStepBuilder implements IUserStepByStepBuilder
{
    public function With_DisplayName($name) {
        $this->_userBuilder->With_DisplayName($name);
        return new UserStepByStepBuilder_With_DisplayName($this->_userBuilder);
    }
}

class UserStepByStepBuilder_With_DisplayName extends AbstractUserStepByStepBuilder implements IUserStepByStepBuilder_With_DisplayName
{
    public function With_Name(IName $name) {
        $this->_userBuilder->With_Name($name);
        return new UserStepByStepBuilder_With_Name($this->_userBuilder);
    }
}

class UserStepByStepBuilder_With_Name extends AbstractUserStepByStepBuilder implements IUserStepByStepBuilder_With_Name
{
    public function With_Tags(array $tags) {
        $this->_userBuilder->With_Tags($tags);
        return new UserStepByStepBuilder_With_Tags($this->_userBuilder);
    }
}

class UserStepByStepBuilder_With_Tags extends AbstractUserStepByStepBuilder implements IUserStepByStepBuilder_With_Tags
{
    public function With_FacebookId($id) {
        $this->_userBuilder->With_FacebookId($id);
        return new UserStepByStepBuilder_With_FacebookId($this->_userBuilder);
    }
}

class UserStepByStepBuilder_With_FacebookId extends AbstractUserStepByStepBuilder implements IUserStepByStepBuilder_With_FacebookId
{
    public function With_Quota($quota)
    {
        $this->_userBuilder->With_Quota($quota);
        return new UserStepByStepBuilder_With_Quota($this->_userBuilder);
    }
}

class UserStepByStepBuilder_With_Quota extends AbstractUserStepByStepBuilder implements IUserStepByStepBuilder_With_Quota
{
    public function With_YearsStepArtist($years) {
        $this->_userBuilder->With_YearsStepArtist($years);
        return $this;
    }
    
    public function With_Country(ICountry $country = null) {
        $this->_userBuilder->With_Country($country);
        return $this;
    }

    public function build() {
        return $this->_userBuilder
                    ->build();
    }
}