<?php

namespace Domain\Entities;

interface IFileBuilder
{
    public function With_Hash($hash);
    public function With_Path($path);
    public function With_Filename($filename);
    public function With_Mimetype($mimetype);
    public function With_Size($size);
    public function With_UploadDate($date);
    public function build();
}