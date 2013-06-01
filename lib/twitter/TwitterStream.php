<?php
require_once('twitter/TwitterAPI.php');

abstract class TwitterStream extends TwitterAPI {
    
    const API = 'ssl://stream.twitter.com';
    const PORT = 443;
    
    protected $userid;
    protected $passwd;
    protected $fp;
    protected $errno;
    protected $errmsg;
    
    public function __construct($userid, $passwd, $consumer_key, $consumer_secret, $access_token, $access_token_secret) {
        parent::__construct($consumer_key, $consumer_secret, $access_token, $access_token_secret);
        $this->userid = $userid;
        $this->passwd = $passwd;
    }
    
    public function __destruct() {
        if ($this->fp) fclose($this->fp);
    }
    
    protected function open() {
        $this->errno = 0;
        $this->errmsg = "";
        // try to establish a connection to a streaming
        // for being delivered a feed of Tweets, without
        // needing to worry about polling or REST API rate limits.
        $this->fp = fsockopen(self::API, self::PORT, $this->errno, $this->errmsg, $this->timeout);
        if ($this->fp === false) {
            echo "\n[errno {$this->errno}] {$this->errmsg}\n\n";
            return false;
        }
        $this->connect();
        return $this->fp;
    }
    
    abstract protected function connect();
}