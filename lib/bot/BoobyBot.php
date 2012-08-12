<?
require_once('DB.php');
require_once('MarkovAgent.php');
require_once('twitter/TwitterSampleStream.php');

class BoobyBot extends TwitterSampleStream {
    
    /**
     * twitter ids to filter
     *
     * @access public
     * @var array $allow_lang
     */
    public $deny_id = array();
    
    /**
     * langages not to filter
     *
     * @access public
     * @var array
     */
    public $allow_lang = array();
    
    /**
     * agent for markov tweet
     * 
     * @access public
     * @var object
     */
    protected $agent;
    
    /**
     * BoobyBot
     *
     * @param string $userid
     * @param string $passwd
     * @param string $consumer_key
     * @param string $consumer_secret
     * @param string $access_token
     * @param string $access_token_secret
     */
    public function __construct($userid, $passwd, $consumer_key, $consumer_secret, $access_token, $access_token_secret) {
        
        $this->agent = new MarkovAgent();
        
        parent::__construct($userid, $passwd, $consumer_key, $consumer_secret, $access_token, $access_token_secret);
    }
    
    /**
     * setup database
     * @return
     */
    public function setup() {
        
        DB::setup();
    }
    
    /**
     * tweet
     *
     * @param string $signature
     * @return string
     */
    public function post($signature = '') {
        
        // get artificially created text using markov chain method
        $tweet = $this->agent->getText($signature);
        
        // tweet
        $ret = $this->twitter->post('statuses/update', array('status' => $tweet));
        
        if ($ret->error) {
            echo $ret->error . "\n";
            exit(0);
        }
        
        echo "@dev_12am:" . $tweet . "\n";
        
        return $tweet;
    }
    
    /**
     * gather tweet
     *
     * @param int $num
     */
    public function pickup($num) {
        
        $i = 0;
        
        if (($fp = $this->open()) === false) {
            return;
        }
        
        while($json = fgets($fp)) {
            
            if ($i >= $num) {
                break;
            }
            
            if (($twitter = json_decode($json, true)) === NULL) {
                continue;
            }
            
            if (($twitter = $this->filter($twitter)) === false) {
                continue;
            }
            
            if ($this->agent->save($twitter)) {
                echo "@" . $twitter['user']['screen_name'] . ":" . $twitter['text'] . "\n";
                $i++;
            }
        }
    }
    
    /**
     * filter twitter strem data
     *
     * @param array $twitter
     * @return array
     */
    private function filter($twitter) {
        
        $twitter['text'] = preg_replace("(¥r¥n|¥r|¥n)", "", $twitter['text']);
        $twitter['text'] = preg_replace("/(#.* |#.*　|#.*)/", "", $twitter['text']);
        $twitter['text'] = preg_replace("/( |　)*(QT|RT)( |　)*/", "", $twitter['text']);
        $twitter['text'] = preg_replace("/( |　|.)*@[0-9a-zA-Z_]+(:)*(| |　)*(さん)*(の|が|を)*/", "", $twitter['text']);
        $twitter['text'] = trim($twitter['text']);
        
        if (in_array($twitter['id_str'], $this->deny_id)) {
            return false;
        }
        
        if (in_array($twitter['user']['lang'], $this->allow_lang) === false) {
            return false;
        }
        
        if ($twitter['text'] === '') {
            return false;
        }
        
        return $twitter;
    }
}