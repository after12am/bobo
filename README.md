BoobyTrap
=========

## Demo

<a href="https://twitter.com/dev_12am">@dev_12am</a>

## Prepare

BoobyTrap requires some settings. Open `lib/constant.php` and fill the below blank.

    define('TWITTER_USERID', '');
    define('TWITTER_PASSWD', '');
    define('TWITTER_CONSUMER_KEY', '');
    define('TWITTER_CONSUMER_SECRET', '');
    define('TWITTER_ACCESS_TOKEN', '');
    define('TWITTER_ACCESS_TOKEN_SECRET', '');
    define('YAHOO_APP_ID', '');

## Usage

A bot tweets a trap message that is artificially created using a second-order Markov chain. To setup BoobyBot, run the following commands in a new terminal.

    cd /path/to/bin
    ./bot setup

To gather tweets, run the following commands. If you want to set limit, set `pick up num` after `./bot pick`.

    ./bot pick [pick up num]

To tweet, run the following commands.

    ./bot post

## Notes

* You can't tweet without picking up timeline. run `./bot pick` before run `./bot post`.