<?php

namespace Services;

use Exception;
use Services\Http\IHttpResponse;
use Services\IStatusReporter;

class StatusReporter implements IStatusReporter
{
    private $_messages = array();
    private $_type;
    private $_response;
    
    const ERROR = 'error';
    const SUCCESS = 'success';
    const EXCEPTION = 'exception';
    
    public function __construct(IHttpResponse $response) {
        $this->_response = $response;
    }
    
    public function error($message = null)
    {
        if($message) $this->addMessage($message);
        $this->_type = self::ERROR;
        $this->_response->setHeader('Content-Type', 'application/json')
                        ->setBody($this->json())
                        ->sendResponse();
        exit();
    }
    
    public function success($message = null)
    {
        if($message) $this->addMessage($message);
        $this->_type = self::SUCCESS;
        $this->_response->setHeader('Content-Type', 'application/json')
                        ->setBody($this->json())
                        ->sendResponse();
        exit();
    }
    
    public function addMessage($message)
    {
        $this->_messages[] = $message;;
    }
    
    //no need to exit here, exceptions stop the program.
    public static function exception(Exception $exception)
    {       
        //we'll be instatic context here so I have to do it this way.
        header('Content-Type: application/json');
        echo json_encode(array(
            'status' => self::EXCEPTION,
            'messages' => array($exception->getMessage())));
    }
    
    public function json()
    {
        return json_encode(
            array(
                'status' => $this->_type,
                'messages' => $this->_messages)
        );
    }
}
