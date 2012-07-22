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
            
            if (!preg_match('/^[0-9]+$/', $d['id'])) return false;

            $db = DB::getInstance();
            $updated = @date("Y-m-d H:i:s");
            
            $query = sprintf(
                "INSERT INTO tweet (id, screen_name, tweet, updated) VALUES (%s, '%s', '%s', '%s');",
                $d['id'],
                $db->escapeString($d['screen_name']),
                $db->escapeString($d['tweet']),
                $db->escapeString($updated)
            );
            
            $db->query($query);
        }
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