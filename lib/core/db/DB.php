<?
class DB {
    
    protected static $instance = NULL;
    
    public static function getInstance() {
        
        if (self::$instance === NULL) {
            self::$instance = new PDO('sqlite:markov.db');
        }
        
        if (!self::$instance) {
            die('database connection failed.');
        }
        
        return self::$instance;
    }
}