<?php
/**
 * This file is part of MeTools.
 *
 * MeTools is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * MeTools is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with MeTools.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @author      Mirko Pagliai <mirko.pagliai@gmail.com>
 * @copyright   Copyright (c) 2016, Mirko Pagliai for Nova Atlantis Ltd
 * @license     http://www.gnu.org/licenses/agpl.txt AGPL License
 * @link        http://git.novatlantis.it Nova Atlantis Ltd
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
     * @return mixed true if the module is enabled, false otherwise. null if
     *  cannot check
     */
    public static function module($module)
    {
        if (!function_exists('apache_get_modules')) {
            return false;
        }

        return in_array($module, apache_get_modules());
    }

    /**
     * Gets the version.
     * @return mixed Version. null if cannot check
     */
    public static function version()
    {
        if (!function_exists('apache_get_version')) {
            return false;
        }

        $version = apache_get_version();

        preg_match('/Apache\/([0-9]+\.[0-9]+\.[0-9]+)/i', $version, $matches);

        return empty($matches[1]) ? $version : $matches[1];
    }
}
