<?php

namespace Services;

use Services\Http\IHttpRequest;
use DataAccess\IUserRepository;

class UserSession implements IUserSession
{
    private $_userRepository;
    private $_request;
    private $_currentUser;
    
    public function __construct(IHttpRequest $request, IUserRepository $repository)
    {
        $this->_request = $request;
        $this->_userRepository = $repository;

        $token = $this->findToken();
        $this->_currentUser = $token ? $this->_userRepository->findByAuthToken($token) : null;
    }
    
    public function getCurrentUser()
    {
        return $this->_currentUser;
    }
    
    private function findToken()
    {        
        if($this->_request->isPost())
        {
            $request = $this->_request->post();
            if(!empty($request['token'])) return $request['token'];
        }
        
        if($this->_request->isGet())
        {
            $request = $this->_request->get();
            if(!empty($request['token'])) return $request['token'];
        }
        
        //no good, try the body
        $body = json_decode($this->_request->getBody(), true);
        if(!empty($body['token'])) return $body['token'];
        
        return null;
    }
}