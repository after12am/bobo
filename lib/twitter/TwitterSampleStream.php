<?
require_once('TwitterStream.php');

class TwitterSampleStream extends TwitterStream {
    
    const PATH = "/1/statuses/sample.json";
    
    protected function connect() {
        
        $host = self::HOST;
        $path = self::PATH;
        $ver = phpversion();
        $basic = base64_encode("{$this->userid}:{$this->passwd}");
        
        $req  = "GET {$path} HTTP/1.1\r\n";
        $req .= "Host: {$host}\r\n";
        $req .= "User-Agent: PHP/{$ver}\r\n";
        $req .= "Authorization: Basic {$basic}\r\n";
        $req .= "Connection: Close\r\n\r\n";
        
        // request streams of the public data flowing through Twitter.
        fwrite($this->fp, $req);
    }
}