<?php

namespace Services\Http;

interface IHttpResponse
{
    // TODO: Maybe we want a method to do an internal redirect to a different controller.
    // Maybe that should be done by the abstract controller class, though?
    public function setStatusCode($code);
    public function setHeader($name, $value);
    public function getHeaders();
    public function setBody($body);
    public function getBody();
    public function isRedirect();
    public function sendResponse();
}