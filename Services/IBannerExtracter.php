<?php

namespace Services;

interface IBannerExtracter
{
    public function extractBanner($zipfile, $bannerName);
}
