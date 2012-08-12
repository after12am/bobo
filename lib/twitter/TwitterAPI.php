<?
require_once('twitteroauth/twitteroauth.php');

class TwitterAPI {
    
    protected $consumer_key;
    protected $consumer_secret;
    protected $access_token;
    protected $access_token_secret;
    protected $twitter;
    
    public function __construct($consumer_key, $consumer_secret, $access_token, $access_token_secret) {
        
        if (!$consumer_key || !$consumer_secret || !$access_token || !$access_token_secret) {
            echo 'twitter constant setup has not been completed.';
            exit(0);
        }
        
        $this->consumer_key = $consumer_key;
        $this->consumer_secret = $consumer_secret;
        $this->access_token = $access_token;
        $this->access_token_secret = $access_token_secret;
        $this->twitter = new TwitterOAuth($consumer_key, $consumer_secret, $access_token, $access_token_secret);
    }
}