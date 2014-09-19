<?php

namespace DataAccess\Queries;

use DataAccess\Queries\QueryBuilder;
use DataAccess\Queries\IQueryBuilderFactory;

class QueryBuilderFactory implements IQueryBuilderFactory
{
    public function createInstance()
    {
        return new QueryBuilder();
    }
}