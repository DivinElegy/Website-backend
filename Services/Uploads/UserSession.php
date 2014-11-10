<?php

namespace Services;

use Services\IUserSession;
use Services\Http\IHttpRequest;
use DataAccess\IUserRepository;

class UserSession implements IUserSession
{
    private $_httpRequest;
    private $_userRepository;
    private $_currentUser;
    
    public function __construct(IHttpRequest $httpRequest, IUserRepository $userRepository)
    {
        $this->_httpRequest = $httpRequest;
        $this->_userRepository = $userRepository;
    }
    
    public function getCurrentUser() {
        if(empty($this->_currentUser))
        {
            $request = $this->_httpRequest->isGet() ? $this->_httpRequest->get() 
                                                    : json_decode($this->_httpRequest->getBody(), true);

            $token = isset($request['token']) ? $request['token'] : null;
            $this->_currentUser = $this->_userRepository->findByAuthToken($token);
        }
        
        return $this->_currentUser;
    }
}

