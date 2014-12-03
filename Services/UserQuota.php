<?php

namespace Services;

use Services\IUserQuota;
use Services\IUserSession;
use DataAccess\IDownloadRepository;
use DataAccess\Queries\DownloadQueryConstraints;
use DateTime;

class UserQuota implements IUserQuota
{
    private $_userSession;
    private $_downloadRepository;
    
    public function __construct(
        IUserSession $userSession,
        IDownloadRepository $downloadRepository
    ) {
        $this->_userSession = $userSession;
        $this->_downloadRepository = $downloadRepository;
    }
    
    public function getCurrentUserQuotaRemaining()
    {
        $start = new DateTime('0:00 today'); // start of today
        $end = new DateTime(); // now
        $user = $this->_userSession->getCurrentUser();
        
        if(!$user) return null;
        
        // TODO: factory?
        $constraints = new DownloadQueryConstraints();
        $constraints->inDateRange($start, $end);
        $downloads = $this->_downloadRepository->findByUserId($user->getId(), $constraints);

        return $user->getQuota() - $this->sumDownloads($downloads);
    }
    
    private function sumDownloads(array $downloads)
    {
        $total = 0;
        
        foreach($downloads as $download)
        {
            $total += $download->getFile()->getSize();
        }
        
        return $total;
    }
}
