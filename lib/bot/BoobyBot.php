<?
require_once('db/Markov.php');
require_once('db/Tweet.php');
require_once('twitter/TwitterStream.php');

class BoobyBot extends TwitterStream {
    
    public function __construct($userid, $passwd, $consumer_key, $consumer_secret, $access_token, $access_token_secret) {
        parent::__construct($userid, $passwd, $consumer_key, $consumer_secret, $access_token, $access_token_secret);
    }
    
    public function post() {
        
    }
    
    public function gather($ignore_ids = array()) {
        
        // no need for deleting file pointer resource
        $fp = $this->open();
        
        while($json = fgets($fp)) {
            
            if (($twitter = json_decode($json, true)) === NULL) continue;
            
            $id = $twitter['id_str'];
            $name = $twitter['user']['screen_name'];
            $lang = $twitter['user']['lang'];
            $text = $this->clean($twitter['text']);
            
            if (in_array($id, $ignore_ids)) continue;
            if ($lang !== "ja") continue;
            if (!$text) continue;
            if (Tweet::isExist($id)) continue;
            
            Tweet::save($id, $text);
            Markov::save($text);
            
            printf("@%s:%s\n", $name, $text);
        }
    }
    
    private function filter() {
        
    }
    
    private function clean($text) {
        
        $cleaned = preg_replace("(¥r¥n|¥r|¥n)", "", $text);
        $cleaned = preg_replace("/(#.* |#.*　|#.*)/", "", $cleaned);
        $cleaned = preg_replace("/( |　)*(QT|RT)( |　)*/", "", $cleaned);
        $cleaned = preg_replace("/( |　|.)*@[0-9a-zA-Z_]+(:)*(| |　)*(さん)*(の|が|を)*/", "", $cleaned);
        
        return trim($cleaned);
    }
}