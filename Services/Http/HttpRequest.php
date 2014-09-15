<?php

namespace Services\Http;

use Services\Http\IHttpRequest;
use Services\Http\Util;

class HttpRequest implements IHttpRequest
{
    const METHOD_GET = 'GET';
    const METHOD_POST = 'POST';
    const METHOD_PUT = 'PUT';
    const METHOD_DELETE = 'DELETE';
    const METHOD_HEAD = 'HEAD';
    
    protected $_method;
    protected $_cookies;
    protected $_formDataMediaTypes = array('application/x-www-form-urlencoded');

    public function __construct()
    {
        $this->_method = $_SERVER['REQUEST_METHOD'];
        
        if(isset($_SERVER['HTTP_COOKIE'])) {
            $this->_cookies = Util::parseCookieHeader($_SERVER['HTTP_COOKIE']);
        }
    }
    
    public function getMethod()
    {
        return $this->_method;
    }
    
    public function isGet()
    {
        return $this->_method == self::METHOD_GET;
    }
    
    public function isPost()
    {
        return $this->_method == self::METHOD_POST;
    }
    
    public function isPut()
    {
        return $this->_method == self::METHOD_PUT;
    }
    
    public function isHead()
    {
        return $this->_method == self::METHOD_HEAD;
    }
    
    public function isDelete()
    {
        return $this->_method == self::METHOD_DELETE;
    }
    
    public function getContentType()
    {
        return isset($_SERVER['CONTENT_TYPE']) ? $_SERVER['CONTENT_TYPE'] : null;
    }
    
    public function isFormData()
    {
        return in_array($this->getContentType(), $this->_formDataMediaTypes);
    }
    
    public function get()
    {
        return !empty($_GET) ? $_GET : null;
    }
    
    public function getGetElement($key)
    {
        return isset($_GET[$key]) ? $_GET[$key] : null;
    }
    
    public function post()
    {
        return !empty($_POST) ? $_POST : null;
    }
    
    public function getPostElement($key)
    {
        return isset($_POST[$key]) ? $_POST[$key] : null;
    }
    
    public function put()
    {
        return $this->post();
    }
    
    public function getPutElement($key)
    {
        return $this->getPostElement($key);
    }
    
    public function delete()
    {
        return $this->post();
    }
    
    public function getDeleteElement($key)
    {
        return $this->getPostElement($key);
    }
    
    public function cookies()
    {
        return $this->_cookies;
    }
    
    public function getCookie($key)
    {
        return isset($this->_cookies[$key]) ? $this->_cookies[$key] : null;
    }
    
    public function getBody()
    {
        return @file_get_contents('php://input');
    }
    public function getHost()
    {
        if(isset($_SERVER['HTTP_HOST'])) {
            $parts = explode(':', $_SERVER['HTTP_HOST']);
            return $parts[0];
        }
        
        return $_SERVER['SERVER_NAME'];
    }
    
    public function getIp()
    {
        $keys = array('X_FORWARDED_FOR', 'HTTP_X_FORWARDED_FOR', 'CLIENT_IP', 'REMOTE_ADDR');
        foreach ($keys as $key) {
            if(isset($_SERVER[$key])) {
                return $_SERVER[$key];
            }
        }
    }
    
    public function getReferrer()
    {
        return $_SERVER['HTTP_REFERER'];
    }
    
    // some people like to spell it referer due to RFC 1945
    public function getReferer()
    {
        return $this->getReferer();
    }
    
    public function getUserAgent()
    {
        return $_SERVER['HTTP_USER_AGENT'];
    }
    
    private function getPhysicalPath()
    {
        $scriptName = $_SERVER['SCRIPT_NAME']; // <-- "/foo/index.php"
        $requestUri = $_SERVER['REQUEST_URI']; // <-- "/foo/bar?test=abc" or "/foo/index.php/bar?test=abc"   
        
        // Physical path
        if (strpos($requestUri, $scriptName) !== false) {
            $physicalPath = $scriptName; // <-- Without rewriting
        } else {
            $physicalPath = str_replace('\\', '', dirname($scriptName)); // <-- With rewriting
        }
        
        return $physicalPath;
    }
    
    public function getPath()
    {
            $physicalPath = $this->getPhysicalPath();
            $requestUri = $_SERVER['REQUEST_URI']; // <-- "/foo/bar?test=abc" or "/foo/index.php/bar?test=abc"   
            $queryString = isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : ''; // <-- "test=abc" or ""

            // Virtual path
            $path = substr_replace($requestUri, '', 0, strlen($physicalPath)); // <-- Remove physical path
            $path = str_replace('?' . $queryString, '', $path); // <-- Remove query string
            $path = '/' . ltrim($path, '/'); // <-- Ensure leading slash
            $path = rtrim($path, '/'); // remove / from the end of string
            
            return $path;
    }
}