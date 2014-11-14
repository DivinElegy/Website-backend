<?php

namespace Services\Uploads;

interface IUploadManager {
    public function setFilesDirectory($path);
    public function setDestination($path);
    public function process();
}
