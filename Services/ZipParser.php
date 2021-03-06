<?php

namespace Services;

use Exception;
use Domain\Exception\InvalidDifficultyException;
use Domain\Exception\InvalidDanceModeException;
use ZipArchive;
use Services\ISimfileParser;
use Services\IBannerExtracter;
use Domain\Entities\IFile;
use Domain\Entities\StepMania\ISimfileStepByStepBuilder;
use Domain\Entities\StepMania\IPackStepByStepBuilder;
use Services\IZipParser;
use Services\IUserSession;
use Services\IStatusReporter;
use Services\InvalidSmFileException;
use Services\IConfigManager;

class ZipParser implements IZipParser
{
    private $_za;
    private $_smFiles = array();
    private $_smParser;
    private $_smBuilder;
    private $_packBuilder;
    private $_bannerExtracter;
    private $_userSession;
    private $_file;
    private $_statusReporter;
    private $_configManager;
    
    public function __construct(
        ISimfileParser $smParser,
        ISimfileStepByStepBuilder $smBuilder,
        IPackStepByStepBuilder $packBuilder,
        IBannerExtracter $bannerExtracter,
        IUserSession $userSession,
        IStatusReporter $statusReporter,
        IConfigManager $configManager
    ) {
        $this->_smParser = $smParser;
        $this->_smBuilder = $smBuilder;
        $this->_packBuilder = $packBuilder;
        $this->_bannerExtracter = $bannerExtracter;
        $this->_userSession = $userSession;
        $this->_statusReporter = $statusReporter;
        $this->_configManager = $configManager;
    }
    
    public function parse(IFile $file)
    {
        $this->_file = $file;
        $this->_za = new ZipArchive();
        //XXX: We assume all files are zips. Should be enforced by validation elsewhere.
        $res = $this->_za->open($this->_configManager->getDirective('filesPath') . '/StepMania/' . $file->getHash() . '.zip');

        if($res !== true) throw new Exception ('Could not open zip for reading.');
        $this->findSms();
    }
    
    public function pack()
    {
        if(count($this->_smFiles) > 1)
        {         
            $packname = $this->packNameFromFiles();
            $banner = $this->_bannerExtracter->extractPackBanner($this->_configManager->getDirective('filesPath') . '/StepMania/' . $this->_file->getHash() . '.zip', $packname);

            /* @var $builder \Domain\Entities\StepMania\PackStepByStepBuilder */
            $builder = $this->_packBuilder;
            return $builder->With_Title($packname)
                           ->With_Uploader($this->_userSession->getCurrentUser())
                           //->With_Simfiles($this->_smFiles)
                           ->With_Simfiles(array())
                           ->With_Banner($banner)
                           ->With_File($this->_file)
                           ->build();
        }
    }
        
    public function simfiles()
    {
        return $this->_smFiles;
    }
    
    public function isPack()
    {
        return count($this->_smFiles) > 1;
    }
    
    public function isSingle()
    {
        return count($this->_smFiles) == 1;
    }
    
    private function findSms()
    {
        for($i=0; $i<$this->_za->numFiles; $i++)
        {
            $stat = $this->_za->statIndex($i);
            if(pathinfo($stat['name'], PATHINFO_EXTENSION) == 'sm')
            {
                $path = realpath($this->_configManager->getDirective('filesPath') . '/StepMania/' . $this->_file->getHash() . '.zip');
                $smData = file_get_contents('zip://' . $path . '#' . $stat['name']);
                $this->_smFiles[$stat['name']] = $smData;
            }
        }
        
        //XXX: Hack. SmDataToSmClass needs to know whether we are dealing with a pack
        //or single, but to do that we simply check the number of found sm files. To overcome this
        //first populate the smFiles array with the raw sm data, then apply SmDataToSmClass on each
        //array element. This way the check is accurate and the array gets populated as expected.
        foreach($this->_smFiles as $index => $data)
        {
            try
            {
                $this->_smFiles[$index] = $this->SmDataToSmClass($data, $index);
            } catch(Exception $e) {
                //Exceptions we care about at this stage
                if(!$e instanceof InvalidSmFileException && 
                   !$e instanceof InvalidDanceModeException &&
                   !$e instanceof InvalidDifficultyException)
                {
                    throw $e;
                }

                //Add to messages.
                $this->_statusReporter->addMessage($e->getMessage() . ' in ' . $index);
                unset($this->_smFiles[$index]);
            }
        }
        //$this->_smFiles = array_map(array($this, 'SmDataToSmClass'), $this->_smFiles);
    }
    
    private function packNameFromFiles()
    {
        $packName = '';
        $smpaths = array_keys($this->_smFiles);
        foreach($smpaths as $path)
        {
            $pathComponents = explode('/', $path);
            
            if(empty($packName)) $packName = $pathComponents[0];
            
            if($packName != $pathComponents[0])
                throw new Exception('Malformed zip. I found more than 1 sm file but the directory structure is not consistent with a pack.');
        }
        
        return $packName;
    }
        
    private function SmDataToSmClass($smData, $index)
    {      
        $parser = $this->_smParser;
        $parser->parse($smData);

        $bannerIndex = $this->resolveBannerIndex($parser->banner(), $index);
        $banner = $this->_bannerExtracter->extractSongBanner(realpath($this->_configManager->getDirective('filesPath') . '/StepMania/' . $this->_file->getHash() . '.zip'), $bannerIndex);

        $file = $this->isPack() ? null : $this->_file;

        return $this->_smBuilder->With_Title($parser->title())
                                ->With_Artist($parser->artist())
                                ->With_Uploader($this->_userSession->getCurrentUser()) //obj
                                ->With_BPM($parser->bpm())
                                ->With_BpmChanges($parser->bpmChanges())
                                ->With_Stops($parser->stops())
                                ->With_FgChanges($parser->fgChanges())
                                ->With_BgChanges($parser->bgChanges())
                                ->With_Steps($parser->steps())
                                ->With_Simfile($file)
                                ->With_Banner($banner)
                                ->build();
    }
    
    private function resolveBannerIndex($bannerDirective, $smFileIndex)
    {
        if(strpos($bannerDirective, '../') !== false || strpos($bannerDirective, '..\\') !== false)
        {
            $pathInfo = pathinfo($smFileIndex);
            return str_replace('..', dirname($pathInfo['dirname']), $bannerDirective);
        }
        
        return dirname($smFileIndex) . '/' . $bannerDirective;
    }
}