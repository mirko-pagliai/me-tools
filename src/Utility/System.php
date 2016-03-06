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
 * @copyright	Copyright (c) 2016, Mirko Pagliai for Nova Atlantis Ltd
 * @license		http://www.gnu.org/licenses/agpl.txt AGPL License
 * @link		http://git.novatlantis.it Nova Atlantis Ltd
 */
namespace MeTools\Utility;

use Cake\Cache\Cache;

/**
 * An utility for checking the status of the system and perform maintenance tasks.
 * 
 * You can use this utility by adding:
 * <code>
 * use MeTools\Utility\System;
 * </code>
 */
class System {	
	/**
	 * Gets all changelog files. 
	 * 
	 * It searchs into `ROOT` and all loaded plugins.
	 * @uses MeTools\Core\Plugin::path()
	 * @return array Changelog files
	 * @uses MeTools\Core\Plugin::path()
	 */
	public static function changelogs() {
		foreach(am([ROOT.DS], \MeTools\Core\Plugin::path()) as $path) {
			//For each changelog file in the current path
			foreach((new \Cake\Filesystem\Folder($path))->find('CHANGELOG(\..+)?') as $file)
				$files[] = rtr($path.$file);
		}
		
		return $files;
	}

    /**
     * Checks if the cache is enabled
     * @return boolean
     */
    public static function checkCache() {
		return Cache::enabled();
    }
	
	/**
     * Clears the cache
     * @return boolean TRUE if the cache is writable and were successfully cleared, FALSE otherwise
	 * @uses Cake\Cache\Cache::clear()
	 * @uses Cake\Cache\Cache::clearGroup()
	 * @uses Cake\Cache\Cache::configured()
	 * @uses Cake\Cache\Cache::groupConfigs()
	 */
    public static function clearCache() {
		$success = TRUE;
		
		//Cleans all cached values for all cache configurations
		foreach(Cache::configured() as $config) {
			if(!Cache::clear(FALSE, $config) && $success)
				$success = FALSE;
		}
		
		//Clean all keys from the cache belonging to all group configurations
		foreach(Cache::groupConfigs() as $groups) 
			foreach($groups as $group) {
				if(!Cache::clearGroup($group) && $success)
					$success = FALSE;
			}
		
		return $success;
    }
}