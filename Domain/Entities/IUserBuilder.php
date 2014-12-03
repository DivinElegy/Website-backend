<?php

namespace Domain\Entities;

use Domain\VOs\ICountry;
use Domain\VOs\IName;

interface IUserBuilder
{
    public function With_Country(ICountry $country);
    public function With_DisplayName($name);
    public function With_Name(IName $name);
    public function With_Tags(array $tags);
    public function With_FacebookId($id);
    public function With_YearsStepArtist($years);
    public function With_Quota($quota);
    public function build();
}
