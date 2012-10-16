BoobyTrap
=========

BoobyTrap is a bot tweets an artificially created message using second order markov chain method. By using markov chain, BoobyTrap tweets as if human tweets. Markov chain is available in case of having relation each other. Especially text. Let's play on words.

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

BoobyTrap needs numerous public tweets for constructing a tweet. So, we have to setup database at first. run the following command in a new terminal.

    cd /path/to/bin
    ./bot setup

And then run the following commands for gathering tweets. If you want to set limit, set `pick up num` after `./bot pick`.

    ./bot pick [pick up num]

At last, run the following commands for tweet.

    ./bot post

## Notes

* You can't tweet without picking up timeline. run `./bot pick` before run `./bot post`.