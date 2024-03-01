<?php

require 'vendor/autoload.php';
include 'get_csv.php';

$csv = new Csv();

$dictionary =$csv->getCsv();

$response = file_get_contents('https://api.apify.com/v2/datasets/vD2nh9Mv7NmEA87FK/items?token=apify_api_o2SD4dM9B2Vfy97qrKgilHwa8BqJsf4epvxo');
$steamFactory = new \Sastrawi\Stemmer\StemmerFactory();
$steammer = $steamFactory->createStemmer();
// var_dump(json_decode($response));
$data = json_decode($response);
$text = [];
foreach($data as $d){
    $result = remove_emoji($d->text);
    if($result!=""){
        // array_push($text,['ori'=>$result,'root'=>$root]);
        array_push($text,trim(preg_replace('/\s+/',' ',strtolower($result))));
    }
}

$words = array();

foreach ($text as $key => $value) {

    array_push($words, explode(" ",$value));
}


foreach ($words as $key => $word) {

    foreach ($word as $i=> $word_value) {
        $normal = true;
        foreach ($dictionary as $j => $value) {
            if($word_value == $value[0]){
                $word_value = $value[1];
                $normal = false;
                continue;
            }
        }
        if(!$normal){
            $words[$key][$i] = $word_value;
        }
        // var_dump($word_value);
        
    }
}

foreach ($words as $key => $word) {
    foreach ($word as $i=>$value) {
        $words[$key][$i] = $steammer->stem($value);
        
    }
}

// var_dump($words);
file_put_contents('normal_comments.json',json_encode($words));


function remove_emoji($string)
{
    // Match Enclosed Alphanumeric Supplement
    $regex_alphanumeric = '/[\x{1F100}-\x{1F1FF}]/u';
    $clear_string = preg_replace($regex_alphanumeric, '', $string);

    // Match Miscellaneous Symbols and Pictographs
    $regex_symbols = '/[\x{1F300}-\x{1F5FF}]/u';
    $clear_string = preg_replace($regex_symbols, '', $clear_string);

    // Match Emoticons
    $regex_emoticons = '/[\x{1F600}-\x{1F64F}]/u';
    $clear_string = preg_replace($regex_emoticons, '', $clear_string);

    // Match Transport And Map Symbols
    $regex_transport = '/[\x{1F680}-\x{1F6FF}]/u';
    $clear_string = preg_replace($regex_transport, '', $clear_string);
    
    // Match Supplemental Symbols and Pictographs
    $regex_supplemental = '/[\x{1F900}-\x{1F9FF}]/u';
    $clear_string = preg_replace($regex_supplemental, '', $clear_string);

    // Match Miscellaneous Symbols
    $regex_misc = '/[\x{2600}-\x{26FF}]/u';
    $clear_string = preg_replace($regex_misc, '', $clear_string);

    // Match Dingbats
    $regex_dingbats = '/[\x{2700}-\x{27BF}]/u';
    $clear_string = preg_replace($regex_dingbats, '', $clear_string);

    $pattern='/[^a-zA-Z ]/';
    $clear_string = preg_replace($pattern,' ',$clear_string);

    return $clear_string;
}