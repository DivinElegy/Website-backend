<?php

namespace DataAccess\Queries;

use DataAccess\Queries\IQueryConstraints;
use DateTime;

interface IDownloadQueryConstraints extends IQueryConstraints
{
    public function inDateRange(DateTime $start, DateTime $end);
}