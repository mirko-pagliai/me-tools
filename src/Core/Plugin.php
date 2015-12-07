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
 * @see			http://api.cakephp.org/3.1/class-Cake.Core.Plugin.html Plugin
 */
namespace MeTools\Core;

use Cake\Core\Plugin as CakePlugin;
use Cake\Filesystem\Folder;

/**
 * An utility to handle plugins.
 * 
 * Rewrites {@link http://api.cakephp.org/3.1/class-Cake.Core.Plugin.html Plugin}.
 */
class Plugin extends CakePlugin {
	/**
	 * Gets all loaded plugins.
	 * @param string|array $except Plugins to exclude
	 * @return array Plugins
	 * @uses Cake\Core\Plugin::loaded()
	 */
	public static function all($except = NULL) {
		$plugins = parent::loaded();
		
		//Removes exceptions
		if(is_array($plugins) && (is_string($except) || is_array($except))) {
			$except = is_array($except) ? $except : [$except];
			$plugins = array_diff($plugins, $except);
		}
		
		return $plugins;
	}
	
	/**
	 * Gets a path for a plugin or for all plugins.
	 * 
	 * If `$plugin` is not a string, returns all the plugins path.
	 * @param string $plugin Plugin name (optional)
	 * @param string $filename Filename from plugin (optional)
	 * @return mixed Plugin path or all plugins path
	 * @uses Cake\Core\Plugin::path()
	 * @uses all()
	 */
	public static function path($plugin = NULL, $filename = NULL) {
		if(is_string($plugin)) {
			$path = parent::path($plugin);
			
			return is_string($filename) ? $path.$filename : $path;
		}
		
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
	public static function version($plugin) {
		$path = self::path($plugin);
		
		if(empty($path))
			return;
		
		$files = (new Folder($path))->find('version(\.txt)?');
		
		return empty($files[0]) ? FALSE : trim(file_get_contents($path.$files[0]));
	}
	
	/**
	 * Gets the version number for each plugin.
	 * @param string|array $except Plugins to exclude
	 * @return mixed array with the version number for each plugin
	 * @uses all()
	 * @uses version()
	 */
	public static function versions($except = NULL) {
		$versions = [];
		
		//For each plugin, sets the name and the version number
		foreach(self::all($except) as $plugin)
			if(self::version($plugin))
				$versions[] = ['name' => $plugin, 'version' => self::version($plugin)];
		
		return $versions;
	}
}