<?

class DB extends SQLite3 {
    
    static protected $instance = NULL;
    
    public function __construct() {
        
        $this->open('markov.db');
    }
    
    public function __destruct() {
        
        $this->close();
    }
    
    static function getInstance() {
        
        if (self::$instance === NULL) {
            self::$instance = new DB();
        }
        return self::$instance;
    }
}