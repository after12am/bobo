<?
require_once("DB.php");

class Markov {
    
    /*
        $rows = array(
            array(
                'lex1' : $lex1,
                'lex2' : $lex2,
                'lex3' : $lex3
            ),
            ...
        );
    */
    public static function save($rows) {
        
        foreach ($rows as $k => $row) {
            
            $rows = self::find($lex1, $lex2, $lex3);
            $cnt = $rows ? count($rows) : 0;
            
            if ($cnt) {
                self::update($row[0], $row[1], $row[2], $cnt + 1);
            } else {
                self::insert($row[0], $row[1], $row[2]);
            }
        }
    }
    
    private static function insert($lex1, $lex2, $lex3) {
        
        $db = DB::getInstance();
        
        $query = sprintf(
            "INSERT INTO markov (lex1, lex2, lex3) VALUES ('%s', '%s', '%s');",
            $db->escapeString($lex1),
            $db->escapeString($lex2),
            $db->escapeString($lex3)
        );
        
        return $db->query($query);
    }
    
    private static function update($lex1, $lex2, $lex3, $cnt) {
        
        $db = DB::getInstance();
        
        $query = sprintf(
            "UPDATE markov set cnt=%s WHERE lex1='%s' AND lex2='%s' AND lex3='%s';",
            $cnt,
            $db->escapeString($lex1),
            $db->escapeString($lex2),
            $db->escapeString($lex3)
        );
        
        return $db->query($query);
    }
    
    public static function find($lex1, $lex2 = NULL, $lex3 = NULL) {
        
        $db = DB::getInstance();
        
        $query = sprintf(
            "SELECT * FROM markov WHERE lex1='%s'",
            $db->escapeString($lex1)
        );
        
        if ($lex2) {
            $query .= sprintf(
                " AND lex2='%s'",
                $db->escapeString($lex2)
            );
        }
        
        if ($lex3) {
            $query .= sprintf(
                " AND lex3='%s'",
                $db->escapeString($lex3)
            );
        }
        
        $query .= ";";
        
        if (($ret = $db->query($query)) === false) {
            return false;
        }
        
        $rows = array();
        
        while ($row = $ret->fetchArray()) {
            $rows[] = $row;
        }
        
        return $rows;
    }
    
    // private static function cnt($lex1, $lex2, $lex3) {
    //     
    //     $db = DB::getInstance();
    //     
    //     $query = sprintf(
    //         "SELECT cnt FROM markov WHERE lex1='%s' AND lex2='%s' AND lex3='%s';",
    //         $db->escapeString($lex1),
    //         $db->escapeString($lex2),
    //         $db->escapeString($lex3)
    //     );
    //     
    //     if (($ret = $db->query($query)) === false) {
    //         return false;
    //     }
    //     
    //     $ret = $ret->fetchArray();
    //     
    //     return $ret['cnt'];
    // }
}