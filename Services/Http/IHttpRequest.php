<?php

namespace Services\Http;

interface IHttpRequest
{
    public function getMethod();
    public function isGet();
    public function isPost();
    public function isPut();
    public function isDelete();
    public function isHead();
    public function isFormData();
    public function get();
    public function put();
    public function post();
    public function delete();
    public function cookies();
    public function getBody();
    public function getContentType();
    public function getHost();
    public function getIp();
    public function getReferrer();
    public function getReferer();
    public function getUserAgent();
    public function getPath();
}