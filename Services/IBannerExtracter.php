<?php

namespace Services;

interface IBannerExtracter
{
    public function extractSongBanner($zipfile, $bannerName);
    public function extractPackBanner($zipfile, $packname);
}
