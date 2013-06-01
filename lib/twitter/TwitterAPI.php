<?php
require_once('twitteroauth/twitteroauth.php');

class TwitterAPI extends TwitterOAuth {
    
    protected $twitter;
    
    public function __construct($consumer_key, $consumer_secret, $access_token, $access_token_secret) {
        
        if (!$consumer_key || !$consumer_secret || !$access_token || !$access_token_secret) {
            echo 'twitter constant setup has not been completed.';
            exit(0);
        }
        
        parent::__construct($consumer_key, $consumer_secret, $access_token, $access_token_secret);
    }
}