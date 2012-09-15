<?
require_once('TwitterStream.php');

class TwitterFilterStream extends TwitterStream {
    
    protected function connect() {
        
        // write request to connect to twitter.
        // path => "/1/statuses/filter.json"
    }
}