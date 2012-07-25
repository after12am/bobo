<?
require_once('DB.php');
require_once('Tweet.php');
require_once('MarkovAgent.php');
require_once('twitter/TwitterSampleStream.php');

class BoobyBot extends TwitterSampleStream {
    
    public $deny_id = array();
    public $allow_lang = array();
    
    protected $markovAgent;
    
    
    public function __construct($userid, $passwd, $consumer_key, $consumer_secret, $access_token, $access_token_secret) {
        
        $this->markovAgent = new MarkovAgent();
        
        parent::__construct($userid, $passwd, $consumer_key, $consumer_secret, $access_token, $access_token_secret);
    }
    
    public function setup() {
        
        DB::setup();
    }
    
    public function post($signature = '') {
        
        $status = 'statuses/update';
        
        // get artificially created text using markov chain method
        $this->markovAgent->signature = $signature;
        $tweet = $this->markovAgent->get();
        
        // post to twitter
        $ret = $this->twitter->post($status, array('status' => $tweet));
        
        // error occurred
        if ($ret->error) {
            echo $ret->error . "\n";
            exit(0);
        }
        
        echo "@dev_12am:" . $tweet . "\n";
        
        return $tweet;
    }
    
    /*
        @int $num   pick up num
    */
    public function pickup($num = 1000) {
        
        $db = DB::getInstance();
        $fp = $this->open(); // no need for deleting file pointer resource
        $i = 0;
        
        while($json = fgets($fp)) {
            
            if (($twitter = json_decode($json, true)) === NULL) {
                continue;
            }
            
            if (($twitter = $this->filter($twitter)) === false) {
                continue;
            }
            
            try {
                $db->beginTransaction();
                $this->save($twitter);
                $db->commit();
            } catch (Exception $e) {
                $db->rollback();
                echo $e->getTraceAsString();
                continue;
            }
            
            echo "@" . $twitter['user']['screen_name'] . ":" . $twitter['text'] . "\n";
            
            $i++;
            if ($i >= $num) {
                break;
            }
        }
    }
    
    private function save($twitter) {
        
        $data = array(
            'id' => $twitter['id_str'],
            'screen_name' => $twitter['user']['screen_name'],
            'tweet' => $twitter['text']
        );
        
        Tweet::save(array($data));
        
        $this->markovAgent->heap($data['id'], $data['tweet']);
    }
    
    private function filter($twitter) {
        
        if (in_array($twitter['id_str'], $this->deny_id)) {
            return false;
        }
        
        if (in_array($twitter['user']['lang'], $this->allow_lang) === false) {
            return false;
        }
        
        if (Tweet::exist($id)) {
            return false;
        }
        
        $twitter['text'] = preg_replace("(¥r¥n|¥r|¥n)", "", $twitter['text']);
        $twitter['text'] = preg_replace("/(#.* |#.*　|#.*)/", "", $twitter['text']);
        $twitter['text'] = preg_replace("/( |　)*(QT|RT)( |　)*/", "", $twitter['text']);
        $twitter['text'] = preg_replace("/( |　|.)*@[0-9a-zA-Z_]+(:)*(| |　)*(さん)*(の|が|を)*/", "", $twitter['text']);
        $twitter['text'] = trim($twitter['text']);
        
        if ($twitter['text'] === '') {
            return false;
        }
        
        return $twitter;
    }
}