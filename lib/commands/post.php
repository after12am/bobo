<?

// post artificially created text using a second-order Markov chain to twitter.

require_once('Configure.php');
require_once('twitter/TwitterSampleStream.php');
require_once('MarkovAgent.php');

class Post extends TwitterSampleStream {
    
    /**
     * Markov agent.
     * @param object
     */
    protected $agent;
    
    /**
     * Defines hashtags with array.
     * @param array
     */
    protected $hashtags;
    
    public function __construct($hashtags = array('#bot')) {
        
        $this->agent = new MarkovAgent();
        $this->hashtags = $hashtags;
        
        parent::__construct(
            Configure::read('twitter.user_id'),
            Configure::read('twitter.password'),
            Configure::read('twitter.consumer_key'),
            Configure::read('twitter.consumer_secret'),
            Configure::read('twitter.access_token'),
            Configure::read('twitter.access_token_secret')
        );
    }
    
    public function post($text) {
        
        $res = parent::post('statuses/update', array('status' => $text));
        
        if ($res->error) {
            echo "{$res->error}\n";
            exit(0);
        }
        
        echo "@dev_12am:$text\n";
    }
    
    public function execute() {
        
        $this->post($this->agent->text($this->hashtags));
    }
}

$command = new Post();
$command->execute();