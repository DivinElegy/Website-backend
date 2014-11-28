<?php

namespace Domain\Entities;

interface IDownload
{
    public function getUser();
    public function getFile();
    public function getTimestamp();
    public function getIp();
}

