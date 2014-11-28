<?php

namespace Domain\Entities;

use Domain\Entities\Download;

interface IDownloadFactory
{
    public function createInstance(
        IUser $user,
        IFile $file,
        $timestamp,
        $ip
    );
}

class DownloadFactory implements IDownloadFactory
{
    public function createInstance(
        IUser $user,
        IFile $file,
        $timestamp,
        $ip
    ) {
        return new Download(
            $user,
            $file,
            $timestamp,
            $ip
        );
    }
}
