<?
require_once('TwitterStream.php');

class TwitterFilterStream extends TwitterStream {
    
    protected $path = "/1/statuses/filter.json";
    
    protected function connect() {
        
        // write request to connect to twitter.
    }
}