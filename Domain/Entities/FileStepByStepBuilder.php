<?php

namespace Domain\Entities;

use Domain\Entities\IFileBuilder;

interface IFileStepByStepBuilder
{
    public function With_Hash($hash);
}

interface IFileStepByStepBuilder_With_Hash
{
    public function With_Path($path);
}

interface IFileStepByStepBuilder_With_Path
{
    public function With_Filename($filename); //TODO: Make user object
}

interface IFileStepByStepBuilder_With_Filename
{
    public function With_Mimetype($mimetype);
}

interface IFileStepByStepBuilder_With_Mimetype
{
    public function With_Size($size);
}

interface IFileStepByStepBuilder_With_Size
{
    public function With_UploadDate($date);
}

interface IFileStepByStepBuilder_With_UploadDate
{
    public function With_Mirrors(array $mirrors = null);
    public function build();
}

abstract class AbstractFileStepByStepBuilder
{
    /* @var $_simfileBuilder Domain\Entities\StepMania\ISimfileBuilder */
    protected $_fileBuilder;
    
    public function __construct(IFileBuilder $builder)
    {
        $this->_fileBuilder = $builder;
    }
}

class FileStepByStepBuilder extends AbstractFileStepByStepBuilder implements IFileStepByStepBuilder
{
    public function With_Hash($hash) {
        $this->_fileBuilder->With_Hash($hash);
        return new FileStepByStepBuilder_With_Hash($this->_fileBuilder);
    }
}

class FileStepByStepBuilder_With_Hash extends AbstractFileStepByStepBuilder implements IFileStepByStepBuilder_With_Hash
{
    public function With_Path($path) {
        $this->_fileBuilder->With_Path($path);
        return new FileStepByStepBuilder_With_Path($this->_fileBuilder);
    }
}

class FileStepByStepBuilder_With_Path extends AbstractFileStepByStepBuilder implements IFileStepByStepBuilder_With_Path
{
    public function With_Filename($filename) {
        $this->_fileBuilder->With_Filename($filename);
        return new FileStepByStepBuilder_With_Filename($this->_fileBuilder);
    }
}

class FileStepByStepBuilder_With_Filename extends AbstractFileStepByStepBuilder implements IFileStepByStepBuilder_With_Filename
{
    public function With_Mimetype($mimetype) {
        $this->_fileBuilder->With_Mimetype($mimetype);
        return new FileStepByStepBuilder_With_Mimetype($this->_fileBuilder);
    }
}

class FileStepByStepBuilder_With_Mimetype extends AbstractFileStepByStepBuilder implements IFileStepByStepBuilder_With_Mimetype
{
    public function With_Size($size) {
        $this->_fileBuilder->With_Size($size);
        return new FileStepByStepBuilder_With_Size($this->_fileBuilder);
    }
}

class FileStepByStepBuilder_With_Size extends AbstractFileStepByStepBuilder implements IFileStepByStepBuilder_With_Size
{
    public function With_UploadDate($date) {
        $this->_fileBuilder->With_UploadDate($date);
        return new FileStepByStepBuilder_With_UploadDate($this->_fileBuilder);
    }
}

class FileStepByStepBuilder_With_UploadDate extends AbstractFileStepByStepBuilder implements IFileStepByStepBuilder_With_UploadDate
{
    public function With_Mirrors(array $mirrors = null) {
        $this->_fileBuilder->With_Mirrors($mirrors);
        return new FileStepByStepBuilder_With_UploadDate($this->_fileBuilder);
    }
    
    public function build() {
        return $this->_fileBuilder
                    ->build();
    }
}