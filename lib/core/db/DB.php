<?
class DB extends PDO {
    
    protected static $instance = NULL;
    
    public function __construct() {
        
        parent::__construct('sqlite:markov.db');
    }
    
    public static function getInstance() {
        
        if (self::$instance === NULL) {
            //self::$instance = new PDO('sqlite:markov.db');
            self::$instance = new DB();
        }
        
        if (!self::$instance) {
            die('database connection failed.');
        }
        
        return self::$instance;
    }
    
    public function find($query) {
        
        $statement = parent::query($query);
        
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function insert($table, $data) {
        
        $escape = function ($value) {
            return sqlite_escape_string($value);
        };
        
        $keys = array_keys($data);
        $values = array_values($data);
        
        $query = sprintf(
            "INSERT INTO $table (`%s`) VALUES ('%s');",
            implode("`,`", $keys),
            implode("','", array_map($escape, $values))
        );
        
        return $this->exec($query);
    }
}