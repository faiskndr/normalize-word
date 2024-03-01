<?php


class Csv{
    private $csv = array();
    
    public function getCsv(){
        $lines = file('colloquial-indonesian-lexicon.csv');
        foreach ($lines as $key=>$line) {
            $this->csv[$key] = str_getcsv($line);
        }

        return $this->csv;  
    }

}
// echo '<pre>';
// print_r($csv);
// echo '</pre>';