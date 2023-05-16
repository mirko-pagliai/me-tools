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
Configure::write('MeTools.WritableDirs', [
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
]);

//Sets symbolic links for vendor assets to be created
Configure::write('MeTools.VendorLinks', [
    'axllent/jquery' => 'jquery',
    'components/jquery' => 'jquery',
    'fortawesome/font-awesome' => 'font-awesome',
    'npm-asset/fancyapps-fancybox/dist' => 'fancyapps-fancybox',
    'twbs/bootstrap/dist' => 'bootstrap',
]);

require_once 'i18n_constants.php';
