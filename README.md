BoobyTrap
=========

This is a bot tweets an artificially created message using second order markov chain method.

## Demo

<a href="https://twitter.com/dev_12am">@dev_12am</a>

## Prepare

BoobyTrap requires some settings. Open `lib/constant.php` and fill the below blank.

    Configure::write('twitter.user_id', '');
    Configure::write('twitter.password', '');
    Configure::write('twitter.consumer_key', '');
    Configure::write('twitter.consumer_secret', '');
    Configure::write('twitter.access_token', '');
    Configure::write('twitter.access_token_secret', '');
    Configure::write('yahoo.app_id', '');

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