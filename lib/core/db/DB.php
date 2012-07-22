<?
class DB {
    
    static protected $instance = NULL;
    
    public function __construct() {
        
        if (file_exists(PATH_TO_JOURNAL)) {
            unlink(PATH_TO_JOURNAL);
        }
    }
    
    public static function getInstance() {
        
        if (self::$instance === NULL) {
            self::$instance = new PDO('sqlite:markov.db');
            
            if (!self::$instance) {
                die('database connection failed.');
            }
        }
        
        return self::$instance;
    }
    
    public static function setup() {
        
        // force to setup database.
        if (file_exists(PATH_TO_DB)) {
            unlink(PATH_TO_DB);
        }
        
        $db = DB::getInstance();
        
        $ret = $db->exec(file_get_contents(PATH_TO_SQL));
        
        if ($ret === false) {
            echo 'database setup failed.';
        } else {
            echo 'database setup succeeded.';
        }
    }
}