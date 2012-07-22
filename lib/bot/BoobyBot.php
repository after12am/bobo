<?
require_once('DB.php');
require_once('Tweet.php');
require_once('MarkovAgent.php');
require_once('twitter/TwitterStream.php');

class BoobyBot extends TwitterStream {
    
    protected $markovAgent;
    
    public function __construct($userid, $passwd, $consumer_key, $consumer_secret, $access_token, $access_token_secret) {
        $this->markovAgent = new MarkovAgent();
        parent::__construct($userid, $passwd, $consumer_key, $consumer_secret, $access_token, $access_token_secret);
    }
    
    public function post() {
        
        $status = 'statuses/update';
        $hash = '#bot';
        
        // get artificially created text using markov chain method
        $tweet = $this->markovAgent->get($hash);
        
        // post to twitter
        $ret = $this->twitter->post($status, array('status' => $tweet));
        
        // error occurred
        if ($ret->error) {
            echo $ret->error . "\n";
            exit(0);
        }
        
        echo "@dev_12am:" . $tweet . "\n";
    }
    
    /*
        @int $num   pick up num
    */
    public function pickup($num, $ignore_ids = array(), $allow_langs = array()) {
        
        if (!preg_match('/^[0-9]+$/', $num)) {
            $num = NULL;
        }
        
        $db = DB::getInstance();
        $fp = $this->open(); // no need for deleting file pointer resource
        $i = 0;
        
        while($json = fgets($fp)) {
            
            if (($twitter = json_decode($json, true)) === NULL) {
                continue;
            }
            
            if ($this->filter($twitter, $ignore_ids, $allow_langs) === false) {
                continue;
            }
            
            $db->exec("BEGIN DEFERRED;");
            
            $data = array(
                'id' => $twitter['id_str'],
                'screen_name' => $twitter['user']['screen_name'],
                'tweet' => $this->clean($twitter['text'])
            );
            
            // save to database
            Tweet::save(array($data));
            $this->markovAgent->set($data['tweet']);
            
            $db->exec("COMMIT;");
            
            echo "@" . $data['screen_name'] . ":" . $data['tweet'] . "\n";
            
            if ($num !== NULL) {
                $i++;
                if ($i >= $num) {
                    break;
                }
            }
        }
    }
    
    private function filter($twitter, $ignore_ids = array(), $allow_langs = array()) {
        
        $id = $twitter['id_str'];
        $lang = $twitter['user']['lang'];
        $text = $twitter['text'];
        
        if (in_array($id, $ignore_ids)) return false;
        if (in_array($lang, $allow_langs) === false) return false;
        if ($text === '') return false;
        if (Tweet::isExist($id)) return false;
        
        return true;
    }
    
    private function clean($text) {
        
        $cleaned = preg_replace("(¥r¥n|¥r|¥n)", "", $text);
        $cleaned = preg_replace("/(#.* |#.*　|#.*)/", "", $cleaned);
        $cleaned = preg_replace("/( |　)*(QT|RT)( |　)*/", "", $cleaned);
        $cleaned = preg_replace("/( |　|.)*@[0-9a-zA-Z_]+(:)*(| |　)*(さん)*(の|が|を)*/", "", $cleaned);
        
        return trim($cleaned);
    }
}