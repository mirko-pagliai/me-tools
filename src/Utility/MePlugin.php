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
use Cake\Filesystem\Folder;

/**
 * An utility to handle plugins.
 * 
 * You can use this utility by adding:
 * <code>
 * use MeTools\Utility\MePlugin;
 * </code>
 */
class MePlugin {
    /**
     * Alias for `getAll()` method
     * @see getAll()
     */
    public static function all() {
        return call_user_func_array([get_class(), 'getAll'], func_get_args());
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
	 * Gets the version number for a plugin.
	 * @param string $plugin Plugin name
	 * @return mixed Version number or FALSE
	 * @uses path()
	 */
	public static function getVersion($plugin) {
		$path = self::path($plugin);
		
		if(empty($path))
			return FALSE;
		
		$folder = new Folder($path);
		$files = $folder->find('version(\.txt)?');
		
		return empty($files[0]) ? FALSE : trim(file_get_contents($path.$files[0]));
	}
	
	/**
	 * Gets the version number for each plugin.
	 * @param string|array $except Plugins to exclude
	 * @return mixed array with the version number for each plugin
	 * @uses all()
	 * @uses version()
	 */
	public static function getVersions($except = NULL) {
		//Gets plugins
		$plugins = self::all();
		
		//Removes exceptions
		if(is_string($except) || is_array($except)) {
			$except = is_array($except) ? $except : [$except];
			$plugins = array_diff($plugins, $except);
		}
		
		if(empty($plugins))
			return FALSE;
		
		$versions = [];
		
		//For each plugin, sets the name and the version number
		foreach($plugins as $plugin)
			if(self::version($plugin))
				$versions[] = ['name' => $plugin, 'version' => self::version($plugin)];
		
		return $versions;
	}
	
    /**
     * Alias for `getPath()` method
     * @see getPath()
     */
    public static function path() {
        return call_user_func_array([get_class(), 'getPath'], func_get_args());
    }
	
    /**
     * Alias for `getVersion()` method
     * @see getVersion()
     */
    public static function version() {
        return call_user_func_array([get_class(), 'getVersion'], func_get_args());
    }
	
    /**
     * Alias for `getVersions()` method
     * @see getVersions()
     */
    public static function versions() {
        return call_user_func_array([get_class(), 'getVersions'], func_get_args());
    }
}