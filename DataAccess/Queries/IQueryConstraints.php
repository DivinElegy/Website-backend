<?php

namespace DataAccess\Queries;

use DataAccess\Queries\IQueryBuilder;

interface IQueryConstraints
{
    public function applyTo(IQueryBuilder $queryBuilder);
}