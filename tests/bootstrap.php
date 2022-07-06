<?php
declare(strict_types=1);

/**
 * This file is part of me-tools.
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright   Copyright (c) Mirko Pagliai
 * @link        https://github.com/mirko-pagliai/me-tools
 * @license     https://opensource.org/licenses/mit-license.php MIT License
 */

use Cake\Cache\Cache;
use Cake\Core\Configure;
use Cake\Datasource\ConnectionManager;

ini_set('intl.default_locale', 'en_US');

if (!defined('DS')) {
    define('DS', DIRECTORY_SEPARATOR);
}

// Path constants to a few helpful things.
define('ROOT', dirname(__DIR__) . DS);
define('VENDOR', ROOT . 'vendor' . DS);
define('CORE_PATH', ROOT . 'vendor' . DS . 'cakephp' . DS . 'cakephp' . DS);
define('CAKE', CORE_PATH . 'src' . DS);
define('TESTS', ROOT . 'tests');
define('APP', ROOT . 'tests' . DS . 'test_app' . DS);
define('APP_DIR', 'test_app');
define('WEBROOT_DIR', 'webroot');
define('WWW_ROOT', APP . 'webroot' . DS);
define('TMP', sys_get_temp_dir() . DS . 'me_tools' . DS);
define('CONFIG', APP . 'config' . DS);
define('CACHE', TMP . 'cache' . DS);
define('LOGS', TMP . 'cakephp_log' . DS);
define('SESSIONS', TMP . 'sessions' . DS);
define('UPLOADS', TMP . 'uploads' . DS);

foreach ([
    TMP . 'tests',
    LOGS,
    SESSIONS,
    CACHE . 'models',
    CACHE . 'persistent',
    CACHE . 'views',
    UPLOADS,
] as $dir) {
    @mkdir($dir, 0777, true);
}

require_once dirname(__DIR__) . '/vendor/autoload.php';
require_once CORE_PATH . 'config' . DS . 'bootstrap.php';

date_default_timezone_set('UTC');
mb_internal_encoding('UTF-8');

Configure::write('debug', true);
Configure::write('App', [
    'namespace' => 'App',
    'encoding' => 'UTF-8',
    'base' => false,
    'baseUrl' => false,
    'dir' => APP_DIR,
    'webroot' => 'webroot',
    'wwwRoot' => WWW_ROOT,
    'fullBaseUrl' => 'http://localhost',
    'imageBaseUrl' => 'img/',
    'jsBaseUrl' => 'js/',
    'cssBaseUrl' => 'css/',
    'paths' => [
        'plugins' => [APP . 'Plugin' . DS],
    ],
]);

/**
 * @todo Upgrade fixtures: https://book.cakephp.org/4/en/appendices/fixture-upgrade.html
 */
Configure::write('Error.ignoredDeprecationPaths', ['*/cakephp/src/TestSuite/Fixture/FixtureInjector.php']);
Configure::write('Session', ['defaults' => 'php']);
Configure::write('Assets.target', TMP . 'assets');

Cache::setConfig([
    '_cake_core_' => [
        'engine' => 'File',
        'prefix' => 'cake_core_',
        'serialize' => true,
    ],
    '_cake_model_' => [
        'engine' => 'File',
        'prefix' => 'cake_model_',
        'serialize' => true,
    ],
    'default' => [
        'engine' => 'File',
        'prefix' => 'default_',
        'serialize' => true,
    ],
]);

ConnectionManager::setConfig('test', ['url' => 'sqlite:///' . TMP . 'test.sq3']);

$_SERVER['PHP_SELF'] = '/';

/**
 * @to-do To be removed in a later version
 */
if (!class_exists('Cake\Console\TestSuite\StubConsoleOutput')) {
    class_alias('Cake\TestSuite\Stub\ConsoleOutput', 'Cake\Console\TestSuite\StubConsoleOutput');
    class_alias('Cake\TestSuite\ConsoleIntegrationTestTrait', 'Cake\Console\TestSuite\ConsoleIntegrationTestTrait');
}
