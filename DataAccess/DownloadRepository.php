<?php

namespace DataAccess;

use DataAccess\IDownloadRepository;
use DataAccess\DataMapper\IDataMapper;
use DataAccess\Queries\IQueryBuilderFactory;
use DataAccess\Queries\IQueryBuilder;
use DataAccess\Queries\IDownloadQueryConstraints;
use Domain\Entities\IDownload;

//TODO: Implement some sort of caching. Probably OK for now not to worry.
class DownloadRepository implements IDownloadRepository
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
             
        $result = $this->_dataMapper->map('Download', $queryBuilder);
        return reset($result);
    }
    
    public function findAll()
    {
        $queryBuilder = $this->_queryBuilderFactory->createInstance();
        $queryBuilder->where('id', '>', 0);
        $result = $this->_dataMapper->map('Download', $queryBuilder);
        return $result;
    }
    
    public function findRange($id, $limit)
    {
        $queryBuilder = $this->_queryBuilderFactory->createInstance();
        $queryBuilder->where('id', '>=', $id)->limit($limit);
                
        return $this->_dataMapper->map('Download', $queryBuilder);
    }
    
    public function save(IDownload $entity) {
        return $this->_dataMapper->save($entity);
    }
    
    public function findByUserId($id, IDownloadQueryConstraints $constraints = null)
    {
        $queryBuilder = $this->_queryBuilderFactory->createInstance();
        $queryBuilder->where('user_id', '=', $id);
        
        return $this->applyConstraintsAndReturn($constraints, $queryBuilder);
    }
    
    public function findByFileId($id, IDownloadQueryConstraints $constraints = null)
    {
        $queryBuilder = $this->_queryBuilderFactory->createInstance();
        $queryBuilder->where('file_id', '=', $id);
        
        return $this->applyConstraintsAndReturn($constraints, $queryBuilder);
    }
    
    private function applyConstraintsAndReturn(IDownloadQueryConstraints $constraints = NULL, IQueryBuilder $queryBuilder)
    {
        if($constraints)
        {
            $constraints->applyTo($queryBuilder);
        }

        return $this->_dataMapper->map('Download', $queryBuilder);
    }
}