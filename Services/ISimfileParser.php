<?php

namespace Services;

interface ISimfileParser
{
    public function title();
    public function subtitle();
    public function bpmChanges();
    public function stops();
    public function bgChanges();
    public function fgChanges();
    public function steps();
}