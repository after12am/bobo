<?

// Applied to the Markov chain with a morphological analysis.

require_once('Configure.php');
require_once('twitter/TwitterSampleStream.php');
require_once('MarkovAgent.php');

class Pick extends TwitterSampleStream {
    
    /**
     * Markov agent.
     * @param object
     */
    protected $agent;
    
    /**
     * Number of picks up.
     * @param int
     */
    protected $max;
    
    /**
     * Allow languages.
     * @param array
     */
    protected $langs = array('ja');
    
    public function __construct($num) {
        
        $this->agent = new MarkovAgent();
        $this->max = $num;
        
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
                
                $this->agent->save($res);
                
                echo "@{$res['user']['screen_name']}:{$res['text']}\n";
                
                return true;
            }
        }
        
        return false;
    }
    
    public function execute() {
        
        $this->open();
        
        if ($this->fp) {
            
            $i = 0;
            
            while($res = fgets($this->fp)) {
                
                if ($i >= $this->max) {
                    break;
                }
                
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