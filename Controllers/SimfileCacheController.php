<?php

namespace Controllers;

use Exception;
use Controllers\IDivineController;
use Services\IConfigManager;
use Services\Http\IHttpRequest;
use DataAccess\StepMania\ISimfileRepository;
use DataAccess\StepMania\IPackRepository;
use Domain\Util;

class SimfileCacheController implements IDivineController
{
    private $_configManager;
    private $_request;
    private $_simfileRepository;
    private $_packRepository;
    
    public function __construct(
        IConfigManager $configManager,
        IHttpRequest $request,
        ISimfileRepository $simfileRepository,
        IPackRepository $packRepository
    ) {
        $this->_configManager = $configManager;
        $this->_request = $request;
        $this->_packRepository = $packRepository;
        $this->_simfileRepository = $simfileRepository;
    }
    
    public function indexAction() {
        $get = $this->_request->get();
        if(!$get['cronToken']) throw new Exception('Token missing');
        
        //TODO: I should make $req->get('token') give the element and $req->get() return the array maybe?
        if($get['cronToken'] !== $this->_configManager->getDirective('cronToken')) throw new Exception ('Invalid token');
        
        $all_files = scandir('../SimfileCache');
        natsort($all_files);
        $most_recent = end($all_files);
        $limit = $this->_configManager->getDirective('maxEntitiesToLoad');
        $simfileArray = array();
        $packArray = array();
        $num = 1;
        $simfileId = 0;
        $packId = 0;

        if($most_recent !== 'simfiles.json' && $most_recent !== '..')
        {
            $json = json_decode(file_get_contents('../SimfileCache/' . $most_recent));
            $packId = $json->packId;
            $simfileId = $json->simfileId;
            $num = explode('_', $most_recent);
            $num = substr(end($num),0,-5) + 1; //lol
        }

        $simfiles = $this->_simfileRepository->findRange($simfileId+1, $limit);
        $packs = $this->_packRepository->findRange($packId+1, $limit);
        
        if(!$simfiles && !$packs)
        {
            $completeArray = array();
            foreach($all_files as $filename)
            {
                if(strpos($filename, 'simfiles_') !== false)
                {
                $json = json_decode(file_get_contents('../SimfileCache/' . $filename), true);
                unset($json['packId']);
                unset($json['simfileId']);
                $completeArray = array_merge_recursive($completeArray, $json);
                unlink('../SimfileCache/' . $filename);
                }
            }
            
            usort($completeArray['packs'], function($a, $b)
            {
                return strcmp($a['title'], $b['title']);
            });
            
            file_put_contents('../SimfileCache/simfiles.json',json_encode($completeArray));
        } else {
            foreach($simfiles as $simfile)
            {
                $simfileArray[] = Util::simfileToArray($simfile);
            }

            foreach($packs as $pack)
            {
                $packArray[] = Util::packToArray($pack);
            }

            $lastSimfileId = end($simfiles) ? end($simfiles)->getId() : $simfileId;
            $lastPackId = end($packs) ? end($packs)->getId() : $packId;

            file_put_contents(
                '../SimfileCache/simfiles_' .$num . '.json',
                json_encode(
                    array(
                        'simfiles' => $simfileArray,
                        'packs' => $packArray,
                        'packId' => $lastPackId,
                        'simfileId' => $lastSimfileId
                    )
                )
            );
        }
//        $simfiles = $this->_simfileRepository->findRange();
//        $packs = $this->_packRepository->findAll();
//        $simfileArray = array();
//        $packArray = array();
//        
//        foreach($simfiles as $simfile)
//        {
//            $simfileArray[] = $this->simfileToArray($simfile);
//        }
//        
//        foreach($packs as $pack)
//        {
//            $packArray[] = array(
//                'title'=> $pack->getTitle(),
//                'contributors' => $pack->getContributors(),
//                'simfiles' => $this->getPackSimfilesArray($pack),
//                'banner' => $pack->getBanner() ? 'files/banner/' . $pack->getBanner()->getHash() : 'files/banner/default',
//                'mirrors' => $this->getPackMirrorsArray($pack),
//                'size' => $pack->getFile() ? Util::bytesToHumanReadable($pack->getFile()->getSize()) : null,
//                'uploaded' => $pack->getFile() ? date('F jS, Y', $pack->getFile()->getUploadDate()) : null
//            );
//        }
//        
//        $returnArray = array('simfiles' => $simfileArray, 'packs' => $packArray);
    }
}