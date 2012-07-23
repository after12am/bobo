<?
require_once("DB.php");

class Markov {
    
    /*
        $rows = array(
            array(
                $lex1,
                $lex2,
                $lex3
            ),
            ...
        );
    */
    public static function save($rows) {
        
        foreach ($rows as $k => $row) {
            
            $ret = self::find($row[0], $row[1], $row[2]);
            $cnt = count($ret);
            
            if ($cnt > 0) {
                // update count
                self::update($row[0], $row[1], $row[2], $cnt + 1);
            } else {
                self::insert($row[0], $row[1], $row[2]);
            }
        }
    }
    
    private static function insert($lex1, $lex2, $lex3) {
        
        $db = DB::getInstance();
        $updated = date("Y-m-d H:i:s");
        
        $query = sprintf(
            "INSERT INTO markov (lex1, lex2, lex3, updated) VALUES ('%s', '%s', '%s', '%s');",
            sqlite_escape_string($lex1),
            sqlite_escape_string($lex2),
            sqlite_escape_string($lex3),
            sqlite_escape_string($updated)
        );
        
        return $db->exec($query);
    }
    
    private static function update($lex1, $lex2, $lex3, $cnt) {
        
        $db = DB::getInstance();
        $updated = @date("Y-m-d H:i:s");
        
        $query = sprintf(
            "UPDATE markov set cnt=%s, updated='%s' WHERE lex1='%s' AND lex2='%s' AND lex3='%s';",
            $cnt,
            sqlite_escape_string($updated),
            sqlite_escape_string($lex1),
            sqlite_escape_string($lex2),
            sqlite_escape_string($lex3)
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
    
    public static function exist($lex1, $lex2 = NULL, $lex3 = NULL) {
        
        return (count(self::find($lex1, $lex2, $lex3)) > 0);
    }
}