<?
require_once('TwitterStream.php');

class TwitterSampleStream extends TwitterStream {
    
    protected function connect() {
        
        $ver = phpversion();
        $basic = base64_encode("{$this->userid}:{$this->passwd}");
        
        $req  = "GET /1/statuses/sample.json HTTP/1.1\r\n";
        $req .= "Host: stream.twitter.com\r\n";
        $req .= "User-Agent: PHP/{$ver}\r\n";
        $req .= "Authorization: Basic {$basic}\r\n";
        $req .= "Connection: Close\r\n\r\n";
        
        // request streams of the public data flowing through Twitter.
        fwrite($this->fp, $req);
    }
}