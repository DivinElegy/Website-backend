<?php

namespace Services;

use ZipArchive;
use finfo;
use Exception;
use Services\IBannerExtracter;
use DataAccess\IFileRepository;
use Domain\Entities\IFileStepByStepBuilder;

class BannerExtracter implements IBannerExtracter
{
    private $_builder;
    private $_destinationFileName;
    private $_hash;
    private $_fileRepository;

    public function __construct(IFileStepByStepBuilder $builder, IFileRepository $fileRepository) {
        $this->_builder = $builder;
        $this->_fileRepository = $fileRepository;
    }
    
    public function extractBanner($zipfile, $bannerName) {
        $za = new ZipArchive();
        //XXX: We assume all files are zips. Should be enforced by validation elsewhere.
        $res = $za->open($zipfile);

        if($res !== true) throw new Exception ('Could not open zip for reading.');

        for($i=0; $i<$za->numFiles; $i++)
        {
            $stat = $za->statIndex($i);
            if(basename($stat['name']) == $bannerName)
            {
                $this->_hash = $this->randomFilename($bannerName);
                $this->_destinationFileName = $this->_hash . '.' . pathinfo($bannerName, PATHINFO_EXTENSION);
                $result = copy('zip://' . $zipfile . '#' . $stat['name'], '../files/banners/' . $this->_destinationFileName);
                break;
            }
        }

        if(!isset($result) || !$result) throw new Exception('Could not extract banner.');

        $finfo = new finfo(FILEINFO_MIME);
        $mimetype = $finfo->file('../files/banners/' . $this->_destinationFileName);
        $size = filesize('../files/banners/' . $this->_destinationFileName);
        /* @var $fff \Domain\Entities\FileStepByStepBuilder */
        $file= $this->_builder->With_Hash($this->_hash)
                              ->With_Path('banners')
                              ->With_Filename($bannerName)
                              ->With_Mimetype($mimetype)
                              ->With_Size($size)
                              ->With_UploadDate(time())
                              ->build();
        
        return $this->_fileRepository->save($file);
    }
    
    private function randomFilename($seed)
    {
        return sha1(mt_rand(1, 9999) . $seed . uniqid() . time());
    }
}