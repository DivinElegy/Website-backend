<?php

namespace Services\Uploads;

interface IUploadManager {
    public function setDestination($path);
    public function process();
}
