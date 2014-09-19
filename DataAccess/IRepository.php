<?php

namespace DataAccess;

use Domain\Entities\IDivineEntity;

interface IRepository
{
    public function findById($id);
    public function findRange($id, $limit);
}