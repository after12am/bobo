<?
class DB extends SQLite3 {
    
    static protected $instance = NULL;
    
    public function __construct() {
        
        $this->open(PATH_TO_DB);
    }
    
    public function __destruct() {
        
        $this->close();
    }
    
    public static function getInstance() {
        
        if (self::$instance === NULL) {
            self::$instance = new DB();
        }
        return self::$instance;
    }
    
    public static function setup() {
        
        try {
            // force to setup database.
            if (file_exists(PATH_TO_DB)) {
                unlink(PATH_TO_DB);
            }
            
            // setup database
            $db = DB::getInstance();
            
            if ($db->exec(file_get_contents(PATH_TO_SQL))) {
                echo 'database setup is succeeded.';
            } else {
                echo 'database setup is failed.';
            }
            
            $db->close();
            
        } catch (Exception $e) {
            echo $e->getTraceAsString();
            exit(0);
        }
    }
}