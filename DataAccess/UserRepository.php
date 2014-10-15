<?php

namespace DataAccess;

use DataAccess\IUserRepository;
use DataAccess\DataMapper\IDataMapper;
use Domain\Entities\IUser;
use DataAccess\Queries\IQueryBuilderFactory;

//TODO: Implement some sort of caching. Probably OK for now not to worry.
class UserRepository implements IUserRepository
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
            'User',
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
    
    public function findByFacebookId($id) {
        $queryBuilder = $this->_queryBuilderFactory->createInstance();
        $queryBuilder->where('facebook_id', '=', "$id");
        
        $results = $this->_dataMapper->map(
                    'User',
                    $queryBuilder
                   );
                
        return reset($results);
    }
    
    public function findByAuthToken($token) {
        return $this->_dataMapper->map(
            'User',
            'SELECT * FROM %s WHERE auth_token=' . $token
        );
    }
    
    public function save(IUser $entity) {
        $this->_dataMapper->save($entity);
    }
    
    //TODO: Implement
    public function remove(IUser $entity) {
        ;
    }
    
}
