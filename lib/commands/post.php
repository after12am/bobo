<?php
/*
    post artificially created text using a second-order Markov chain to twitter.
*/
require_once('Configure.php');
require_once('twitter/TwitterSampleStream.php');
require_once('MarkovAgent.php');

class Post extends TwitterSampleStream {
    
    /**
     * Defines hashtags with array.
     * @param array
     */
    protected $hashtags;
    
    protected $agent;
    
    public function __construct($hashtags = array('#bot')) {
        parent::__construct(
            Configure::read('twitter.user_id'),
            Configure::read('twitter.password'),
            Configure::read('twitter.consumer_key'),
            Configure::read('twitter.consumer_secret'),
            Configure::read('twitter.access_token'),
            Configure::read('twitter.access_token_secret')
        );
        $this->hashtags = $hashtags;
        $this->agent = new MarkovAgent();
    }
    
    public function execute() {
        $text = $this->agent->text($this->hashtags);
        $result = parent::post('statuses/update', array('status' => $text));
        if ($result->error) {
            echo "{$result->error}\n";
            exit(0);
        }
        echo "$text\n";
    }
}

$command = new Post();
$command->execute();