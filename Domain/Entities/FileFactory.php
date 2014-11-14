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
        $uploadDate
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
        $uploadDate
    ) {
        return new File(
        $hash,
        $path,
        $filename,
        $mimetype,
        $size,
        $uploadDate
        );
    }
}
