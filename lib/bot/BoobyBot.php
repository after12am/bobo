<?
require_once('db/Markov.php');
require_once('db/Tweet.php');
require_once('twitter/TwitterAPI.php');

class BoobyBot extends TwitterAPI {
    
    public function __construct($consumer_key, $consumer_secret, $access_token, $access_token_secret) {
        parent::__construct($consumer_key, $consumer_secret, $access_token, $access_token_secret);
    }
    
    public function post() {
        
    }
    
    public function gather($userid, $passwd, $ignore_ids = array()) {
        
        // gather using stream api through Twitter
        
        $host = "stream.twitter.com";
        $path = "/1/statuses/sample.json";
        $port = 443;
        $timeout = 30;
        
        $fp = fsockopen("ssl://{$host}", $port, $errno, $errmsg, $timeout);
        
        if ($fp === false) {
            echo "{$errmsg} ({$errno})<br />\n";
            return;
        }
        
        $ver = phpversion();
        $basic = base64_encode("{$userid}:{$passwd}");
        
        $req  = "GET {$path} HTTP/1.1\r\n";
        $req .= "Host: {$host}\r\n";
        $req .= "User-Agent: PHP/{$ver}\r\n";
        $req .= "Authorization: Basic {$basic}\r\n";
        $req .= "Connection: Close\r\n\r\n";
        
        // request streams of the public data flowing through Twitter.
        fwrite($fp, $req);
        
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
        
        fclose($fp);
    }
    
    private function clean($text) {
        $cleaned = preg_replace("(¥r¥n|¥r|¥n)", "", $text);
        $cleaned = preg_replace("/(#.* |#.*　|#.*)/", "", $cleaned);
        $cleaned = preg_replace("/( |　)*(QT|RT)( |　)*/", "", $cleaned);
        $cleaned = preg_replace("/( |　|.)*@[0-9a-zA-Z_]+(:)*(| |　)*(さん)*(の|が|を)*/", "", $cleaned);
        return trim($cleaned);
    }
}