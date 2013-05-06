<?

// Applied to the Markov chain with a morphological analysis.

require_once('Configure.php');
require_once('twitter/TwitterSampleStream.php');
require_once('MarkovAgent.php');

class Pick extends TwitterSampleStream {
    
    /**
     * Number of picks up.
     * @param int
     */
    protected $num;
    
    /**
     * Allow languages.
     * @param array
     */
    protected $langs;
    
    public function __construct($num, $langs = array('ja')) {
        
        $this->num = $num;
        $this->langs = $langs;
        
        parent::__construct(
            Configure::read('twitter.user_id'),
            Configure::read('twitter.password'),
            Configure::read('twitter.consumer_key'),
            Configure::read('twitter.consumer_secret'),
            Configure::read('twitter.access_token'),
            Configure::read('twitter.access_token_secret')
        );
    }
    
    protected function clean($text) {
        
        $text = preg_replace("(¥r¥n|¥r|¥n)", "", $text);
        $text = preg_replace("/(#.* |#.*　|#.*)/", "", $text);
        $text = preg_replace("/( |　)*(QT|RT)( |　)*/", "", $text);
        $text = preg_replace("/( |　|.)*@[0-9a-zA-Z_]+(:)*(| |　)*(さん)*(の|が|を)*/", "", $text);
        return trim($text);
    }
    
    protected function save($res) {
        
        if (in_array($res['user']['lang'], $this->langs)) {
            if ($res['text'] = $this->clean($res['text'])) {
                $agent = new MarkovAgent();
                $agent->save($res);
                echo "@{$res['user']['screen_name']}:{$res['text']}\n";
                return true;
            }
        }
        return false;
    }
    
    public function execute() {
        
        $i = 0;
        
        if ($this->open()) {
            
            while($res = fgets($this->fp)) {
                
                if (strpos($res, 'Unauthorized') > 0) {
                    print "unauthorized access.\n";
                    print "please confirm your twitter account.\n";
                    break;
                }
                
                if ($i >= $this->num) break;
                if ($res = json_decode($res, true)) {
                    if ($this->save($res)) {
                        $i++;
                    }
                }
            }
        }
    }
}

// pick up num
if (preg_match('/^([0-9]{1,})$/', $argv[2])) {
    $n = $argv[2];
} else {
    $n = 100;
}

$command = new Pick($n);
$command->execute();