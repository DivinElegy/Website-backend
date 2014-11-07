<?php

namespace DataAccess;

use DataAccess\IRepository;
use Domain\Entities\IUser;

interface IUserRepository extends IRepository
{
    public function findByAuthToken($token);
    public function findByFacebookId($id);
    public function save(IUser $entity);
    public function remove(IUser $entity);
}

    
