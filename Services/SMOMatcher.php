<?php

namespace Services;

use Services\ISMOMatcher;
use DOMDocument;
use DOMXPath;
use DOMNodeList;

class SMOMatcher implements ISMOMatcher
{
    private $_records;
    
    public function match($title, $filesize)
    {
        if(!$this->scrapeSmo()) return null;

        $most_likely = array('confidence' => 0);
        foreach($this->_records as $rowNum => $row) {
            $cells = $row->getElementsByTagName('td');
    
            if($rowNum > 0) {
                $candidate = $this->cellToCandidateArray($cells);
                $this->setCandidateSimilarty($title, $filesize, $candidate);
                $most_likely = $candidate['confidence'] > $most_likely['confidence'] ? $candidate : $most_likely;
            }
        }
        
        return $most_likely;
    }
    
    private function scrapeSmo()
    {
        if($this->_records) return true;
        
        $c = curl_init('http://stepmaniaonline.net/index.php?page=downloads');
        curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
        //curl_setopt(... other options you want...)
        
        $html = curl_exec($c);
        
        if (curl_error($c)) return false;

        // Get the status code
        $status = curl_getinfo($c, CURLINFO_HTTP_CODE);

        curl_close($c);
        //$html = file_get_contents('smo.html');
        $dom = new DOMDocument();
        @$dom->loadHTML($html);
        $xpath = new DOMXPath($dom);

        $table = $xpath->query("/html/body/div[@class='container']/div[@class='mid']/div[@class='content']/div[3]/div[@class='blockcontent']/table")->item(0);
        $this->_records = $table->getElementsByTagName("tr");
        
        return true;
    }
    
    private function cellToCandidateArray(DOMNodeList $cells)
    {
        return array(
            'href' => $cells->item(0)->getElementsByTagName('a')->item(0)->getAttribute('href'),
            'title' => $cells->item(0)->nodeValue,
            'filesize' => strpos($cells->item(1)->nodeValue, 'Gb') === true ? ($cells->item(1)->nodeValue)*1024*1024*1024 : ($cells->item(1)->nodeValue)*1024*1024            
        );
    }
    
    private function setCandidateSimilarty($title, $filesize, array &$candidate)
    {
        similar_text($title, $candidate['title'], $percent);
        $r = $candidate['filesize'] > $filesize ? $filesize/$candidate['filesize'] : $candidate['filesize']/$filesize;
        $candidate['confidence'] = $percent*$r;
    }
}