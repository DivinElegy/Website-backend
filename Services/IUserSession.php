<?php

namespace Services;

interface IUserSession
{
    public function getCurrentUser();
    public function getCurrentUserQuota();
}

