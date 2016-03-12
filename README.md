# PHP Last.fm API Wrapper

[![Build Status](https://travis-ci.org/chrismou/php-lastfm-wrapper.svg?branch=master)](https://travis-ci.org/chrismou/php-lastfm-wrapper)
[![Test Coverage](https://codeclimate.com/github/chrismou/php-lastfm-wrapper/badges/coverage.svg)](https://codeclimate.com/github/chrismou/php-lastfm-wrapper/coverage)
[![Code Climate](https://codeclimate.com/github/chrismou/php-lastfm-wrapper/badges/gpa.svg)](https://codeclimate.com/github/chrismou/php-lastfm-wrapper)

A dead simple wrapper class for the last.fm API. After using a couple of existing last.fm wrapper classes, it became 
obvious that most were either a) Incomplete, b) Seemingly abandoned, or c) Poorly unit tested. After the August 2015 last.fm
redesign, the library I'd settled with started throwing unexepected errors. At this point, I decided it might be simpler 
to just write my own.

This library aims to address these issues, providing a simple, centralised way of calling any method on the last.fm API, 
meaning it should support all future API additions out of the box. It also includes an full set of unit tests.


## Install

For [composer](http://getcomposer.org) based projects:

```
composer require chrismou/lastfm
```

## Usage

First you need an API key and secret from Last.FM.  You can obtain one by signing up here: [http://www.last.fm/api](http://www.last.fm/api)

To set up the last.fm API client:

```
$lastfm = new \Chrismou\LastFm\LastFm(
    new GuzzleHttp\Client(),
    YOUR_API_KEY,
    YOUR_API_SECRET
);
```

The format for calls is: `$lastfm->get($method, $parameters);`, where method is the last.fm api method name.

So, if you wanted to get info on an artist, you could run:

```
$lastfm->get('artist.getInfo', 'cher');
```

This makes a request and returns all info on the Artist 'Cher'.

Please refer to the [last.fm api documentation](http://www.last.fm/api) for a full list of available methods and required parameters.


## Tests

To run the unit test suite:

```
curl -s https://getcomposer.org/installer | php
php composer.phar install
./vendor/bin/phpunit
```

If you use docker, you can also run the test suite against all supported PHP versions:
```
./vendor/bin/dunit
```

## License

Released under the MIT License. See [LICENSE](LICENSE.md).

## Thanks to

[dandelionmood/php-lastfm](https://github.com/dandelionmood/php-lastfm), the original inspiration for the library.
