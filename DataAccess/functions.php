<?php

namespace DataAccess;

use DataAccess\DataMapper\Helpers\EntityMapsHelper;
use DataAccess\DataMapper\Helpers\VOMapsHelper;
use DataAccess\DataMapper\Helpers\VOArrayMapsHelper;
use DataAccess\DataMapper\Helpers\IntMapsHelper;
use DataAccess\DataMapper\Helpers\VarcharMapsHelper; 

function Entity($mapName, $accessor=null, $tableName = null)
{
    return new EntityMapsHelper($mapName, $accessor, $tableName);
}

function VO($mapName, $accessor=null, $tableName = null)
{
    return new VOMapsHelper($mapName, $accessor, $tableName);
}

function VOArray($mapName, $accessor=null, $tableName = null)
{
    return new VOArrayMapsHelper($mapName, $accessor, $tableName);
}

function Varchar($mapName, $accessor=null, $tableName = null)
{
    return new VarcharMapsHelper($mapName, $accessor, $tableName);
}

function Int($mapName, $accessor=null, $tableName = null)
{
    return new IntMapsHelper($mapName, $accessor, $tableName);
}

