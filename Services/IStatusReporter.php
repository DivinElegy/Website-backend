<?php

namespace Services;

use Exception;

interface IStatusReporter
{
    public function success($message);
    public function error($message);
    public static function exception(Exception $exception);
    public function json();
}
