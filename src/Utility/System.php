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
use Cake\Filesystem\File;
use Cake\Filesystem\Folder;
use MeTools\Core\Plugin;

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
     * Gets the CakePHP version.
     * @return string CakePHP version
     */
    public static function cakeVersion() {
        return Configure::version();
    }
	
	/**
	 * Gets all changelog files. 
	 * 
	 * It searchs into `ROOT` and all loaded plugins.
	 * @uses MeTools\Core\Plugin::path()
	 * @return array Changelog files
	 * @uses Cake\I18n\I18n::locale()
	 * @uses MeTools\Core\Plugin::path()
	 */
	public static function changelogs() {
		$files = af(array_map(function($path) {
			//Gets the current locale
			$locale = substr(\Cake\I18n\I18n::locale(), 0, 2);

			if(!empty($locale) && is_readable($file = sprintf($path.'CHANGELOG_%s.md', $locale)))
				return str_replace(ROOT.DS, NULL, $file);
			elseif(is_readable($file = $path.'CHANGELOG.md'))
				return str_replace(ROOT.DS, NULL, $file);
			else
				return FALSE;
		}, am([ROOT.DS], Plugin::path())));
		
		//Re-indexes, starting to 1, and returns
		return array_combine(range(1, count($files)), array_values($files));
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
     * Clears the logs
     * @return boolean TRUE if the cache is writable and were successfully cleared, FALSE otherwise
	 * @uses checkLogs()
	 */
	public static function clearLogs() {
		if(!self::checkLogs())
			return FALSE;
		
		$success = TRUE;
		
		//Deletes each file
        foreach((new Folder(LOGS))->findRecursive() as $file)
            if(!(new File($file))->delete() && $success)
                $success = FALSE;
		
        return $success;
	}
	
    /**
     * Gets the cache size.
     * @return int Cache size
     */
    public static function getCacheSize() {
        return (new Folder(CACHE))->dirsize();
    }
	
	/**
	 * Gets all logs files.
	 * @return array|Null Log files
	 */
	public static function getLogs() {
		//Gets log files
		$files = (new Folder(LOGS))->find('[^\.]+\.log(\.[^\-]+)?', TRUE);
				
		//Re-indexes, starting to 1, and returns
		return empty($files) ? NULL : array_combine(range(1, count($files)), array_values($files));
	}
	
	/**
	 * Gets the logs size.
	 * @return int Logs size
	 */
	public static function getLogsSize() {
        return (new Folder(LOGS))->dirsize();
	}
	
    /**
     * Alias for `getLogs()` method.
     * @see getLogs()
     */
    public static function logs() {
        return call_user_func_array([get_class(), 'getLogs'], func_get_args());
    }
	
	
    /**
     * Alias for `getLogsSize()` method.
     * @see getLogsSize()
     */
	public static function logsSize() {
        return call_user_func_array([get_class(), 'getLogsSize'], func_get_args());
	}
}