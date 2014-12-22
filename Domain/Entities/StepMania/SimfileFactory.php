<?php

namespace Domain\Entities\StepMania;

use Domain\VOs\StepMania\IArtist;
use Domain\VOs\StepMania\IBPM;
use Domain\Entities\StepMania\Simfile;
use Domain\Entities\IUser;
use Domain\Entities\IFile;

interface ISimfileFactory
{
    public function createInstance(
        $title,
        IArtist $artist = null,
        IUser $uploader,
        IBPM $bpm,
        $bpmChanges,
        $stops,
        $fgChanges,
        $bgChanges,
        IFile $banner,
        IFile $simfile,
        $packId = null,
        array $steps
    );
}

class SimfileFactory implements ISimfileFactory
{
    public function createInstance(
        $title,
        IArtist $artist = null,
        IUser $uploader,
        IBPM $bpm,
        $bpmChanges,
        $stops,
        $fgChanges,
        $bgChanges,
        IFile $banner = null,
        IFile $simfile = null,
        $packId = null,
        array $steps
    ) {
        return new Simfile(
            $title,
            $artist,
            $uploader, //TODO: will be user object
            $bpm,
            $bpmChanges,
            $stops,
            $fgChanges,
            $bgChanges,
            $banner,
            $simfile,
            $packId,
            $steps
        );
    }
}
