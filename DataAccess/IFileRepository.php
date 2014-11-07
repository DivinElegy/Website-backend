<?php

namespace DataAccess;

use DataAccess\IRepository;

interface IFileRepository extends IRepository
{
    public function findByHash($hash);
}

    
