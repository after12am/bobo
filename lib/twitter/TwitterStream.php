<?
require_once('twitter/TwitterAPI.php');

abstract class TwitterStream extends TwitterAPI {
    
    const HOST = "stream.twitter.com";
    
    protected $userid;
    protected $passwd;
    protected $port = 443;
    protected $timeout = 30;
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
    
    protected function open() {
        
        $host = self::HOST;
        $this->errno = 0;
        $this->errmsg = "";
        
        // try to establish a connection to a streaming
        // for being delivered a feed of Tweets, without
        // needing to worry about polling or REST API rate limits.
        $this->fp = fsockopen("ssl://{$host}", $this->port, $this->errno, $this->errmsg, $this->timeout);
        
        if ($this->fp === false) {
             return false;
        }
        
        $this->connect();
        
        return $this->fp;
    }
    
    abstract protected function connect();
}