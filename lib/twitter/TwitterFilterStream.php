<?
require_once('TwitterStream.php');

class TwitterFilterStream extends TwitterStream {
    
    const PATH = "/1/statuses/filter.json";
    
    protected function connect() {
        
        // write request to connect to twitter.
    }
}