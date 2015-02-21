<?php

namespace Services\Uploads;

interface IUploadQueueManager {
    public function setFilesDirectory($path);
    public function setDestination($path);
    public function process($num);
}
