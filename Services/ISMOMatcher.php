<?php

namespace Services;

interface ISMOMatcher
{
    public function match($filename, $filesize);
}