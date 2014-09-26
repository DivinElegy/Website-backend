<?php

namespace Services\Uploads;

interface IFileFactory
{
    public function createInstance($name, $type, $tempName, $size);
}