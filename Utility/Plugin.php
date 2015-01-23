<?php
/**
 * Plugin utility
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
 * An utility to handle plugins.
 * 
 * You can use this utility by adding in your controller:
 * <code>
 * App::uses('Plugin', 'MeTools.Utility');
 * </code>
 */
class Plugin {
	/**
	 * Gets the version for a plugin.
	 * @param string $plugin Plugin name
	 * @return mixed Version number or FALSE
	 */
	public static function getVersion($plugin) {
		$path = CakePlugin::path($plugin);
		
		if(empty($path))
			return FALSE;
		
		$folder = new Folder($path);
		$files = $folder->find('version(\.txt)?');
			
		if(empty($files[0]))
			return FALSE;
		
		return trim(file_get_contents($path.$files[0]));
	}
	
	/**
	 * Gets the version number for each plugin.
	 * @param string|array $except Plugins to exclude
	 * @return mixed array with the version number for each plugin
	 * @uses getVersion()
	 */
	public static function getVersions($except = NULL) {
		//Gets plugins
		$plugins = CakePlugin::loaded();
		
		//Removes the exceptions
		if(is_string($except) || is_array($except)) {
			$except = is_array($except) ? $except : array($except);
			$plugins = array_diff($plugins, $except);
		}
		
		if(empty($plugins))
			return FALSE;
		
		$version = array();
		
		//For each plugin, sets the name and the version number
		foreach($plugins as $plugin)
			$version[] = array('name' => $plugin, 'version' => self::getVersion($plugin));
		
		return $version;
	}
}