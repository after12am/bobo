<?
require_once('../lib/bootstrap.php');
require_once('bot/BoobyBot.php');

$userid = TWITTER_USERID;
$passwd = TWITTER_PASSWD;
$consumer_key = TWITTER_CONSUMER_KEY;
$consumer_secret = TWITTER_CONSUMER_SECRET;
$access_token = TWITTER_ACCESS_TOKEN;
$access_token_secret = TWITTER_ACCESS_TOKEN_SECRET;
$appid = YAHOO_APP_ID;

if (in_array('post', $argv)) {
    $bot = new BoobyBot($consumer_key, $consumer_secret, $access_token, $access_token_secret);
    $bot->post();
    exit(1);
    
} else if (in_array('gather', $argv)) {
    $bot = new BoobyBot($userid, $passwd, $consumer_key, $consumer_secret, $access_token, $access_token_secret);
    $bot->gather();
    exit(1);
    
} else {
    echo "Usage: $argv[0] [post] [collect]\n";
    echo "  post        Post using markov chain.\n";
    echo "  gather -id -pass      Gather recently tweets.\n";
}
