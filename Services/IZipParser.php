<?php

namespace Services;

use Domain\Entities\IFile;

interface IZipParser
{
    public function parse(IFile $zipFile);
    public function isPack();
    public function isSingle();
    public function pack();
    public function simfiles();
}