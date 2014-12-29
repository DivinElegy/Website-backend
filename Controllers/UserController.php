<?php

namespace Controllers;

use Exception;
use Domain\Exception\InvalidCountryException;
use Controllers\IDivineController;
use Services\Http\IHttpRequest;
use Services\Http\IHttpResponse;
use Services\IUserQuota;
use Services\IStatusReporter;
use Services\IUserSession;
use DataAccess\IUserRepository;
use Domain\Util;
use Domain\VOs\Country;

class UserController implements IDivineController
{
    private $_userRepository;
    private $_response;
    private $_request;
    private $_userQuota;
    private $_statusReporter;
    private $_userSession;
    
    public function __construct(
        IHttpRequest $request,
        IHttpResponse $response,
        IUserRepository $userRepository,
        IUserQuota $userQuota,
        IStatusReporter $statusReporter,
        IUserSession $userSession
    ) {
        $this->_request = $request;
        $this->_response = $response;
        $this->_userRepository = $userRepository;
        $this->_userQuota = $userQuota;
        $this->_statusReporter = $statusReporter;
        $this->_userSession = $userSession;
    }
    
    public function indexAction() {
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
            'country' => $user->getCountry()->getCountryName(),
        );
        
        if($this->_userSession->getCurrentUser())
        {
            $returnArray['quota'] = Util::bytesToHumanReadable($user->getQuota());
            $returnArray['quotaRemaining'] =  Util::bytesToHumanReadable($this->_userQuota->getCurrentUserQuotaRemaining());
        }

        $this->_response->setHeader('Content-Type', 'application/json')
                        ->setBody(json_encode($returnArray))
                        ->sendResponse();
    }
    
    public function updateAction($facebookId)
    {
        if(!$user = $this->_userSession->getCurrentUser()) $this->_statusReporter->error('You must be authenticated update your account.');
        $userUpdateData = $this->userFromJson($this->_request->getBody());
        
        try
        {
            if(isset($userUpdateData->displayName)) $user->setDisplayName($userUpdateData->displayName);
            //TODO: Direct instantiation bad?
            if(isset($userUpdateData->country)) $user->setCountry(new Country($userUpdateData->country));
            $this->_userRepository->save($user);
        } catch (Exception $e) {
            if(strpos($e->getMessage(), 'Duplicate entry') !== false)
            {
                $this->_statusReporter->error('Sorry, that display name is already in use.');
            }
            
            if($e instanceof InvalidCountryException)
            {
                $this->_statusReporter->error($userUpdateData->country . ' is not a valid country.');
            }
        }
        
        $this->_statusReporter->success();
    }
    
    private function userFromJson($json)
    {
        $user = json_decode($json);
        if(json_last_error() !== JSON_ERROR_NONE || !$user) $this->_statusReporter->error ('Malformed or missing JSON');
        
        return $user;
    }
}
