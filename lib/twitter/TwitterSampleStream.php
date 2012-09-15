<?
require_once('TwitterStream.php');

class TwitterSampleStream extends TwitterStream {
    
    protected $path = "/1/statuses/sample.json";
    
    protected function connect() {
        
        $ver = phpversion();
        $basic = base64_encode("{$this->userid}:{$this->passwd}");
        
        $req  = "GET {$this->path} HTTP/1.1\r\n";
        $req .= "Host: {$this->hosts['stream']}\r\n";
        $req .= "User-Agent: PHP/{$ver}\r\n";
        $req .= "Authorization: Basic {$basic}\r\n";
        $req .= "Connection: Close\r\n\r\n";
        
        // request streams of the public data flowing through Twitter.
        fwrite($this->fp, $req);
    }
}