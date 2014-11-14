<?php

namespace Services;

interface ISimfileParser
{
    public function parse($smFileData);
    public function title();
    public function subtitle();
    public function artist();
    public function bpm();
    public function bpmChanges();
    public function stops();
    public function bgChanges();
    public function fgChanges();
    public function steps();
    public function banner();
}