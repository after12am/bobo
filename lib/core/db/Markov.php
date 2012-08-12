<?
require_once("DB.php");

class Markov {
    
    /*
        $rows = array(
            array(
                $tweet_id
                $lex1,
                $lex2,
                $lex3
            ),
            ...
        );
    */
    public static function save($data) {
        
        $updated = date('Y-m-d H:i:s');
        
        if (1) {
            self::insert($data['lex1'], $data['lex2'], $data['lex3'], $updated);
        }
    }
    
    private static function insert($lex1, $lex2, $lex3, $updated) {
        
        $db = DB::getInstance();
        
        $query = sprintf(
            "INSERT INTO markov (lex1, lex2, lex3, updated) VALUES ('%s', '%s', '%s', '%s');",
            sqlite_escape_string($lex1),
            sqlite_escape_string($lex2),
            sqlite_escape_string($lex3),
            $updated
        );
        
        return $db->exec($query);
    }
    
    public static function find($lex1, $lex2 = NULL, $lex3 = NULL) {
        
        $db = DB::getInstance();
        
        $query = sprintf(
            "SELECT * FROM markov WHERE lex1='%s'",
            sqlite_escape_string($lex1)
        );
        
        if ($lex2) {
            $query .= sprintf(
                " AND lex2='%s'",
                sqlite_escape_string($lex2)
            );
        }
        
        if ($lex3) {
            $query .= sprintf(
                " AND lex3='%s'",
                sqlite_escape_string($lex3)
            );
        }
        
        $query .= ";";
        
        if (($ret = $db->query($query)) === false) {
            throw new Exception('query failed.');
        }
        
        return $ret->fetchAll(PDO::FETCH_ASSOC);
    }
    
    private static function exist($id, $lex1, $lex2, $lex3) {
        
        $db = DB::getInstance();
        
        $query = sprintf(
            "SELECT * FROM markov WHERE lex1='%s' AND lex2='%s' AND lex3='%s';",
            sqlite_escape_string($lex1),
            sqlite_escape_string($lex2),
            sqlite_escape_string($lex3)
        );
        
        if (($ret = $db->query($query)) === false) {
            throw new Exception('query failed.');
        }
        
        return $ret->fetchAll(PDO::FETCH_ASSOC);
    }
}