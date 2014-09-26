<?php

namespace Services\Uploads;

use Services\Uploads\IFileFactory;
use Services\Uploads\File;

class FileFactory implements IFileFactory
{
    public function createInstance($name, $type, $tempName, $size) {
        return new File($name, $type, $tempName, $size);
    }
}
