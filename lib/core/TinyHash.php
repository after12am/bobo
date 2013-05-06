<?

class TinyHash {
    
    public static function create($id, $base = 6, $shuffle = true) {
        $shuffleTable = array(0,1,2,3,4,5,6,7,8,9);
        $asciiTable = array(48,49,50,51,52,53,54,55,56,57);
        $hashTable = array();
        $i = 0;
        
        if ($shuffle) {
            shuffle($shuffleTable);
        }
        
        do {
            $hashTable[$i] = chr($asciiTable[$shuffleTable[(int)(floor($id / pow(62, $i)) + $i) % 62]]);
            $i = count($hashTable);
        } while(($base > $i) || (pow(62, $i) <= $id));

        return implode("", $hashTable);
    }
}

//print TinyHash::create(1);