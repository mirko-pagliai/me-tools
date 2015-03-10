<?php
/**
 * System utility
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

App::uses('Folder', 'Utility');
App::uses('Plugin', 'MeTools.Utility');

/**
 * An utility for checking the status of the system and perform maintenance tasks.
 * 
 * You can use this utility by adding in your controller:
 * <code>
 * App::uses('System', 'MeTools.Utility');
 * </code>
 */
class System {
    /**
     * Checks if the cache is readable and writable.
     * @return boolean TRUE if if the cache is readable and writable, FALSE otherwise
	 * @uses dirIsWritable()
     */
    public static function checkCache() {
		return self::dirIsWritable(CACHE);
    }

    /**
     * Checks if the cache is enabled.
     * @return boolean TRUE if the cache is enabled, FALSE otherwise
     */
    public static function checkCacheStatus() {
        return !Configure::read('Cache.disable');
    }
	
    /**
     * Checks if the logs directory is readable and writable.
     * @return boolean TRUE if if the logs directory is readable and writable, FALSE otherwise
	 * @uses dirIsWritable()
     */
	public static function checkLogs() {
		return self::dirIsWritable(LOGS);
	}

    /**
     * Checks if the temporary directory is readable and writable.
     * @return boolean TRUE if the temporary directory is readable and writable, FALSE otherwise
	 * @uses dirIsWritable()
     */
    public static function checkTmp() {
		return self::dirIsWritable(TMP);
    }

    /**
     * Clears the cache.
     * @return boolean TRUE if the cache is writable and were successfully cleared, FALSE otherwise
	 * @uses checkCache()
	 */
    public static function clearCache() {
		if(!self::checkCache())
			return FALSE;
		
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
     * Clears the thumbnails.
     * @return boolean TRUE if the thumbnails are writable and were successfully cleared, FALSE otherwise
	 * @uses checkThumbs()
     */
    public static function clearThumbs() {
		if(!self::checkThumbs())
			return FALSE;
		
        $dir = new Folder(TMP.'thumbs');
		$success = TRUE;
		
		//For each file
        foreach($dir->findRecursive() as $file) {
            $file = new File($file);
			
			//Deletes the file
            if(!$file->delete() && $success)
                $success = FALSE;
        }
		
        return $success;
    }
	
	/**
	 * Checks if a directory and its subdirectories are readable and writable.
	 * @param string $path Path
	 * @return boolean TRUE if they are readable and writable, FALSE otherwise
	 */
	public static function dirIsWritable($directory) {
		if(!is_readable($directory) || !is_writable($directory))
			return FALSE;
		
		$folder = new Folder();

        foreach($folder->tree($directory, FALSE, 'dir') as $dir) {
            if(!is_readable($dir) || !is_writable($dir))
                return FALSE;
        }

        return TRUE;
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
	 * Gets all changelog files. It searchs into APP and all loaded plugins.
	 * @uses Plugin::getPath()
	 */
	public static function getChangelogs() {
		return array_values(array_filter(array_map(function($path) {
			//Gets the current locale
			$locale = Configure::read('Config.language');
			
			if(!empty($locale) && is_readable($file = sprintf('%sCHANGELOG_%s.md', $path, $locale)))
				return $file;
			elseif(is_readable($file = $path.'CHANGELOG.md'))
				return $file;
			else
				return FALSE;	
		}, am(array(APP), Plugin::getPath()))));
	}

    /**
     * Gets the thumbnails size.
     * @return int Thumbnails size
	 * @uses checkThumbs()
     */
    public static function getThumbsSize() {
		if(!self::checkThumbs())
			return FALSE;
		
        $thumbs = new Folder(TMP.'thumbs');
        return $thumbs->dirsize();
    }
}