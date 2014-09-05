<?php

namespace DataAccess\DataMapper\Helpers;

use DataAccess\DataMapper\Helpers\VOMapsHelper;
use Domain\Entities\IDivineEntity;
use Exception;

class AbstractPopulationHelper
{
    
    const REFERENCE_FORWARD = 1;
    const REFERENCE_BACK = 2;
    const REFERENCE_SELF = 3;
    const QUERY_TYPE_UPDATE = 'update';
    const QUERY_TYPE_CREATE = 'create';
    
    static function getConstrutorArray($maps, $entity, $row, $db)
    {
        $constructors = array();
        
        foreach($maps[$entity]['maps'] as $constructor => $mapsHelper)
        {
            switch(get_class($mapsHelper))
            {
                case 'DataAccess\DataMapper\Helpers\IntMapsHelper':
                case 'DataAccess\DataMapper\Helpers\VarcharMapsHelper':
                    $constructors[$constructor] = $row[$mapsHelper->getColumnName()];
                    break;
                case 'DataAccess\DataMapper\Helpers\VOMapsHelper':
                case 'DataAccess\DataMapper\Helpers\VOArrayMapsHelper':
                case 'DataAccess\DataMapper\Helpers\EntityMapsHelper':
                    $constructors[$constructor] = $mapsHelper->populate($maps, $db, $entity, $row);
                    break;
            }
        }
        
        return $constructors;
    }
    
    static function generateUpdateSaveQuery($maps, $entity, $id, $db, &$queries = array(), $extraColumns = array())
    {
        $entityMapsIndex = self::getMapsNameFromEntityObject($entity, $maps);
        
        if($id)
        {
            $query = sprintf('update %s set ', $maps[$entityMapsIndex]['table']);    
        } else {
            $queryColumnNamesAndValues = array();
        }
        
        foreach($maps[$entityMapsIndex]['maps'] as $mapsHelper)
        {
            $accessor = $mapsHelper->getAccessor();
            $property = $entity->{$accessor}();

            switch(get_class($mapsHelper))
            {
                case 'DataAccess\DataMapper\Helpers\VOMapsHelper':
                    //we have a vo. Determine which way the reference is
                    $voMapsIndex = self::getMapsNameFromEntityObject($property, $maps);
                    $refDir = self::getReferenceDirection(
                        $maps[$entityMapsIndex]['table'],
                        $maps[$voMapsIndex]['table'],
                        $entityMapsIndex,
                        $mapsHelper->getTableName(),
                        $db);
                    
                    switch($refDir)
                    {
                        // our table stores their ID, all we do is update
                        // our reference.
                        case self::REFERENCE_FORWARD:
                            $voTableId = self::findVOInDB($maps, $property, $db);
                            
                            if($id)
                            {
                                $query .= sprintf('%s=%u, ',
                                    strtolower($mapsHelper->getTableName() . '_id'),
                                    $voTableId);
                            } else {
                                // we have a forward reference to a value object.
                                // see if it exists first:                                
                                if($voTableId)
                                {
                                    $queryColumnNamesAndValues[strtolower($mapsHelper->getTableName() . '_id')] = $voTableId;
                                } else {
                                    //make a note that this field will need the id from another
                                    self::generateUpdateSaveQuery($maps, $property, NULL, $db, $queries);
                                    $queryColumnNamesAndValues[strtolower($mapsHelper->getTableName() . '_id')] = '%INDEX_REF_' . (count($queries)-1) . '%';
                                }
                            }

                            break;
                        case self::REFERENCE_SELF:
                            //no need to find ids, but we need the
                            //column names
                            $columns = self::resolveColumnNamesAndValues($maps, $property);
                            foreach($columns as $columnName=>$columnValue)
                            {
                                if($id)
                                {
                                    //TODO: logic to detemine what the value is? i.e., string, int etc?
                                    $query .= sprintf('%s="%s", ',
                                        $columnName,
                                        $columnValue
                                    );
                                } else {
                                    //TODO: logic to detemine what the value is? i.e., string, int etc?
                                    $queryColumnNamesAndValues[$columnName] = sprintf('"%s"', $columnValue);
                                }
                            }

                            break;
                        case self::REFERENCE_BACK:
                            echo 'bleh';

                            $voId = self::findVOInDB($maps,
                                $property,
                                $db,
                                array(strtolower($entityMapsIndex . '_id') => $id));
                            if($voId)
                            {
                                self::generateUpdateSaveQuery($maps, $property, $voId, $db, $queries);   
                            } else {
                                $extra = array(strtolower($entityMapsIndex . '_id') => '%MAIN_QUERY_ID%');
                                self::generateUpdateSaveQuery($maps, $property, NULL, $db, $queries, $extra);
                            }
                            break;
                    }

                    break;

                // We should never update referenced entities, the db
                // should always store an ID as a reference to them.
                //
                // In the case where we cannot find the entity in the database,
                // throw an exception ?
                case 'DataAccess\DataMapper\Helpers\EntityMapsHelper':
                    $subEntityMapsIndex = self::getMapsNameFromEntityObject($property, $maps);
                    $refDir = self::getReferenceDirection(
                        $maps[$entityMapsIndex]['table'],
                        $maps[$subEntityMapsIndex]['table'],
                        $entityMapsIndex,
                        $mapsHelper->getTableName(),
                        $db);

                    switch($refDir)
                    {
                        // our table stores their ID, all we do is update
                        // our reference.
                        case self::REFERENCE_FORWARD:
                            if($property->getId())
                            {
                                // we exist in db already, update our reference
                                if($id)
                                {
                                    $query .= sprintf('%s=%u, ',
                                        strtolower($mapsHelper->getEntityName() . '_id'),
                                        $property->getId());
                                } else {
                                    //not in db yet. make new ref
                                    $queryColumnNamesAndValues[strtolower($mapsHelper->getEntityName() . '_id')] = $property->getId();
                                }
                            } else {
                                // The entity we care about references an entity that
                                // has not yet been saved.
                                //
                                // TODO: Should we _try_ to save it? Or should
                                // it be enforced that entites already exist in the db?
                                // In the case of something like referencing a user entity,
                                // then for sure the user should already be saved because
                                // it makes no sense to assign a user at the time of simfile
                                // upload, they should have already completed the process.
                                // but could there be other entities where it does make sense
                                // for them to be created at the time of something else ?
                                throw new Exception(sprintf(
                                    'Could not find referenced entity, %s, in the database. Has it been saved yet?',
                                     $mapsHelper->getEntityName()));
                            }
                            break;
                    }
                    break;
                case 'DataAccess\DataMapper\Helpers\IntMapsHelper':
                    if($id)
                    {
                        //easy case, plain values in our table.
                        $query .= sprintf('%s=%u, ',
                            $mapsHelper->getColumnName(),
                            $property);               
                    } else {
                        if(is_bool($property))
                        {
                            $property = ($property) ? '1' : '0';
                        }
                        $queryColumnNamesAndValues[$mapsHelper->getColumnName()] = sprintf('%u', $property);
                    }
                    break;
                case 'DataAccess\DataMapper\Helpers\VarcharMapsHelper':
                    if($id){
                        //easy case, plain values in our table.
                        $query .= sprintf('%s="%s", ',
                            $mapsHelper->getColumnName(),
                            $property);                        
                    } else {
                        $queryColumnNamesAndValues[$mapsHelper->getColumnName()] = sprintf('"%s"', $property);
                    }

                    break;

                // I am making a bit of an assumption here. In my mind it only
                // makes sense for an array of VOs to be stored in a different
                // table in the DB since the main row can't possibly store
                // different objects.
                //
                // in that regard, the way this works is that mapVOArrayToIds
                // simply queries the DB and returns the VO ids in order then
                // I assume that the object also has them in the same order
                // (which it will if it is pulled out by this mapper.
                //
                // in the case of setting up a new entity, the VOs should never
                // exist in the first place, so we just make them.
                case 'DataAccess\DataMapper\Helpers\VOArrayMapsHelper':
                    if($id)
                    {
                        // If we assume that all elements in the array are the same then
                        // we can just use the first one to figure out which maps entry to use
                        $subEntityMapsIndex = self::getMapsNameFromEntityObject($property[0], $maps);
                        $voIds = self::mapVOArrayToIds($maps[$subEntityMapsIndex]['table'],
                            array(strtolower($entityMapsIndex . '_id'), $id),
                            $db);
                        
                        foreach($property as $index => $propertyArrayElement)
                        {
                            $extra = array(strtolower($entityMapsIndex . '_id') => $id);
                            if(isset($voIds[$index]))
                            {
                                self::generateUpdateSaveQuery($maps, $propertyArrayElement, $voIds[$index], $db, $queries, $extra);
                            } else {
                                self::generateUpdateSaveQuery($maps, $propertyArrayElement, NULL, $db, $queries, $extra);
                            }
                        }

                        break;
                    } else {
                        foreach($property as $propertyArrayElement)
                        {
                            // TODO: TRICKY! Since this is a back-reference, it
                            // needs the ID of the object we're trying to save
                            // to complete
                            $extra = array(strtolower($entityMapsIndex . '_id') => '%MAIN_QUERY_ID%');
                            self::generateUpdateSaveQuery($maps, $propertyArrayElement, NULL, $db, $queries, $extra);
                        }
                    }
            }
        }
        
        if($id)
        {
            $query = substr($query, 0, -2);
            $query .= sprintf(' WHERE id=%u', $id);
            $queries['TYPE'] = self::QUERY_TYPE_UPDATE;
        } else {
            $queryColumnNamesAndValues = array_merge($queryColumnNamesAndValues, $extraColumns);
            $query = sprintf('INSERT INTO %s (%s) VALUES (%s)',
                $maps[$entityMapsIndex]['table'],
                implode(', ', array_keys($queryColumnNamesAndValues)),
                implode(', ', $queryColumnNamesAndValues));
            $queries['TYPE'] = self::QUERY_TYPE_CREATE;
        }
        
        $queries[] = $query;
        return $queries;
    }
    
    static private function getMapsNameFromEntityObject($entity, $maps)
    {
        //todo maybe check that $entity is vo or entity
        
        $classname = get_class($entity);
        foreach ($maps as $entityName => $map)
        {
            if($map['class'] == $classname)
            {
                return $entityName;
            }
        }
    }
    
    static private function getReferenceDirection($tableA, $tableB, $nameA, $nameB, $db)
    {
        //TODO: check if tables are the same and return a constant for that
        //echo '!!! ' . $tableA . ' needs ' . $nameB . ' : ' . $tableB . ' needs ' . $nameA . ' !!!<br />';
                
        if($tableA === $tableB)
        {
            return self::REFERENCE_SELF;
        }
        
        // first look in table A for a reference to B
        $statement = $db->prepare(sprintf(
            'SELECT `COLUMN_NAME` FROM `INFORMATION_SCHEMA`.`COLUMNS` WHERE `TABLE_SCHEMA`="divinelegy" AND `TABLE_NAME`="%s"',
            $tableA));

        $statement->execute();
        $rows = $statement->fetchAll();
        
        //print_r($rows);
        
        foreach($rows as $row)
        {
            if($row['COLUMN_NAME'] == strtolower($nameB . '_id'))
            {
                return self::REFERENCE_FORWARD;
            }
        }
        
        // now look in table b for a reference to a
        $statement = $db->prepare(sprintf(
            'SELECT `COLUMN_NAME` FROM `INFORMATION_SCHEMA`.`COLUMNS` WHERE `TABLE_SCHEMA`="divinelegy" AND `TABLE_NAME`="%s"',
            $tableB));

        $statement->execute();
        $rows = $statement->fetchAll();
        
        foreach($rows as $row)
        {
            if($row['COLUMN_NAME'] == strtolower($nameA . '_id'))
            {
                return self::REFERENCE_BACK;
            }
        }
    }
    
    // can use this when we reference a VO
    static public function findVOInDB($maps, $VO, $db, $extraColumns = array())
    {
        $mapsIndex = self::getMapsNameFromEntityObject($VO, $maps);
        $table = $maps[$mapsIndex]['table'];
        
        $columns = array_merge(self::resolveColumnNamesAndValues($maps, $VO), $extraColumns);
        
        $query = "SELECT * FROM $table where ";
        
        foreach($columns as $columnName => $columnValue)
        {
            $query .= sprintf('%s="%s" AND ', $columnName, $columnValue);
        }
        
        $query = substr($query, 0, -4);
        $statement = $db->prepare($query);
        $statement->execute();
        $row = $statement->fetch();
        
        return $row['id'];
    }
    
    // this will figure out what columns belong to an entity and
    // map the column names to the current entity values
    static public function resolveColumnNamesAndValues($maps, $entity, $originalTable = null, &$columnNamesAndValues = array())
    {
        $mapsIndex = self::getMapsNameFromEntityObject($entity, $maps);
        
        // This is the name of the table that the current object
        // we are looking at belongs to. We need to compare this
        //  to original table to decide what to do.
        $currentTable = $maps[$mapsIndex]['table'];
        
        if(!$originalTable)
        {
            // this will be the table that the VO we care about is stored in
            // we check all future values to make sure they belong to this table.
            // on the first pass we pull it out, and then on subsequent passes
            // it should come in through the function call.
            $originalTable = $currentTable;
        }

        foreach($maps[$mapsIndex]['maps'] as $mapsHelper)
        {
            switch(get_class($mapsHelper))
            {
                case 'DataAccess\DataMapper\Helpers\VOMapsHelper':
                    $accessor = $mapsHelper->getAccessor();
                    $VO = $entity->{$accessor}();
                    self::resolveColumnNamesAndValues($maps, $VO, $originalTable, $columnNamesAndValues);
                    break;
                case 'DataAccess\DataMapper\Helpers\VarcharMapsHelper':
                case 'DataAccess\DataMapper\Helpers\IntMapsHelper':
                    //is plain value.
                    
                    if($currentTable == $originalTable)
                    {
                        //It also keeps values in our table. Saving.
                        $accessor = $mapsHelper->getAccessor();
                        $value = $entity->{$accessor}();
                    
                        $columnNamesAndValues[$mapsHelper->getColumnName()] = $value;
                    } else {
                        //It does not store values in our table
                        //TODO: Should I try check if our table references the id of the record in the other table?
                    }
                    break;
            }
        }
        
        return $columnNamesAndValues;
    }
    
    // When we have VO arrays, it should be the case that there is another
    // table that references us. If we assume entries are in the db in the
    // same order they are in the array, it should be possible to map them up.
    // This way we can update existing VO entries instead of deleting and
    // making new ones. And if there are VOs where we can't get an ID, that
    // means we have to make a new one.
    //
    // Assumption: Array contains entries of all the same type
    static public function mapVOArrayToIds($voTable, $columnToMatch, $db)
    {
        $query = sprintf('SELECT id from %s WHERE %s=%u',
            $voTable,
            $columnToMatch[0],
            $columnToMatch[1]);
        
        $statement = $db->prepare($query);
        $statement->execute();
        $rows = $statement->fetchAll();
        
        $map = array();
        
        foreach($rows as $row)
        {
            $map[] = $row['id'];
        }
        
        return $map;
    }
}

//go off and resolve all column names for this table recursively
//do a check to make sure the VO we are investigating is in the same table
//   -if it is: good, record its column name and value
//   -if it isn't, we need to find this next VO in the db and record its ID as our column value 
//       -assuming a forward reference (IE our table has a column called voname_id.
//       -if it doesn't, then we don't have to worry about it

