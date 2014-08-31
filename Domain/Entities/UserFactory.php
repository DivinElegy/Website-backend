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
        $yearsStepArtist
    );
}

class UserFactory implements IUserFactory
{
    public function createInstance(
        ICountry $country,
        $displayName,
        IName $name,
        array $tags,
        $yearsStepArtist
    ) {
        return new User(
            $country,
            $displayName,
            $name,
            $tags,
            $yearsStepArtist
        );
    }
}