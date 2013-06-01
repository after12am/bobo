bobo
====

bobo is a bot tweets an artificially created message using second order 
<a href="http://en.wikipedia.org/wiki/Markov_chain">markov chain method</a>. 
This method is a mathematical system that undergoes transitions from one state to another, 
between a finite or countable number of possible states. This method is available and effective in case of having relationships each other. 
Specifically, bobo will comprehensively enumerate word sequence which could be 
generated from the input sentence, and give the score corresponding to the probability 
of occurrence of the two words with connected. Finally, bobo will espouse a linear sequence of words 
which has highest score in the entire statement. Therefore bot tweets as if human tweets. 
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

bobo needs numerous public tweets for constructing an artificially message. So, we have to setup database at first.

    cd /path/to/bin
    ./bot setup

command for pick up tweets is: 

    ./bot pick [num]

command for tweet is:

    ./bot post

## Notes

* You can't post message without picking up timeline. run `./bot pick` before run `./bot post`.
* If you want to set limit on `pick` command, set `num` as option after `./bot pick`.
