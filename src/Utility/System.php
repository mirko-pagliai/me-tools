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

use Cake\Cache\Cache;
use Cake\Core\Configure;
use Cake\Filesystem\Folder;
use MeTools\Utility\MePlugin as Plugin;

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
     * Alias for `getCacheSize()` method.
     * @see getCacheSize()
     */
    public static function cacheSize() {
        return call_user_func_array([get_class(), 'getCacheSize'], func_get_args());
    }
	
    /**
     * Alias for `checkCacheStatus()` method.
     * @see checkCacheStatus()
     */
    public static function cacheStatus() {
        return call_user_func_array([get_class(), 'checkCacheStatus'], func_get_args());
    }
	
    /**
     * Alias for `getCakeVersion()` method.
     * @see getCakeVersion()
     */
    public static function cakeVersion() {
        return call_user_func_array([get_class(), 'getCakeVersion'], func_get_args());
    }
	
    /**
     * Alias for `getChangelogs()` method.
     * @see getChangelogs()
     */
    public static function changelogs() {
        return call_user_func_array([get_class(), 'getChangelogs'], func_get_args());
    }
	
    /**
     * Checks if the cache is readable and writable
     * @return boolean
     */
    public static function checkCache() {
		return folder_is_writable(CACHE);
    }

    /**
     * Checks if the cache is enabled
     * @return boolean
     */
    public static function checkCacheStatus() {
		return Cache::enabled();
    }
	
    /**
     * Checks if the logs directory is readable and writable
     * @return boolean
     */
	public static function checkLogs() {
		return folder_is_writable(LOGS);
	}
	
    /**
     * Checks if the temporary directory is readable and writable.
     * @return boolean
     */
    public static function checkTmp() {
		return folder_is_writable(TMP);
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
	
    /**
     * Gets the cache size.
     * @return int Cache size
     */
    public static function getCacheSize() {
        $cache = new Folder(CACHE);
        return $cache->dirsize();
    }

    /**
     * Gets the CakePHP version.
     * @return string CakePHP version
     */
    public static function getCakeVersion() {
        return Configure::version();
    }
	
	/**
	 * Gets all changelog files. 
	 * 
	 * It searchs into `ROOT` and all loaded plugins.
	 * @uses MeTools\Utility\Plugin::getPath()
	 * @return array Changelog files
	 */
	public static function getChangelogs() {
		//Set paths
		$paths = am([ROOT.DS], Plugin::getPath());
		
		//Gets changelog files
		$files = af(array_map(function($path) {
			//TO-DO: fix
			//Gets the current locale
			//$locale = Configure::read('Config.language');
			$locale = 'it';

			if(!empty($locale) && is_readable($file = sprintf($path.'CHANGELOG_%s.md', $locale)))
				return str_replace(ROOT.DS, NULL, $file);
			elseif(is_readable($file = $path.'CHANGELOG.md'))
				return str_replace(ROOT.DS, NULL, $file);
			else
				return FALSE;	
		}, $paths));
		
		//Re-indexes, starting to 1, and returns
		return array_combine(range(1, count($files)), array_values($files));
	}
	
	/**
	 * Gets all logs files.
	 * @return array Log files
	 */
	public static function getLogs() {
		//Gets log files
		$dir = new Folder(LOGS);
		$files = $dir->find('[^\.]+\.log(\.[^\-]+)?', TRUE);
		
		if(empty($files))
			return [];
		
		//Re-indexes, starting to 1, and returns
		return array_combine(range(1, count($files)), array_values($files));
	}
	
    /**
     * Alias for `getLogs()` method.
     * @see getLogs()
     */
    public static function logs() {
        return call_user_func_array([get_class(), 'getLogs'], func_get_args());
    }
}