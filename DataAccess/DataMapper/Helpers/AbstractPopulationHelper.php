<?php

namespace DataAccess\DataMapper\Helpers;

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
                    if(
                        !empty($row[$mapsHelper->getColumnName()]) &&
                        (string)(int)$row[$mapsHelper->getColumnName()] != $row[$mapsHelper->getColumnName()] &&
                        (string)(int)$row[$mapsHelper->getColumnName()] != PHP_INT_MAX //FFFFFFFFFFF
                    ) {
                        throw new Exception('Expected numeric value.');
                    }
                    $constructors[$constructor] = (int)$row[$mapsHelper->getColumnName()];
                    break;
                case 'DataAccess\DataMapper\Helpers\VarcharMapsHelper':
                    $constructors[$constructor] = $row[$mapsHelper->getColumnName()];
                    break;
                case 'DataAccess\DataMapper\Helpers\VOMapsHelper':
                case 'DataAccess\DataMapper\Helpers\VOArrayMapsHelper':
                case 'DataAccess\DataMapper\Helpers\EntityMapsHelper':
                case 'DataAccess\DataMapper\Helpers\EntityArrayMapsHelper':
                    $constructors[$constructor] = $mapsHelper->populate($maps, $db, $entity, $row);
                    break;
            }
        }
        return $constructors;
    }
    
    static function generateUpdateSaveQuery($maps, $entity, $id, $db, &$queries = array(), $extraColumns = array(), $mapsIndex = null)
    {
        $entityMapsIndex = isset($mapsIndex) ? $mapsIndex : self::getMapsNameFromEntityObject($entity, $maps);

        if($id)
        {
            $query = sprintf('update %s set ', $maps[$entityMapsIndex]['table']);    
        } else {
            $queryColumnNamesAndValues = array();
        }
        
        foreach($maps[$entityMapsIndex]['maps'] as $mapsHelper)
        {
            $accessor = $mapsHelper->getAccessor();
            $property = isset($entity) ? $entity->{$accessor}() : null;

            //sometimes children objects will be null, e.g., the banner for a simfile
            //just skip them.
            //Tricky: only skip when we're making a new entity. In the case of
            //existing ones we need to cater for null objects. For example a user
            //might change their country to null.
            if((!is_null($property) || (is_null($property) && $id))) 
            {
                switch(get_class($mapsHelper))
                {
                    case 'DataAccess\DataMapper\Helpers\VOMapsHelper':
                        //we have a vo. Determine which way the reference is
                        //
                        //TODO: This is how I used to do this, but it failed if the property was NULL.
                        //I dunno if I will have to do a similar thing with entity references (next case block)
                        //but I'm leaving this here as a reminder if I ever have to come back to thiat.
                        //
                        //Notice I also added mapsIndex to the generateUpdateSaveQuery thing, that's important.
                        //When I call it in this case block you can see I added voMapsIndex as that argument.
                        //God I hope I can remember this stuff in the future.
                        //$voMapsIndex = self::getMapsNameFromEntityObject($property, $maps);
                        $voMapsIndex = $mapsHelper->getVOName();
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
                                $voTableId = self::findVOInDB($maps, $voMapsIndex, $property, $db);

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
                                        $queryColumnNamesAndValues[$columnName] = $db->quote($columnValue);
                                    }
                                }

                                break;
                            case self::REFERENCE_BACK:
                                $voId = self::findVOInDB($maps,
                                    $voMapsIndex,
                                    $property,
                                    $db,
                                    array(strtolower($entityMapsIndex . '_id') => $id));
                                if($voId)
                                {
                                    self::generateUpdateSaveQuery($maps, $property, $voId, $db, $queries, null, $voMapsIndex);   
                                } else {
                                    $extra = array(strtolower($entityMapsIndex . '_id') => '%MAIN_QUERY_ID%');
                                    self::generateUpdateSaveQuery($maps, $property, NULL, $db, $queries, $extra, $voMapsIndex);
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
                                            //strtolower($mapsHelper->getEntityName() . '_id'),
                                            strtolower($mapsHelper->getTableName() . '_id'),
                                            $property->getId());
                                    } else {
                                        //not in db yet. make new ref
                                        //$queryColumnNamesAndValues[strtolower($mapsHelper->getEntityName() . '_id')] = $property->getId();
                                        $queryColumnNamesAndValues[strtolower($mapsHelper->getTableName() . '_id')] = $property->getId();
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
                            $queryColumnNamesAndValues[$mapsHelper->getColumnName()] = $property;
                        }
                        break;
                    case 'DataAccess\DataMapper\Helpers\VarcharMapsHelper':
                        //XXX: pls magically fix all my character encoding issues.
                        $property = isset($property) ? mb_convert_encoding($property, "UTF-8", mb_detect_encoding($property, "UTF-8, ISO-8859-1, ISO-8859-15", true)) : NULL;
                        if($id){
                            //easy case, plain values in our table.
                            if(isset($property))
                            {
                                $query .= sprintf('%s="%s", ',
                                    $mapsHelper->getColumnName(),
                                    $property);     
                            } else {
                                $query .= sprintf('%s=NULL, ',
                                    $mapsHelper->getColumnName());                                     
                            }
                        } else {
                            $queryColumnNamesAndValues[$mapsHelper->getColumnName()] = $db->quote($property);
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
                    case 'DataAccess\DataMapper\Helpers\EntityArrayMapsHelper':
                        if($id && isset($property[0]))
                        {
                            // If we assume that all elements in the array are the same then
                            // we can just use the first one to figure out which maps entry to use

                            $subEntityMapsIndex = self::getMapsNameFromEntityObject($property[0], $maps);
                            //TODO: I think this function will work with Entities too, but I should probably rename it at some point
                            $voIds = self::mapVOArrayToIds($maps[$subEntityMapsIndex]['table'],
                                array(strtolower($entityMapsIndex . '_id'), $id),
                                $db);

                            foreach($property as $index => $propertyArrayElement)
                            {
                                //XXX: I wanted this to only run on VOs, not entities. But there's a problem with that.
                                //when creating a pack, the simfile entities need to reference the pack, and the only way for
                                //that to happen is here. What I do instead is check that the entity has an id (which implies
                                //it has already been created and save) if it is a IDivineEntity. If it doesn't, complain.
                                //this ensures consistent behaviour with other parts of this mapper.
                                if($property instanceof \Domain\Entities\IDivineEntity && !$property->getId())
                                {
                                    throw new Exception(sprintf(
                                        'Could not find referenced entity, %s, in the database. Has it been saved yet?',
                                         $mapsHelper->getEntityName()));
                                }
                                
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
                                //XXX: I wanted this to only run on VOs, not entities. But there's a problem with that.
                                //when creating a pack, the simfile entities need to reference the pack, and the only way for
                                //that to happen is here. What I do instead is check that the entity has an id (which implies
                                //it has already been created and save) if it is a IDivineEntity. If it doesn't, complain.
                                //this ensures consistent behaviour with other parts of this mapper.
                                if($property instanceof \Domain\Entities\IDivineEntity && !$property->getId())
                                {
                                    throw new Exception(sprintf(
                                        'Could not find referenced entity, %s, in the database. Has it been saved yet?',
                                         $mapsHelper->getEntityName()));
                                }
                                
                                // TODO: TRICKY! Since this is a back-reference, it
                                // needs the ID of the object we're trying to save
                                // to complete
                                $extra = array(strtolower($entityMapsIndex . '_id') => '%MAIN_QUERY_ID%');
                                self::generateUpdateSaveQuery($maps, $propertyArrayElement, NULL, $db, $queries, $extra);
                            }
                        }
                }
            }
        }
        
        if($id)
        {
            $queryColumnNamesAndValues = @$queryColumnNamesAndValues ?: array();
            $queries[] = array('id' => $id, 'prepared' => $query, 'table' => $maps[$entityMapsIndex]['table'], 'columns' => $queryColumnNamesAndValues);
        } else {
            $queryColumnNamesAndValues = array_merge($queryColumnNamesAndValues, $extraColumns);
            $queries[] = array('table' => $maps[$entityMapsIndex]['table'], 'columns' => $queryColumnNamesAndValues);
        }
            
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
        $dbName = $db->query('select database()')->fetchColumn();
        if($tableA === $tableB)
        {
            return self::REFERENCE_SELF;
        }
        
        // first look in table A for a reference to B
        $statement = $db->prepare(sprintf(
            'SELECT `COLUMN_NAME` FROM `INFORMATION_SCHEMA`.`COLUMNS` WHERE `TABLE_SCHEMA`="%s" AND `TABLE_NAME`="%s"',
            $dbName,
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
            'SELECT `COLUMN_NAME` FROM `INFORMATION_SCHEMA`.`COLUMNS` WHERE `TABLE_SCHEMA`="%s" AND `TABLE_NAME`="%s"',
            $dbName,
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
    static public function findVOInDB($maps, $mapsIndex, $VO, $db, $extraColumns = array())
    {
        //$mapsIndex = self::getMapsNameFromEntityObject($VO, $maps);
        $table = $maps[$mapsIndex]['table'];

        //TODO: This may break everythign, but I _think_ if I pass extraColuns, it is always an id column.
        //I also think that this method is only called when we are trying to work out a VO (NOT a VOArray)
        //in which case there should be one unique row somewhere in the database that corresponds to the VO
        //we want, in that case we can just find it by id. Throwing in more columns causes issues trying to
        //update an existing VO because it uses the values of the current object, which we are trying to save,
        //so it never finds the existing row and therefore makes a whole new one, which ruins everything.
        if($extraColumns)
        {
            $columns = $extraColumns;
        } else {
            //I only had this before, I think I don't actually need to do this merge but I'm leaving it incase
            $columns = array_merge(self::resolveColumnNamesAndValues($maps, $VO), $extraColumns);
        }

        $query = "SELECT * FROM $table where ";
        
        foreach($columns as $columnName => $columnValue)
        {
            $columnValue = $db->quote($columnValue);
            $query .= sprintf('%s=%s AND ', $columnName, str_replace('"', '\"', $columnValue));
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

