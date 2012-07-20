<?
require_once('../lib/bootstrap.php');
require_once('bot/BoobyBot.php');

function h() {
    
    echo "\n  usage : php bot.php [option]\n";
    echo "\n  the below are available options.\n";
    echo "\n";
    echo "  setup   setup database.\n";
    echo "  post    tweet using a second-order markov chain.\n";
    echo "  pick    pick up recent tweets was filtered by language with jp.\n";
    echo "\n";
}

function main($argv) {
    
    switch ($argv[1]) {
        
        case 'setup':
            try {
                // force to setup database.
                @unlink(PATH_TO_DB);
                
                // setup database
                $db = new SQLite3(PATH_TO_DB);
                
                if ($db->exec(file_get_contents(PATH_TO_SQL))) {
                    echo 'database setup is succeeded.';
                } else {
                    echo 'database setup is failed.';
                }
                
                $db->close();
                
            } catch (Exception $e) {
                echo $e->getTraceAsString();
                exit(0);
            }
            break;
            
        case 'post':
            $bot = new BoobyBot(
                TWITTER_USERID, 
                TWITTER_PASSWD, 
                TWITTER_CONSUMER_KEY, 
                TWITTER_CONSUMER_SECRET, 
                TWITTER_ACCESS_TOKEN, 
                TWITTER_ACCESS_TOKEN_SECRET
            );
            $bot->post();
            break;
            
        case 'pick':
            $bot = new BoobyBot(
                TWITTER_USERID, 
                TWITTER_PASSWD, 
                TWITTER_CONSUMER_KEY, 
                TWITTER_CONSUMER_SECRET, 
                TWITTER_ACCESS_TOKEN, 
                TWITTER_ACCESS_TOKEN_SECRET
            );
            $bot->pick(array(), array('ja'));
            break;
            
        case 'h':
        default:
            h();
            break;
    }
}


if (strcmp(basename($argv[0]), basename(__FILE__)) === 0) {
    main($argv);
}