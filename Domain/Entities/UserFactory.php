<?php

namespace Domain\Entities;

use Domain\VOs\ICountry;
use Domain\VOs\IName;
use Domain\Entities\User;

interface IUserFactory
{
    public function createInstance(
        ICountry $country,
        $displayName,
        IName $name,
        array $tags,
        $facebookId,
        $quota
    );
}

class UserFactory implements IUserFactory
{
    public function createInstance(
        ICountry $country = null,
        $displayName,
        IName $name,
        array $tags,
        $facebookId,
        $quota
    ) {
        return new User(
            $country,
            $displayName,
            $name,
            $tags,
            $facebookId,
            $quota
        );
    }
}