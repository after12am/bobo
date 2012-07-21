<?
require_once('db/Markov.php');
require_once('db/Tweet.php');
require_once('twitter/TwitterStream.php');
require_once('yahoo/MAService.php');

class BoobyBot extends TwitterStream {
    
    public function __construct($userid, $passwd, $consumer_key, $consumer_secret, $access_token, $access_token_secret) {
        parent::__construct($userid, $passwd, $consumer_key, $consumer_secret, $access_token, $access_token_secret);
    }
    
    public function post() {
        
        $tweet = '';
        $status = 'statuses/update';
        $hash = ' #bot';
        
        // begin
        $rows = Markov::find('BOF');
        
        if (count($rows) === 0) {
            echo "we can't find tweet candidate.\n";
            echo "because of this reason, tweet request was canceled.\n";
            return;
        }
        
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
            $suffixLen = mb_strlen($hash, 'UTF-8');
            
            if ($tweetLen + $addedLen + $suffixLen > 140) {
                break;
            }
            
            $tweet .= $ma['lex1'] . $ma['lex2'];
        }
        
        // add hash tag
        $tweet .= $hash;
        
		$ret = $this->twitter->post($status, array('status' => $tweet));
        
        // in case of error
        if ($ret->error) {
            echo $ret->error . "\n";
            exit(0);
        }
        
        echo "@dev_12am:" . $tweet . "\n";
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
    
    public function pickUp($ignore_ids = array(), $allow_langs = array()) {
        
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
            
            $id = $twitter['id_str'];
            $name = $twitter['user']['screen_name'];
            $cleaned = $this->clean($twitter['text']);

            $data = array(
                array(
                    'id' => $id,
                    'tweet' => $cleaned
                )
            );
            Tweet::save($data);

            $data = $this->analyse($cleaned);
            Markov::save($data);

            echo "@" . $name . ":" . $cleaned . "\n";
        }
    }
    
    private function analyse($text) {
        
        $shelter = $this->shelter($text);
        $maService = new MAService(YAHOO_APP_ID);
        $result = $maService->words($shelter['text']);
        $rows = array();
        
        for ($i = -1; $i < count($result) - 1; $i++) {
            
            $r1 = (array)$result[$i];
            $r2 = (array)$result[$i + 1];
            $r3 = (array)$result[$i + 2];
            
            if ($i === -1) {
                $r1 = array('surface' => BOF);
            }
            
            if ($i === (count($result) - 2)) {
                $r3 = array('surface' => EOF);
            }
            
            $rows[] = array(
                $r1['surface'],
                $r2['surface'],
                $r3['surface']
            );
        }
        
        if (isset($shelter['match'])) {
            foreach ($rows as $i => $row) {
                foreach ($row as $j => $c) {
                    if ($c === $shelter['rep']) {
                        $rows[$i][$j] = $shelter['match'];
                    }
                }
            }
        }
        
        return $rows;
    }
    
    private function shelter($text) {
        
        // shelter URL
        
        // pattern of URL
        $pat = "/https?:\/\/[-_.!~*\'()a-zA-Z0-9;\/?:\@&=+\$,%#]+/";
        $rep = "REPLACEDURL";
        
        $m = array();
        preg_match($pat, $text, $m);
        $text = preg_replace($pat, $rep, $text);
        
        $ret = array(
            'text' => $text,    // replaced text
            'match' => $m[0],   // match text
            'rep' => $rep,      // replace text
            'pat' => $pat       // pattern
        );
        
        return $ret;
    }
}