<?php

namespace DataAccess;

use DataAccess\IFileRepository;
use DataAccess\DataMapper\IDataMapper;
use DataAccess\Queries\IQueryBuilderFactory;

class FileRepository implements IFileRepository
{
    private $_dataMapper;
    private $_queryBuilderFactory;
    
    public function __construct(IDataMapper $dataMapper, IQueryBuilderFactory $queryBuilderFactory) {
        $this->_dataMapper = $dataMapper;
        $this->_queryBuilderFactory = $queryBuilderFactory;
    }
    
    public function findById($id) {
        $queryBuilder = $this->_queryBuilderFactory->createInstance();
        $queryBuilder->where('id', '=', $id);
        
        $results = $this->_dataMapper->map(
            'File',
            $queryBuilder
        );
        
        return reset($results);
    }
    
    public function findAll()
    {
        $queryBuilder = $this->_queryBuilderFactory->createInstance();
        $queryBuilder->where('id', '>', 0);
        
        return $this->_dataMapper->map(
            'File',
            $queryBuilder
        );
    }
    
    public function findRange($id, $limit)
    {
        return $this->_dataMapper->findRange(
            'User',
            'SELECT * FROM %s WHERE id>=' . $id . ' LIMIT ' . $limit
        );
    }
    
    public function findByHash($hash)
    {
        $queryBuilder = $this->_queryBuilderFactory->createInstance();
        $queryBuilder->where('hash', '=', "$hash");
        
        $results = $this->_dataMapper->map(
                    'File',
                    $queryBuilder
                   );
        
        //XXX: Hack. Sometimes instead of getting a real array back we get the
        //lazyload thing because I was an idiot with the database. Originally
        //I simply did return reset($results) but if we don't have an array that
        //won't work. So instead do a foreach (lazyload thing is iterable) and just
        //return the first element.
        //
        //XXX: Disregard, I fixed the DB at home so that wasn't an issue and never
        //put it up on the live server. Idiot.
        return reset($results);
    }
    
    public function save(\Domain\Entities\IFile $file) {
        return $this->_dataMapper->save($file);
    }
}
