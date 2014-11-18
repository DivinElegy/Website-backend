<?php

namespace Domain\Entities\StepMania;

use Domain\Entities\StepMania\IPackBuilder;
use Domain\Entities\IUser;
use Domain\Entities\IFile;

interface IPackStepByStepBuilder
{
    public function With_Title($title);
}

interface IPackStepByStepBuilder_With_Title
{
    public function With_Uploader(IUser $user);
}

interface IPackStepByStepBuilder_With_Uploader
{
    public function With_Simfiles(array $simfiles);
}

interface IPackStepByStepBuilder_With_Simfiles
{
    public function With_File(IFile $file);
    public function build();
}

abstract class AbstractPackStepByStepBuilder
{
    /* @var $_simfileBuilder Domain\Entities\StepMania\ISimfileBuilder */
    protected $_packBuilder;
    
    public function __construct(IPackBuilder $builder)
    {
        $this->_packBuilder = $builder;
    }
}


class PackStepByStepBuilder extends AbstractPackStepByStepBuilder implements IPackStepByStepBuilder
{
    public function With_Title($title)
    {
        $this->_packBuilder->With_Title($title);
        return new PackStepByStepBuilder_With_Title($this->_packBuilder);
    }
}

class PackStepByStepBuilder_With_Title extends AbstractPackStepByStepBuilder implements IPackStepByStepBuilder_With_Title
{        
    public function With_Uploader(IUser $user)
    {
        $this->_packBuilder->With_Artist($artist);
        return new PackStepByStepBuilder_With_Artist($this->_packBuilder);
    }
}

class PackStepByStepBuilder_With_Uploader extends AbstractPackStepByStepBuilder implements IPackStepByStepBuilder_With_Uploader
{
    public function With_Simfiles(array $simfiles)
    {
        $this->_packBuilder->With_Simfiles($simfiles);
        return new PackStepByStepBuilder_With_Simfiles($this->_packBuilder);
    }
}

class PackStepByStepBuilder_With_Simfiles extends AbstractPackStepByStepBuilder implements IPackStepByStepBuilder_With_Simfiles
{
    public function With_File(Ifile $file)
    {
        $this->_packBuilder->With_File($file);
    }
    
    public function build()
    {
        return $this->_simfileBuilder
                    ->build();
    }
}
