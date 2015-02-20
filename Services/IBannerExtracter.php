<?php

namespace Services;

interface IBannerExtracter
{
    public function extractSongBanner($zipfile, $bannerIndex);
    public function extractPackBanner($zipfile, $packname);
}
