<?php

namespace Controllers;

use Domain\Util;
use Services\Http\IHttpResponse;
use Services\Http\IHttpRequest;
use Services\IFacebookSessionFactory;
use Domain\Entities\IUserStepByStepBuilder;
use Domain\Entities\IUser;
use DataAccess\IUserRepository;
use Facebook\FacebookSession;
use Facebook\FacebookRequest;
use Facebook\GraphUser;
use Facebook\GraphLocation;
use Facebook\FacebookRequestException;

class UserAuthController implements IDivineController
{
    private $_response;
    private $_request;
    private $_facebookSessionFactory;
    private $_facebookSession;
    private $_facebookRequest;
    private $_userRepository;
    
    /* @var $_userStepByStepBuilder Domain\Entities\UserStepByStepBuilder */
    private $_userStepByStepBuilder;
    
    //override
    public function __construct(
        IHttpRequest $request,
        IHttpResponse $response,
        IFacebookSessionFactory $facebookSessionFactory,
        IUserRepository $userRepository,
        IUserStepByStepBuilder $userStepByStepBuilder
    ) {
        $this->_request = $request;
        $this->_response = $response;
        $this->_facebookSessionFactory = $facebookSessionFactory;
        $this->_userRepository = $userRepository;
        $this->_userStepByStepBuilder = $userStepByStepBuilder;
    }
        
    public function indexAction() {
        $token = $this->validateAuthRequest();
        $facebookSession = $this->_facebookSessionFactory->createInstance($token);
        
        $this->_facebookSession = $this->isSessionLongLived($facebookSession) ? $facebookSession->getLongLivedSession() : $facebookSession;
        $this->_facebookRequest = (new FacebookRequest($this->_facebookSession, 'GET', '/me?fields=hometown,first_name,last_name'))->execute();

        $id = $this->_facebookRequest->getGraphObject(GraphUser::className())->getId();
        
        // If the user is not in the DB, create them.
        $user = $this->_userRepository->findByFacebookId($id) ?: $this->registerUser();

        $this->_response->setHeader('Content-Type', 'application/json')
                        ->setBody(json_encode(array('token' => $this->_facebookSession->getToken(), 'expires' => $this->getSessionExpiryTimestamp($this->_facebookSession), 'displayName' => $user->getDisplayName())))
                        ->sendResponse();
    }
    
    private function validateAuthRequest()
    {
        $request = $this->_request->get();
        $response = $this->_response->setHeader('Content-Type', 'application/json');

        if(!isset($request['token']))
        {
            $response->setBody(json_encode(array('result' => 'error', 'message' => 'missing auth token')))
                     ->sendResponse();            
            die();            
        }
        
        return $request['token'];
    }
    
    private function registerUser()
    {
        $userProfile = $this->_facebookRequest->getGraphObject(GraphUser::className());    

        $homeTown = $userProfile->getProperty('hometown');   
        
        if($homeTown)
        {
            $homeTownPageId = $homeTown ? $homeTown->getProperty('id') : null;

            $pageRequest = (new FacebookRequest($this->_facebookSession, 'GET', '/' . $homeTownPageId ))->execute();
            $pageLocation = $pageRequest->getGraphObject(GraphLocation::className())->getProperty('location')->cast(GraphLocation::className());

            $country = Util::countryNameFromLatLong($pageLocation->getLatitude(), $pageLocation->getLongitude());
        }
        
        $firstName = $userProfile->getFirstName();
        $lastName = $userProfile->getLastName();
        $facebookId = $userProfile->getId();
        
        //TODO: Is insantiating the VO classes here a good idea?
        $newUser = $this->_userStepByStepBuilder->With_DisplayName($firstName)
                                                ->With_Name(new \Domain\VOs\Name($firstName, $lastName))
                                                ->With_Tags(array())
                                                ->With_FacebookId($facebookId)
                                                ->With_Quota(100000000) //XXX: quota is in bytes
                                                //XXX: Is this confusing? Maybe better to do a conditional and only call with_country when we have a country
                                                ->With_Country(isset($country) ? new \Domain\VOs\Country($country) : null)
                                                ->build();
                
        $this->_userRepository->save($newUser);

        return $newUser;
    }
    
    private function isSessionLongLived(FacebookSession $session)
    {
        return $this->getSessionExpiryTimestamp($session) - time() >= 60;
    }
    
    private function getSessionExpiryTimestamp(FacebookSession $session)
    {
        return $session->getSessionInfo()->getExpiresAt()->format('U');
    }
}
