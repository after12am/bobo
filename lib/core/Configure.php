<?

class Configure {
    
    protected static $instance = NULL;
    protected $defines = array();
    
    protected static function getInstance() {
        
        if (self::$instance === NULL) {
            self::$instance = new Configure();
        }
        return self::$instance;
    }
    
    public static function write($key, $value) {
        
        $instance = self::getInstance();
        $instance->defines[$key] = $value;
    }
    
    public static function read($key) {
        
        $instance = self::getInstance();
        
        if (isset($instance->defines[$key])) {
            return $instance->defines[$key];
        }
        return NULL;
    }
}