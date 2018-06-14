<?php
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
use Cake\Core\Plugin;

//Sets the default MeTools name
if (!defined('ME_TOOLS')) {
    define('ME_TOOLS', 'MeTools');
}

//Loads `Assets` plugin
if (!Plugin::loaded('Assets')) {
    Plugin::load('Assets', ['bootstrap' => true, 'routes' => true]);
}
