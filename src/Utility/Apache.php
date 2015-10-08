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
 * @author		Mirko Pagliai <mirko.pagliai@gmail.com>
 * @copyright	Copyright (c) 2015, Mirko Pagliai for Nova Atlantis Ltd
 * @license		http://www.gnu.org/licenses/agpl.txt AGPL License
 * @link		http://git.novatlantis.it Nova Atlantis Ltd
 */
namespace MeTools\Utility;

/**
 * An utility to handle Apache.
 * 
 * You can use this utility by adding:
 * <code>
 * use MeTools\Utility\Apache;
 * </code>
 */
class Apache {	
	/**
     * Checks if a module is enabled.
     * @param string $module Name of the module to be checked
     * @return mixed TRUE if the module is enabled, FALSE otherwise. NULL if cannot check
     * @uses modules()
     */
    public static function checkModule($module) {
		$modules = self::modules();
		
		if(is_null($modules) || empty($modules))
			return;
		
        return in_array($module, $modules);
    }
	
    /**
     * Gets modules.
     * @return mixed Modules list. NULL if cannot check
     */
    public static function getModules() {
		if(!function_exists('apache_get_modules'))
			return;
		
        return apache_get_modules();
    }
	
	/**
	 * Gets the version.
	 * @return mixed Version. NULL if cannot check
	 */
	public static function getVersion() {
		if(!function_exists('apache_get_version'))
			return;
		
		preg_match('/Apache\/([0-9]+\.[0-9]+\.[0-9]+)/i', $version = apache_get_version(), $matches);
		
		return empty($matches[1]) ? $version : $matches[1];
	}
	
    /**
     * Alias for `checkModule()` method.
     * @see checkModule()
     */
    public static function module() {
        return call_user_func_array([get_class(), 'checkModule'], func_get_args());
    }
	
    /**
     * Alias for `getModules()` method.
     * @see getModules()
     */
    public static function modules() {
        return call_user_func_array([get_class(), 'getModules'], func_get_args());
    }
	
    /**
     * Alias for `getVersion()` method.
     * @see getVersion()
     */
    public static function version() {
        return call_user_func_array([get_class(), 'getVersion'], func_get_args());
    }
}