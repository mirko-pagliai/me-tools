<?php
/**
 * Php utility
 *
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
 * @author		Mirko Pagliai <mirko.pagliai@gmail.com>
 * @copyright	Copyright (c) 2015, Mirko Pagliai for Nova Atlantis Ltd
 * @license		http://www.gnu.org/licenses/agpl.txt AGPL License
 * @link		http://git.novatlantis.it Nova Atlantis Ltd
 * @package		MeTools\Utility
 */

/**
 * An utility to handle PHP.
 * 
 * You can use this utility by adding in your controller:
 * <code>
 * App::uses('Php', 'MeTools.Utility');
 * </code>
 */
class Php {
    /**
     * Alias for `checkExtension()` method.
     * @see checkExtension()
     */
    public static function checkExt() {
        return call_user_func_array(array(get_class(), 'checkExtension'), func_get_args());
    }
	
    /**
     * Checks if an extension is enabled.
     * @param string $extension Extension to be checked
     * @return boolean TRUE if the extension is enabled, FALSE otherwise
     */
    public static function checkExtension($extension) {
        return extension_loaded($extension);
    }
	
    /**
     * Checks if the current version of PHP is equal to or greater than the required version.
	 * 
	 * CakePHP 2.x requires at least the `5.2.8` version.
     * @param string $required Required version of PHP
     * @return boolean TRUE if the current version is equal to or greater than the required version, FALSE otherwise
     * @uses getVersion()
     */
    public static function checkVersion($required = '5.2.8') {
        return version_compare(self::getVersion(), $required, '>=');
    }

    /**
     * Gets the current version.
     * @return string Current version
     */
    public static function getVersion() {
        return PHP_VERSION;
    }
}