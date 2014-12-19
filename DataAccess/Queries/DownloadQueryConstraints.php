<?php

namespace DataAccess\Queries;

use DataAccess\Queries\IQueryBuilder;
use DataAccess\Queries\IDownloadQueryConstraints;
use DateTime;

class DownloadQueryConstraints implements IDownloadQueryConstraints
{        
    private $_queryBuilder;
    private $_dateRangeStart;
    private $_dateRangeEnd;
    
    public function applyTo(IQueryBuilder $queryBuilder)
    {
        $this->_queryBuilder = $queryBuilder;
        $this->applyDateRange();
    }
    
    public function inDateRange(DateTime $start, DateTime $end)
    {
        $this->_dateRangeStart = $start;
        $this->_dateRangeEnd = $end;
        return $this;
    }
    
    private function applyDateRange()
    {
        if($this->_dateRangeStart && $this->_dateRangeEnd) {
            $this->_queryBuilder->where('timestamp', '>=', $this->_dateRangeStart->getTimestamp())
                                ->where('timestamp', '<=', $this->_dateRangeEnd->getTimestamp());
        }
        
        return $this;
    }
}
    