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
namespace MeTools\Utility;

/**
 * An utility to handle Apache
 */
class Apache
{
    /**
     * Checks if a module is enabled.
     * @param string $module Name of the module to be checked
     * @return bool
     */
    public static function module($module)
    {
        return in_array($module, apache_get_modules());
    }

    /**
     * Gets the version.
     * @return string
     */
    public static function version()
    {
        $version = apache_get_version();

        preg_match('/Apache\/([0-9]+\.[0-9]+\.[0-9]+)/i', $version, $matches);

        return empty($matches[1]) ? $version : $matches[1];
    }
}
