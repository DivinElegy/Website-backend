<?php

namespace Domain\Entities;

interface IFile extends IDivineEntity
{
    public function getPath();
    public function getHash();
    public function getFilename();
    public function getMimetype();
    public function getSize();
    public function getUploadDate();
}