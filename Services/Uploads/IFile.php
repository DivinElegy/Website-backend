<?php

namespace Services\Uploads;

interface IFile {
    public function getExtension();
    public function getName();
    public function getTempName();
    public function getType();
    public function getSize();
}