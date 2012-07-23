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
    public static function save($rows) {
        
        foreach ($rows as $k => $row) {
            
            if (!self::exist($row['id'], $row['lex1'], $row['lex2'], $row['lex3'])) {
                self::insert($row['id'], $row['lex1'], $row['lex2'], $row['lex3']);
            } else {
                
            }
        }
    }
    
    private static function insert($id, $lex1, $lex2, $lex3) {
        
        if (!preg_match('/^[0-9]+$/', $id)) {
            return false;
        }
        
        $db = DB::getInstance();
        
        $query = sprintf(
            "INSERT INTO markov (tweet_id, lex1, lex2, lex3) VALUES (%s, '%s', '%s', '%s');",
            $id, 
            sqlite_escape_string($lex1),
            sqlite_escape_string($lex2),
            sqlite_escape_string($lex3)
        );
        
        return $db->exec($query);
    }
    
    private static function update() {
        
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
            "SELECT * FROM markov WHERE id=%s AND lex1='%s' AND lex2='%s' AND lex3='%s';",
            $id,
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