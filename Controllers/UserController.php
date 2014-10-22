<?php

namespace Controllers;

use Controllers\IDivineController;
use Services\Http\IHttpRequest;
use Services\Http\IHttpResponse;
use DataAccess\IUserRepository;

class UserController implements IDivineController
{
    private $_userRepository;
    private $_response;
    private $_request;
    
    public function __construct(
        IHttpRequest $request,
        IHttpResponse $response,
        IUserRepository $userRepository
    ) {
        $this->_request = $request;
        $this->_response = $response;
        $this->_userRepository = $userRepository;
    }
    
    public function indexAction() {
        ;
    }
    
    // list simfiles
    public function getUserAction($facebookId)
    {
        /* @var $user Domain\Entities\IUser */
        $user = $this->_userRepository->findByFacebookId($facebookId);

        $returnArray = array(
            'id' => $user->getId(),
            'name' => $user->getName()->getFullName(),
            'displayName' => $user->getDisplayName(),
            'tags' => $user->getTags(),
            'country' => $user->getCountry()->getCountryName()
        );

        $this->_response->setHeader('Content-Type', 'application/json')
                        ->setBody(json_encode($returnArray))
                        ->sendResponse();
    }
}
