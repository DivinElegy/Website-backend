<?php

namespace DataAccess;

use DataAccess\IRepository;
use Domain\Entities\IFile;

interface IFileRepository extends IRepository
{
    public function findByHash($hash);
    public function save(IFile $file);
}

    
