<?php

namespace DataAccess;

use DataAccess\IRepository;
use DataAccess\Queries\IDownloadQueryConstraints;
use Domain\Entities\IDownload;

interface IDownloadRepository extends IRepository
{
    public function findByUserId($id, IDownloadQueryConstraints $constraints = null);
    public function findByFileId($id, IDownloadQueryConstraints $constraints = null);
    public function findPopular();
    public function save(IDownload $file);
}

    
