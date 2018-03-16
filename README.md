# MeTools

[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.txt)
[![Build Status](https://travis-ci.org/mirko-pagliai/me-tools.svg?branch=master)](https://travis-ci.org/mirko-pagliai/me-tools)
[![Coverage Status](https://img.shields.io/codecov/c/github/mirko-pagliai/me-tools.svg?style=flat-square)](https://codecov.io/github/mirko-pagliai/me-tools)

MeTools is a CakePHP plugin to improve applications development.  
It provides some useful tools, such as components, helpers and javascript libraries.

## Tests
Tests are divided into two groups, `onlyUnix` and `onlyWindows`. This is
necessary because some commands to be executed in the terminal are only valid
for an environment.

By default, phpunit is executed like this:

    vendor/bin/phpunit --exclude-group=onlyWindows

On Windows, it must be done this way:

    vendor\bin\phpunit.bat --exclude-group=onlyUnix

## Versioning
For transparency and insight into our release cycle and to maintain backward compatibility, 
MeTools will be maintained under the [Semantic Versioning guidelines](http://semver.org).

## Libraries and script
MeTools includes libraries and scripts:

- reCAPTCHA PHP library 1.11 ([site](https://developers.google.com/recaptcha)).

jQuery, Bootstrap, Bootstrap Date/Time Picker, Moment.js and Font Awesome are installed via Composer.
