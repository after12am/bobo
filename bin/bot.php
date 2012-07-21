<?
require_once('../lib/bootstrap.php');
require_once('bot/BoobyBot.php');
require_once('db/DB.php');

function help() {
    echo "\n  usage : php bot.php [option]\n";
    echo "\n  the below are available options.\n";
    echo "\n";
    echo "  setup   setup database.\n";
    echo "  post    tweet using a second-order markov chain.\n";
    echo "  pick    pick up recent tweets was filtered by language with jp.\n";
    echo "  help    \n";
    echo "\n";
}

function setup() {
    DB::setup();
}

function post() {
    $bot = new BoobyBot(
        TWITTER_USERID, 
        TWITTER_PASSWD, 
        TWITTER_CONSUMER_KEY, 
        TWITTER_CONSUMER_SECRET, 
        TWITTER_ACCESS_TOKEN, 
        TWITTER_ACCESS_TOKEN_SECRET
    );
    $bot->post();
}

function pick_up() {
    $bot = new BoobyBot(
        TWITTER_USERID, 
        TWITTER_PASSWD, 
        TWITTER_CONSUMER_KEY, 
        TWITTER_CONSUMER_SECRET, 
        TWITTER_ACCESS_TOKEN, 
        TWITTER_ACCESS_TOKEN_SECRET
    );
    $bot->pickUp(array(), array('ja'));
}

function main($argv) {
    
    switch ($argv[1]) {
        
        case 'setup':
            setup();
            break;
            
        case 'post':
            post();
            break;
            
        case 'pick':
            pick_up();
            break;
            
        case 'h':
        case 'help':
        default:
            help();
            break;
    }
}

if (strcmp(basename($argv[0]), basename(__FILE__)) === 0) main($argv);