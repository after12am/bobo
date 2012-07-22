<?
require_once('DB.php');
require_once('Markov.php');
require_once('yahoo/MAService.php');

class MarkovAgent {
    
    protected $maService;
    
    public function __construct() {
        
        $this->maService = new MAService(YAHOO_APP_ID);
    }
    
    public function set($text) {
        
        $backup = $this->backup($text);
        $data = $this->maService->words($backup['text']);
        $rows = $this->fix($data);
        $rows = $this->restore($backup, $rows);
        
        // save to database
        Markov::save($rows);
    }
    
    public function get($sign = '') {
        
        $text = '';
        
        // begin
        $rows = Markov::find('BOF');
        
        if (count($rows) === 0) {
            echo "we can't find tweet candidate.\n";
            echo "because of this reason, tweet request was canceled.\n";
            exit(0);
        }
        
        $add = $rows[mt_rand(0, count($rows) - 1)];
        $text .= $add['lex2'];
        
        while ($add['lex3'] !== 'EOF') {
            
            $rows = Markov::find($add['lex3']);
            
            // I don't understand why I can't get target rows.
            if (count($rows) === 0) {
                break;
            }
            
            $add = $rows[mt_rand(0, count($rows) - 1)];
            
            $textLen = mb_strlen($text, 'UTF-8');
            $addLen = mb_strlen($add['lex1'] . $add['lex2'], 'UTF-8');
            $signLen = mb_strlen($sign, 'UTF-8') + 1;
            
            if ($textLen + $addLen + $signLen > 140) {
                break;
            }
            
            $text .= $add['lex1'] . $add['lex2'];
        }
        
        return "$text $sign";
    }
    
    private function fix($ma) {
        
        // fix ma data format to markov model data
        
        $rows = array();
        
        for ($i = -1; $i < count($ma) - 1; $i++) {
            
            $r1 = (array)$ma[$i];
            $r2 = (array)$ma[$i + 1];
            $r3 = (array)$ma[$i + 2];
            
            if ($i === -1) {
                $r1 = array('surface' => BOF);
            }
            
            if ($i === (count($ma) - 2)) {
                $r3 = array('surface' => EOF);
            }
            
            $rows[] = array(
                $r1['surface'],
                $r2['surface'],
                $r3['surface']
            );
        }
        
        return $rows;
    }
    
    private function backup($text) {
        
        // backup URL
        $pat = "/https?:\/\/[-_.!~*\'()a-zA-Z0-9;\/?:\@&=+\$,%#]+/";
        $rep = "REPLACEDURL";
        
        $match = array();
        preg_match($pat, $text, $match);
        $text = preg_replace($pat, $rep, $text);
        
        $backup = array(
            'text' => $text,        // replaced text
            'match' => $match[0],   // match text
            'rep' => $rep,          // replace text
            'pat' => $pat           // pattern
        );
        
        return $backup;
    }
    
    /*
        $backup = array(
            'text' => $text,        // replaced text
            'match' => $match[0],   // match text
            'rep' => $rep,          // replace text
            'pat' => $pat           // pattern
        );
        
        $rows = array(
            array(
                $r1['surface'],
                $r2['surface'],
                $r3['surface']
            ),
            ...
        );
    */
    private function restore($backup = array(), $rows = array()) {
        
        // I wake up URL that was evacuated
        if (!isset($backup['match'])) {
            return $rows;
        }
        
        foreach ($rows as $i => $row) {
            foreach ($row as $j => $c) {
                if ($c === $backup['rep']) {
                    $rows[$i][$j] = $backup['match'];
                }
            }
        }
        
        return $rows;
    }
}