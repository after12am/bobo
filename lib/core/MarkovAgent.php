<?
require_once('DB.php');
require_once('yahoo/MAService.php');
require_once('TinyHash.php');

class MarkovAgent {
    
    /**
     * Database object.
     * @var object
     */
    protected $db;
    
    /**
     * Yahoo MA service.
     * @var array
     */
    protected $ma;
    
    public function __construct() {
        
        $this->db = DB::getInstance();
        $this->ma = new MAService(Configure::read('yahoo.app_id'));
    }
    
    public function exist($id) {
        
        $query = sprintf(
            "SELECT id FROM tweet WHERE id = %s", 
            sqlite_escape_string($id)
        );
        
        return count($this->db->find($query)) > 0;
    }
    
    public function save($twitter) {
        
        if (!preg_match('/^([0-9]+)$/', $twitter['id_str'])) {
            return false;
        }
        
        if ($this->exist($twitter['id_str'])) {
            return false;
        }
        
        $this->db->beginTransaction();
        
        try {
            // save to Twitter table.
            $data = array(
                'id' => $twitter['id_str'],
                'screen_name' => $twitter['user']['screen_name'],
                'tweet' => $twitter['text'],
                'updated' => date("Y-m-d H:i:s")
            );
            
            $this->db->insert('tweet', $data);
            
            // save to Markov table.
            $backup = $this->backup($twitter['text']);
            $data = $this->ma->words($backup[0]);
            $rows = $this->fix($data);
            $rows = $this->restore($backup[1], $rows);
            
            foreach ($rows as $row) {
                $row['updated'] = date("Y-m-d H:i:s");
                $this->db->insert('markov', $row);
            }
            
            $this->db->commit();
            
        } catch (Exception $e) {
            echo $e->getTraceAsString();
            $this->db->rollback();
            return false;
        }
        
        return true;
    }
    
    public function text($hashtags = array()) {
        
        $query = "SELECT * FROM markov WHERE lex1='%s';";
        
        $hashtags = implode(' ', $hashtags);
        $rows = $this->db->find(sprintf($query, 'BOF'));
        
        // begin
        if (count($rows) === 0) {
            echo "we can't find tweet candidate.\n";
            echo "tweet request was canceled.\n";
            exit(0);
        }
        
        $add = $rows[mt_rand(0, count($rows) - 1)];
        $text = $add['lex2'];
        
        while ($add['lex3'] !== 'EOF') {
            
            $rows = $this->db->find(sprintf($query, sqlite_escape_string($add['lex3'])));
            
            if (count($rows) === 0) {
                break;
            }
            
            $add = $rows[mt_rand(0, count($rows) - 1)];
            $textLen = mb_strlen($text, 'UTF-8');
            $addLen = mb_strlen($add['lex1'] . $add['lex2'], 'UTF-8');
            $hashLen = mb_strlen($hashtags, 'UTF-8') + 1;
            
            if ($textLen + $addLen + $hashLen > 140) {
                break;
            }
            
            $text .= $add['lex1'].$add['lex2'];
        }
        
        return "$text $hashtags";
    }
    
    private function fix($ma) {
        
        $rows = array();
        
        for ($i = -1; $i < count($ma) - 1; $i++) {
            
            $r1 = (array)$ma[$i];
            $r2 = (array)$ma[$i + 1];
            $r3 = (array)$ma[$i + 2];
            
            $r1 = (array)$r1['surface'];
            $r2 = (array)$r2['surface'];
            $r3 = (array)$r3['surface'];
            
            if ($i === -1) {
                $r1 = array(BOF);
            }
            
            if ($i === (count($ma) - 2)) {
                $r3 = array(EOF);
            }
            
            $rows[] = array(
                'lex1' => $r1[0],
                'lex2' => $r2[0],
                'lex3' => $r3[0]
            );
        }
        
        return $rows;
    }
    
    private function backup($text) {
        
        // backup URL
        $pat = "/https?:\/\/[-_.!~*\'()a-zA-Z0-9;\/?:\@&=+\$,%#]+/";
        $replaced_text = $text;
        $backup = array();
        
        preg_match_all($pat, $replaced_text, $m);
        if (count($m)) {
            foreach ($m[0] as $k => $v) {
                $_v = str_replace('/', '\/', $v);
                $rep = sprintf("%010s", TinyHash::create($k));
                $replaced_text = preg_replace("/{$_v}/", $rep, $replaced_text);
                $backup[] = array(
                    'match' => $v,
                    'rep' => $rep
                );
            }
        }
        
        return array($replaced_text, $backup);
    }
    
    /*
        $backup = array(
            'match' => $m[0],   // match text
            'rep' => $rep       // replace text
        );
        
        $rows = array(
            array($r1['surface'], $r2['surface'], $r3['surface']),
            ...
        );
    */
    private function restore($backup = array(), $rows = array()) {
        
        // I wake up URL that was evacuated
        // if (!isset($backup['match'])) {
        //     return $rows;
        // }
        
        foreach ($rows as $i => $row) {
            foreach ($row as $j => $c) {
                foreach($backup as $b) {
                    if ($c === $b['rep']) {
                        $rows[$i][$j] = $b['match'];
                    }
                }
            }
        }
        
        return $rows;
    }
}