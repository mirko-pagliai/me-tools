# MeTools

[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.txt)
[![CI](https://github.com/mirko-pagliai/me-tools/actions/workflows/ci.yml/badge.svg)](https://github.com/mirko-pagliai/me-tools/actions/workflows/ci.yml)
[![codecov](https://codecov.io/gh/mirko-pagliai/me-tools/branch/master/graph/badge.svg?token=qIHCm6UVu1)](https://codecov.io/gh/mirko-pagliai/me-tools)
[![Codacy Badge](https://app.codacy.com/project/badge/Grade/cc055cbeba0a454188e14f726b4423c9)](https://www.codacy.com/gh/mirko-pagliai/me-tools/dashboard?utm_source=github.com&amp;utm_medium=referral&amp;utm_content=mirko-pagliai/me-tools&amp;utm_campaign=Badge_Grade)
[![CodeFactor](https://www.codefactor.io/repository/github/mirko-pagliai/me-tools/badge/develop)](https://www.codefactor.io/repository/github/mirko-pagliai/me-tools/overview/develop)

MeTools is a CakePHP plugin to improve applications development.
It provides some useful tools, such as components, helpers and javascript libraries.
Refer to our [API](http://mirko-pagliai.github.io/me-tools) to discover them all.

## Installation
You can install the plugin via composer:

```bash
$ composer require --prefer-dist mirko-pagliai/me-tools
```

Then you have to load the plugin. For more information on how to load the plugin, please refer to the [Cookbook](http://book.cakephp.org/4.0/en/plugins.html#loading-a-plugin).

Simply, you can execute the shell command to enable the plugin:
```bash
bin/cake plugin load MeTools
```
This would update your application's bootstrap method.

### Installation on older CakePHP and PHP versions
Recent packages and the master branch require at least CakePHP 4.2 and PHP 7.4 and the current development of the code is based on these and later versions of CakePHP and PHP.
However, there are still some branches compatible with previous versions of CakePHP and PHP.

#### For PHP 7.2 or later
The [php7.2](http://github.com/mirko-pagliai/me-tools/tree/php7.2) branch requires at least PHP 7.2.

In this case, you can install the package as well:
```bash
$ composer require --prefer-dist mirko-pagliai/me-tools:dev-php7.2
```

Note that the `php7.2` branch will no longer be updated as of May 13, 2022,
except for security patches, and it matches the [2.20.9](http://github.com/mirko-pagliai/me-tools/releases/tag/2.20.9) version.

### Use the theme for Bake
MeTools includes a theme for Bake. For information on Bake's themes, refer to the [CookBook](http://book.cakephp.org/bake/2/en/development.html#creating-a-bake-theme).

If you want to use this theme, don't forget to use the `--theme MeTools` option when you Bake, or to set the theme as default:
```php
<?php
// in src/Application::bootstrapCli() before loading the 'Bake' plugin.
Configure::write('Bake.theme', 'MeTools');
```

## How to extract POT files
First, copy the [CakePHP binaries](http://github.com/cakephp/app/tree/4.x/bin) (at least `bin/cake` and `bin/cake.php`),
adapting `bin/cake.php` to the plugin.

For example, it might look like this:
```php
#!/usr/bin/php -q
<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/tests/bootstrap.php';

use App\Application;
use Cake\Console\CommandRunner;

// Build the runner with an application and root executable name.
$runner = new CommandRunner(new Application(APP . '/config'), 'cake');
exit($runner->run($argv));
```

Then run the extract command in the console:
```bash
$ bin/cake i18n extract --paths config,src,templates --overwrite
```

## Versioning
For transparency and insight into our release cycle and to maintain backward compatibility, MeTools will be maintained under the [Semantic Versioning guidelines](http://semver.org).
