<?
class DB extends PDO {
    
    protected static $instance = NULL;
    
    public function __construct() {
        
        parent::__construct('sqlite:markov.db');
    }
    
    public static function getInstance() {
        
        if (self::$instance === NULL) {
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
        
        $escape = function ($v) {
            return sqlite_escape_string($v);
        };
        
        $query = sprintf(
            "INSERT INTO $table (`%s`) VALUES ('%s');",
            implode("`,`", array_keys($data)),
            implode("','", array_map($escape, array_values($data)))
        );
        
        return $this->exec($query);
    }
}