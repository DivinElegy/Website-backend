<?php

namespace Services;

interface IFacebookSessionFactory {
    public function createInstance($token);
}