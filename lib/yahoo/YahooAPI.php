<?
class YahooAPI {
    
    protected $appid;
    
    public function __construct($appid) {
        
        if (!$appid) {
            echo 'yahoo constant setup has not been completed.';
            exit(0);
        }
        
        $this->appid = appid;
    }
}
