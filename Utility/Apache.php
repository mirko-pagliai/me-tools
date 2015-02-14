<?php
/**
 * Apache utility
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
 * An utility to handle Apache.
 * 
 * You can use this utility by adding in your controller:
 * <code>
 * App::uses('Apache', 'MeTools.Utility');
 * </code>
 */
class Apache {
    /**
     * Alias for `checkModule()` method.
     * @see checkModule()
     */
    public static function checkMod() {
        return call_user_func_array(array(get_class(), 'checkModule'), func_get_args());
    }
	
	/**
     * Checks if a module is enabled.
     * @param string $module Name of the module to be checked
     * @return mixed TRUE if the module is enabled, FALSE otherwise. NULL if cannot check
     * @uses getModules()
     */
    public static function checkModule($module) {
		$modules = self::getModules();
		
		if(empty($modules))
			return NULL;
		
        return in_array($module, $modules);
    }
	
    /**
     * Gets modules.
     * @return mixed Modules list or FALSE
     */
    public static function getModules() {
		if(!function_exists('apache_get_modules'))
			return FALSE;
		
        return apache_get_modules();
    }
	
	/**
	 * Gets the version.
	 * @return string Version
	 */
	public static function getVersion() {
		return apache_get_version();
	}
}