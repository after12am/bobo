bobo
====

bobo is a bot tweets an artificially created message using second order 
<a href="http://en.wikipedia.org/wiki/Markov_chain">markov chain method</a>. 
This method is a mathematical system that undergoes transitions from one state to another, 
between a finite or countable number of possible states. This method is available in case of having relationships 
each other and is particularly effective for text. By using this, bot tweets as if human tweets. 
This personification system is really absorbing, but is still little stupid. If interested, let's play on words.

## Demo

<a href="https://twitter.com/dev_12am">@dev_12am</a>

## Prepare

bobo requires some settings. Open `lib/constant.php` and fill the below blank.

    Configure::write('twitter.user_id', '');
    Configure::write('twitter.password', '');
    Configure::write('twitter.consumer_key', '');
    Configure::write('twitter.consumer_secret', '');
    Configure::write('twitter.access_token', '');
    Configure::write('twitter.access_token_secret', '');
    Configure::write('yahoo.app_id', '');

## Usage

bobo needs numerous public tweets for constructing a tweet. So, we have to setup database at first. The command is:

    cd /path/to/bin
    ./bot setup

pick up tweet command is: 

    ./bot pick [num]

tweet command is:

    ./bot tweet

## Notes

* You can't tweet without picking up timeline. run `./bot pick` before run `./bot post`.
* If you want to set limit on `pick`, set `pick up num` after `./bot pick`.