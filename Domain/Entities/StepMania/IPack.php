<?php

namespace Domain\Entities;

use Domain\Entities\IDivineEntity;

interface IPack extends IDivineEntity
{
    public function getTitle();
    public function getUploader();
    public function getContributors();
    public function getSimfiles();
    public function getFile();
}
