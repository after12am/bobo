<?
require_once("DB.php");
require_once('yahoo/MAService.php');

class Markov {
    
    public static function save($tweet) {
        
        $rows = Markov::analyse($tweet);
        
        foreach ($rows as $row) {
            
            if ($count = self::count($lex1, $lex2, $lex3)) {
                self::update($count + 1);
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
        
        return $ret->fetchArray();
    }
    
    private static function analyse($tweet) {
        
        $p = "/https?:\/\/[-_.!~*\'()a-zA-Z0-9;\/?:\@&=+\$,%#]+/";
        $m = array();
        preg_match($p, $tweet, $m);
        $tweet = preg_replace($p, "REPLACEDURL", $tweet);
		
        $ret = self::parse($tweet);
        $ret = $ret->ma_result->word_list->word;
        
        $rows = array();
        
        for ($i = 0; $i < count($ret) - 1; $i++) {
            
            $r1 = (array)$ret[$i];
            $r2 = (array)$ret[$i + 1];
            $r3 = (array)$ret[$i + 2];
            
            if ($m) {
                if ($r1['surface'] === 'REPLACEDURL') {
                    $r1['surface'] = $m[0];
                }
                
                if ($r2['surface'] === 'REPLACEDURL') {
                    $r2['surface'] = $m[0];
                }
                
                if ($r3['surface'] === 'REPLACEDURL') {
                    $r3['surface'] = $m[0];
                }
            }
            
            $rows[] = array(
                $r1['surface'],
                $r2['surface'],
                isset($r3['surface']) ? $r3['surface'] : EOF
            );
        }
        
        return $rows;
    }
    
    private static function parse($tweet) {
        
        $maService = new MAService(YAHOO_APP_ID);
        $result = $maService->parse($tweet);
        return $result;
    }
}