<?php

namespace Services\Http;

use Services\Http\IHttpResponse;
use Exception;

class HttpResponse implements IHttpResponse
{
    private $_statusCode = 200;
    private $_headers = array();
    private $_body;
    private $_isRedirect = false;
    
    public function setStatusCode($code)
    {
        if(!is_int($code) || (100 > $code) || (599 < $code)) {
            throw new Exception(sprintf('Invalid HTTP response code, %u', $code));
        }
        
        $this->_isRedirect = (300 <= $code) && (307 >= $code);
        $this->_statusCode = $code;
        
        return $this;
    }
    
    public function isRedirect()
    {
        return $this->_isRedirect;
    }
    
    public function setHeader($name, $value)
    {
        $value = (string) $value;
        
        $this->_headers[$name] = $value;
        
        return $this;
    }
    
    public function getHeaders()
    {
        return $this->_headers;
    }
    
    private function sendHeaders()
    {
        $statusCodeSent = false;
        
        if(!count($this->_headers)) {
            return $this;
        }
        
        foreach($this->_headers as $headerName => $headerValue) {
            if(!$statusCodeSent) {
                header(
                    sprintf('%s: %s', $headerName, $headerValue),
                    false,
                    $this->_statusCode);
                
                $statusCodeSent = true;
            } else {
                header(
                    sprintf('%s: %s', $headerName, $headerValue));
            }
        }
        
        return $this;
    }
    
    public function setBody($content)
    {
        $this->_body = $content;
        
        return $this;
    }
    
    public function getBody()
    {
        return $this->_body;
    }
    
    
    private function sendBody()
    {
        echo $this->_body;
        
        return $this;
    }
        
    public function sendResponse()
    {
        $this->sendHeaders()
             ->sendBody();
    }
    
    public function download($path)
    {
        $fp = fopen($path, "rb");
        $this->sendHeaders();
        @ob_clean();
        rewind($fp);
        fpassthru($fp);
    }
}
