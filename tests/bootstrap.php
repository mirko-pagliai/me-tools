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

ini_set('intl.default_locale', 'en_US');

if (!defined('DS')) {
    define('DS', DIRECTORY_SEPARATOR);
}

define('ROOT', dirname(__DIR__) . DS);
const CORE_PATH = ROOT . 'vendor' . DS . 'cakephp' . DS . 'cakephp' . DS;
const APP = ROOT . 'tests' . DS . 'test_app' . DS;
const APP_DIR = 'test_app';
const WWW_ROOT = APP . 'webroot' . DS;
define('TMP', sys_get_temp_dir() . DS . 'me_tools' . DS);
const LOGS = TMP . 'cakephp_log' . DS;
const UPLOADS = TMP . 'uploads' . DS;

foreach ([TMP, LOGS, UPLOADS] as $dir) {
    if (!file_exists($dir)) {
        mkdir($dir, 0777, true);
    }
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

Configure::write('Session', ['defaults' => 'php']);
Configure::write('Assets.target', TMP . 'assets');

Cache::setConfig([
    '_cake_core_' => [
        'engine' => 'File',
        'prefix' => 'cake_core_',
        'serialize' => true,
    ],
]);

$_SERVER['PHP_SELF'] = '/';
