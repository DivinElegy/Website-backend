<?php

namespace Domain\Entities;

use Domain\Entities\File;

interface IFileFactory
{
    public function createInstance(
        $hash,
        $path,
        $filename,
        $mimetype,
        $size,
        $uploadDate,
        array $mirrors = null
    );
}

class FileFactory implements IFileFactory
{
    public function createInstance(
        $hash,
        $path,
        $filename,
        $mimetype,
        $size,
        $uploadDate,
        array $mirrors = null
    ) {
        return new File(
            $hash,
            $path,
            $filename,
            $mimetype,
            $size,
            $uploadDate,
            $mirrors
        );
    }
}
