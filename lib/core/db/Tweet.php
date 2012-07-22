<?
require_once("DB.php");

class Tweet {
    
    /*
        $rows = array(
            array(
                'id' : $id,
                'tweet' : $tweet
            ),
            ...
        );
    */
    public static function save($rows) {
        
        foreach ($rows as $d) {
            
            if (1) {
                self::insert($d['id'], $d['screen_name'], $d['tweet']);
            } else {
                
            }
        }
    }
    
    private static function insert($id, $screen_name, $tweet) {
        
        if (!preg_match('/^[0-9]+$/', $id)) {
            return false;
        }
        
        $db = DB::getInstance();
        $updated = @date("Y-m-d H:i:s");
        
        $query = sprintf(
            "INSERT INTO tweet (id, screen_name, tweet, updated) VALUES (%s, '%s', '%s', '%s');",
            $id,
            sqlite_escape_string($screen_name),
            sqlite_escape_string($tweet),
            sqlite_escape_string($updated)
        );
        
        return $db->exec($query);
    }
    
    private static function update() {
        
    }
    
    public static function exist($id) {
        
        if (!preg_match('/^[0-9]+$/', $id)) return false;
        
        $db = DB::getInstance();
        
        $query = sprintf(
            "SELECT id FROM tweet WHERE id = %s",
            $id
        );
        
        if (($ret = $db->query($query)) === false) {
            throw new Exception('query failed.');
        }
        
        return (count($ret->fetchAll(PDO::FETCH_ASSOC)) > 0);
    }
}