<?php

namespace Domain\Entities;

use Domain\VOs\IFileMirror;

interface IFile extends IDivineEntity
{
    public function getPath();
    public function getHash();
    public function getFilename();
    public function getMimetype();
    public function getSize();
    public function getUploadDate();
    public function getMirrors();
    public function addMirror(IFileMirror $mirror);
}