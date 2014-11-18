<?php

namespace Domain\Entities\StepMania;

use Domain\Entities\IUser;
use Domain\Entities\IFile;

interface IPackBuilder
{
    public function With_Title($title);
    public function With_Uploader(IUser $uploader);
    public function With_Simfiles(array $simfiles);
    public function With_File(IFile $file);
    public function build();
}