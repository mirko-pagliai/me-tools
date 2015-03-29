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

use Cake\Core\Plugin;

/**
 * An utility to handle plugins.
 * 
 * You can use this utility by adding in your controller:
 * <code>
 * use MeTools\Utility\MePlugin;
 * </code>
 */
class MePlugin {
    /**
     * Alias for `getAll()` method
     * @see getAll()
     */
    public function all() {
        return call_user_func_array(array(get_class(), 'getAll'), func_get_args());
    }
	
	/**
	 * Gets all loaded plugins.
	 * @return array Plugins
	 * @uses Plugin::loaded()
	 */
	public static function getAll() {
		return Plugin::loaded();
	}
	
	/**
	 * Gets a path for a plugin or for all plugins.
	 * 
	 * If `$plugin` is not a string, returns all the plugins path.
	 * @param mixed $plugin Plugin name
	 * @return mixed Plugin path or all plugins path
	 * @uses Plugin::path()
	 * @uses getAll()
	 */
	public static function getPath($plugin = NULL) {
		if(is_string($plugin))
			return Plugin::path($plugin);
		
		return array_map(function($v){
			return self::path($v);
		}, self::all());
	}
	
    /**
     * Alias for `getPath()` method
     * @see getPath()
     */
    public function path() {
        return call_user_func_array(array(get_class(), 'getPath'), func_get_args());
    }
}