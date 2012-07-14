<?
require_once("DB.php");

class Tweet {
    
    public static function save($id, $tweet) {
        
        if (!preg_match('/^[0-9]+$/', $id)) return false;
        
        $db = DB::getInstance();
        
        $query = sprintf(
            "INSERT INTO tweet (id, tweet) VALUES (%s, '%s');",
            $id,
            $db->escapeString($tweet)
        );
        
        return $db->query($query);
    }
    
    public static function isExist($id) {
        
        if (!preg_match('/^[0-9]+$/', $id)) return false;
        
        $db = DB::getInstance();
        
        $query = sprintf(
            "SELECT id FROM tweet WHERE id = %s",
            $id
        );
        
        return $db->query($query)->fetchArray();
    }
}