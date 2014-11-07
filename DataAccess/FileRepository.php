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
        $queryBuilder->where('id', '=', "$id");
        
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
                
        return reset($results);
    }
}
