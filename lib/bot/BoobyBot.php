<?
require_once('db/Markov.php');
require_once('db/Tweet.php');
require_once('twitter/TwitterStream.php');

class BoobyBot extends TwitterStream {
    
    public function __construct($userid, $passwd, $consumer_key, $consumer_secret, $access_token, $access_token_secret) {
        parent::__construct($userid, $passwd, $consumer_key, $consumer_secret, $access_token, $access_token_secret);
    }
    
    public function post() {
        
        $tweet = '';
        
        // begin
        $rows = Markov::find('BOF');
        
        if (count($rows) > 0) {
            
            $ma = $rows[mt_rand(0, count($rows) - 1)];
        	$tweet .= $ma['lex2'];
            
        	while ($ma['lex3'] !== 'EOF') {
        	    
        	    $rows = Markov::find($ma['lex3']);
        	    
        	    // I don't understand why I can't get target rows.
                if (count($rows) === 0) {
                    break;
                }
                
                $ma = $rows[mt_rand(0, count($rows) - 1)];
                
                $tweetLen = mb_strlen($tweet, 'UTF-8');
                $addedLen = mb_strlen($ma['lex1'] . $ma['lex2'], 'UTF-8');
                
                if ($tweetLen + $addedLen > 140) {
                    break;
                }
                
        	    $tweet .= $ma['lex1'] . $ma['lex2'];
        	}
        }
        
        echo "@dev_12am:" . $tweet . "\n";
    }
    
    public function pick($ignore_ids = array(), $allow_langs = array()) {
        
        // no need for deleting file pointer resource
        $fp = $this->open();
        
        while($json = fgets($fp)) {
            
            $twitter = json_decode($json, true);
            
            if ($twitter === NULL) {
                continue;
            }
            
            if ($this->filter($twitter, $ignore_ids, $allow_langs) === false) {
                continue;
            }
            
            $this->save($twitter);
        }
    }
    
    private function save($twitter) {
        
        $id = $twitter['id_str'];
        $name = $twitter['user']['screen_name'];
        $cleaned = $this->clean($twitter['text']);
        
        Tweet::save($id, $cleaned);
        Markov::save($cleaned);
        
        echo "@" . $name . ":" . $cleaned . "\n";
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