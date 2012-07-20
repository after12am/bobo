<?
require_once('twitter/TwitterAPI.php');

class TwitterStream extends TwitterAPI {
    
    protected $userid;
    protected $passwd;
    protected $fp;
    protected $errno;
    protected $errmsg;
    
    public function __construct($userid, $passwd, $consumer_key, $consumer_secret, $access_token, $access_token_secret) {
        
        $this->userid = $userid;
        $this->passwd = $passwd;
        parent::__construct($consumer_key, $consumer_secret, $access_token, $access_token_secret);
    }
    
    public function __destruct() {
        
        if ($this->fp) {
            fclose($this->fp);
        }
    }
    
    protected function open($host = "stream.twitter.com", $path = "/1/statuses/sample.json", $port = 443, $timeout = 30) {
        
        $this->errno = 0;
        $this->errmsg = "";
        
        // try to establish a connection to a streaming
        // for being delivered a feed of Tweets, without
        // needing to worry about polling or REST API rate limits.
        $this->fp = fsockopen("ssl://{$host}", $port, $this->errno, $this->errmsg, $timeout);
        
        if ($this->fp === false) {
             return false;
        }
        
        $this->connect($host, $path);
        
        return $this->fp;
    }
    
    private function connect($host, $path) {
        
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