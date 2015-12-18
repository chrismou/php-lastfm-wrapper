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

Docs to follow

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