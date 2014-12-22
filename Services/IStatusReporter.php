<?php

namespace Services;

use Exception;

interface IStatusReporter
{
    public function success($message = null);
    public function error($message = null);
    public function addMessage($message);
    public static function exception(Exception $exception);
    public function json();
}
