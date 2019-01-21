# MeTools

[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.txt)
[![Build Status](https://travis-ci.org/mirko-pagliai/me-tools.svg?branch=master)](https://travis-ci.org/mirko-pagliai/me-tools)
[![Build status](https://ci.appveyor.com/api/projects/status/mlm4yqrmj8c5thr0?svg=true)](https://ci.appveyor.com/project/mirko-pagliai/me-tools)
[![Coverage Status](https://img.shields.io/codecov/c/github/mirko-pagliai/me-tools.svg?style=flat-square)](https://codecov.io/github/mirko-pagliai/me-tools)

MeTools is a CakePHP plugin to improve applications development.  
It provides some useful tools, such as components, helpers and javascript libraries.  
Refer to our [API](//mirko-pagliai.github.io/me-tools) to discover them all.

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
