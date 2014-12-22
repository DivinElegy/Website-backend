<?php

namespace Domain\Entities\StepMania;

use Domain\VOs\StepMania\IArtist;
use Domain\VOs\StepMania\IBPM;
use Domain\Entities\StepMania\ISimfileBuilder;
use Domain\Entities\IUser;
use Domain\Entities\IFile;

interface ISimfileStepByStepBuilder
{
    public function With_Title($title);
}

interface ISimfileStepByStepBuilder_With_Title
{
    public function With_Artist(IArtist $artist = null);
    public function With_Uploader(IUser $uploader);
}

interface ISimfileStepByStepBuilder_With_Uploader
{
    public function With_BPM(IBPM $bpm);
}

interface ISimfileStepByStepBuilder_With_BPM
{
    public function With_BpmChanges($const);
}

interface ISimfileStepByStepBuilder_With_BpmChanges
{
    public function With_Stops($const);
}

interface ISimfileStepByStepBuilder_With_Stops
{
    public function With_FgChanges($const);
}

interface ISimfileStepByStepBuilder_With_FgChanges
{
    public function With_BgChanges($const);
}

interface ISimfileStepByStepBuilder_With_BgChanges
{
    public function With_Steps(array $steps);
}

interface ISimfileStepByStepBuilder_With_Steps
{
    public function With_Banner(IFile $banner = null);
    public function With_Simfile(IFile $simfile = null);
    public function With_PackId($packId = null);
    public function build();
}


abstract class AbstractSimfileStepByStepBuilder
{
    /* @var $_simfileBuilder Domain\Entities\StepMania\ISimfileBuilder */
    protected $_simfileBuilder;
    
    public function __construct(ISimfileBuilder $builder)
    {
        $this->_simfileBuilder = $builder;
    }
}


class SimfileStepByStepBuilder extends AbstractSimfileStepByStepBuilder implements ISimfileStepByStepBuilder
{
    public function With_Title($title)
    {
        $this->_simfileBuilder->With_Title($title);
        return new SimfileStepByStepBuilder_With_Title($this->_simfileBuilder);
    }
}

class SimfileStepByStepBuilder_With_Title extends AbstractSimfileStepByStepBuilder implements ISimfileStepByStepBuilder_With_Title
{        
    public function With_Artist(IArtist $artist = null)
    {
        $this->_simfileBuilder->With_Artist($artist);
        return $this;
    }
    
    public function With_Uploader(IUser $uploader)
    {
        $this->_simfileBuilder->With_Uploader($uploader);
        return new SimfileStepByStepBuilder_With_Uploader($this->_simfileBuilder);
    }
}

class SimfileStepByStepBuilder_With_Uploader extends AbstractSimfileStepByStepBuilder implements ISimfileStepByStepBuilder_With_Uploader
{
    public function With_BPM(IBPM $bpm)
    {
        $this->_simfileBuilder->With_BPM($bpm);
        return new SimfileStepByStepBuilder_With_BPM($this->_simfileBuilder);
    }
}

class SimfileStepByStepBuilder_With_BPM extends AbstractSimfileStepByStepBuilder implements ISimfileStepByStepBuilder_With_BPM
{
    public function With_BpmChanges($const)
    {
        $this->_simfileBuilder->With_BpmChanges($const);
        return new SimfileStepByStepBuilder_With_BpmChanges($this->_simfileBuilder);
    }
}

class SimfileStepByStepBuilder_With_BpmChanges extends AbstractSimfileStepByStepBuilder implements ISimfileStepByStepBuilder_With_BpmChanges
{
    public function With_Stops($const) {
        $this->_simfileBuilder->With_Stops($const);
        return new SimfileStepByStepBuilder_With_Stops($this->_simfileBuilder);
    }
}

class SimfileStepByStepBuilder_With_Stops extends AbstractSimfileStepByStepBuilder implements ISimfileStepByStepBuilder_With_Stops
{
    public function With_FgChanges($const)
    {
        $this->_simfileBuilder->With_FgChanges($const);
        return new SimfileStepByStepBuilder_With_FgChanges($this->_simfileBuilder);
    }
}

class SimfileStepByStepBuilder_With_FgChanges extends AbstractSimfileStepByStepBuilder implements ISimfileStepByStepBuilder_With_FgChanges
{
    public function With_BgChanges($const)
    {
        $this->_simfileBuilder->With_BgChanges($const);
        return new SimfileStepByStepBuilder_With_BgChanges($this->_simfileBuilder);
    }
}

class SimfileStepByStepBuilder_With_BgChanges extends AbstractSimfileStepByStepBuilder implements ISimfileStepByStepBuilder_With_BgChanges
{
    public function With_Steps(array $steps)
    {
        $this->_simfileBuilder->With_Steps($steps);
        return new SimfileStepByStepBuilder_With_Steps($this->_simfileBuilder);
    }
}

class SimfileStepByStepBuilder_With_Steps extends AbstractSimfileStepByStepBuilder implements ISimfileStepByStepBuilder_With_Steps
{
    public function With_Banner(IFile $banner = null)
    {
        $this->_simfileBuilder->With_Banner($banner);
        return new SimfileStepByStepBuilder_With_Steps($this->_simfileBuilder); //TODO: Pretty sure return $this will be OK
    }
    
    public function With_Simfile(IFile $simfile = null)
    {
        $this->_simfileBuilder->With_Simfile($simfile);
        return new SimfileStepByStepBuilder_With_Steps($this->_simfileBuilder);
    }
    
    public function With_PackId($packId = null)
    {
        $this->_simfileBuilder->With_PackId($packId);
        return new SimfileStepByStepBuilder_With_Steps($this->_simfileBuilder);
    }
    
    public function build()
    {
        return $this->_simfileBuilder
                    ->build();
    }
}