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

use Cake\Core\Configure;

//Sets directories to be created and must be writable
Configure::write('WRITABLE_DIRS', array_merge(Configure::read('WRITABLE_DIRS', []), [
    CACHE,
    CACHE . 'models',
    CACHE . 'persistent',
    CACHE . 'views',
    LOGS,
    TMP,
    TMP . 'sessions',
    TMP . 'tests',
    WWW_ROOT . 'files',
    WWW_ROOT . 'vendor',
]));

//Sets symbolic links for vendor assets to be created
Configure::write('VENDOR_LINKS', array_merge(Configure::read('VENDOR_LINKS', []), [
    'eonasdan' . DS . 'bootstrap-datetimepicker' . DS . 'build' => 'bootstrap-datetimepicker',
    'components' . DS . 'jquery' => 'jquery',
    'moment' . DS . 'moment' . DS . 'min' => 'moment',
    'fortawesome' . DS . 'font-awesome' => 'font-awesome',
    'npm-asset' . DS . 'fancyapps-fancybox' . DS . 'dist' => 'fancyapps-fancybox',
    'twbs' . DS . 'bootstrap' . DS . 'dist' => 'bootstrap',
]));
