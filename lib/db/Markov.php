<?
require_once("DB.php");
require_once('yahoo/MAService.php');

class Markov {
    
    public static function save($text) {
        
        $rows = Markov::analyse($text);
        
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
    
    private static function update($lex1, $lex2, $lex3, $count) {
        
        $db = DB::getInstance();
        
        $query = sprintf(
            "UPDATE markov set count=%s WHERE lex1='%s' AND lex2='%s' AND lex3='%s';",
            $count,
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
    
    private static function count($lex1, $lex2, $lex3) {
        
        $db = DB::getInstance();
        
        $query = sprintf(
            "SELECT count FROM markov WHERE lex1='%s' AND lex2='%s' AND lex3='%s';",
            $db->escapeString($lex1),
            $db->escapeString($lex2),
            $db->escapeString($lex3)
        );
        
        if (($ret = $db->query($query)) === false) {
            return false;
        }
        
        $ret = $ret->fetchArray();
        
        return $ret['count'];
    }
    
    private static function analyse($text) {
        
        $shelter = self::shelter($text);
        $r = self::parse($shelter['text']);
        $rows = array();
        
        for ($i = -1; $i < count($r) - 1; $i++) {
            
            $r1 = (array)$r[$i];
            $r2 = (array)$r[$i + 1];
            $r3 = (array)$r[$i + 2];
            
            if ($i === -1) {
                $r1 = array('surface' => BOF);
            }
            
            if ($i === (count($r) - 2)) {
                $r3 = array('surface' => EOF);
            }
            
            $rows[] = array(
                $r1['surface'],
                $r2['surface'],
                $r3['surface']
            );
        }
        
        if (isset($shelter['match'])) {
            foreach ($rows as $i => $row) {
                foreach ($row as $j => $c) {
                    if ($c === $shelter['rep']) {
                        $rows[$i][$j] = $shelter['match'];
                    }
                }
            }
        }
        
        return $rows;
    }
    
    private static function parse($text) {
        
        $maService = new MAService(YAHOO_APP_ID);
        $ret = $maService->parse($text);
        $ret = $ret->ma_result->word_list->word;
        
        return $ret;
    }
    
    private static function shelter($text) {
        
        // shelter email address
        
        $pat = "/https?:\/\/[-_.!~*\'()a-zA-Z0-9;\/?:\@&=+\$,%#]+/";
        $rep = "REPLACEDURL";
        
        $m = array();
        preg_match($pat, $text, $m);
        $text = preg_replace($pat, $rep, $text);
        
        $ret = array(
            'text' => $text,    // replaced text
            'match' => $m[0],   // match text
            'rep' => $rep,      // replace text
            'pat' => $pat       // pattern
        );
        
        return $ret;
    }
}