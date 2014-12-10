<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace DataAccess\Queries;

interface IQueryBuilder
{
    public function setBaseQuery($query);
    public function limit($start, $end);
    public function where($column, $operator, $value);
    public function join($type, $tableA, $columnA, $tableB, $columnB);
    public function null($columnName);
    public function orderBy($column, $direction);
    public function count($column, $as = null);
    public function group($column);
    public function buildQuery();
}