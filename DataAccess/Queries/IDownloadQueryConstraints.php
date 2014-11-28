<?php

namespace DataAccess\Queries;

use DataAccess\Queries\IQueryConstraints;
use DateTimeInterface;

interface IDownloadQueryConstraints extends IQueryConstraints
{
    public function inDateRange(DateTimeInterface $start, DateTimeInterface $end);
}