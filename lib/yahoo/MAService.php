<?php
require_once('YahooAPI.php');

class MAService extends YahooAPI {
    
    const API = 'http://jlp.yahooapis.jp/MAService/V1/parse';
    
    public function analyse($sentence, $results = 'ma') {
        $api = self::API;
        $appid = $this->appid;
        $sentence = urlencode($sentence);
        $api = "{$api}?appid={$appid}&results={$results}&sentence={$sentence}";
        return simplexml_load_file($api);
    }
    
    public function words($sentence, $results = 'ma') {
        $ret = $this->analyse($sentence, $results);
        $ret = $ret->ma_result->word_list->word;
        return $ret;
    }
}