<?php
/**
 * SystemComponent
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
 * @copyright	Copyright (c) 2013, Mirko Pagliai for Nova Atlantis Ltd
 * @license		http://www.gnu.org/licenses/agpl.txt AGPL License
 * @link		http://git.novatlantis.it Nova Atlantis Ltd
 * @package		MeTools\Controller\Component
 */

/**
 * A component for checking the status of the system and perform maintenance tasks.
 */
class SystemComponent extends Component {	
	/**
	 * Checks if an Apache module is active
	 * @param string $module Name of the module to be checked
	 * @return boolean TRUE if the module is enabled, FALSE otherwise
	 * @uses getApacheModules() to get Apache's modules
	 */
	public function checkApacheModule($module) {
		return in_array($module, self::getApacheModules());
	}
	
	/**
	 * Checks if the cache and all its subdirectories are readable and writable
	 * @return boolean TRUE if if the cache and all its subdirectories are readable and writable, FALSE otherwise
	 */
	public function checkCache() {
		//Cache default directories
		$default = array(CACHE, CACHE.'models', CACHE.'persistent', CACHE.'views');
		
		//Directories in the cache
		$cache = new Folder(CACHE);
		$cache = $cache->tree(CACHE, FALSE, 'dir');
		
		foreach(array_unique(am($cache, $default)) as $dir) {
			if(!is_readable($dir) || !is_writable($dir))
				return FALSE;
		}
		
		return TRUE;
	}

	/**
	 * Checks the cache status (if it's enabled)
	 * @return boolean TRUE if the cache is enabled, FALSE otherwise
	 */
	public function checkCacheStatus() {
		return !Configure::read('Cache.disable');
	}
	
	/**
	 * Checks if the current version of PHP is equal to or greater than the required version
	 * @param string $required_version Required version of PHP
	 * @return boolean TRUE if the current version of PHP is equal to or greater than the required version, FALSE otherwise
	 * @uses getPhpVersion() to get the current version of PHP
	 */
	public function checkPhpVersion($required_version) {		
		return version_compare(self::getPhpVersion(), $required_version, '>=');
	}
	
	/**
	 * Checks if a PHP extension is enabled
	 * @param string $extension Name of the extension to be checked
	 * @return boolean TRUE if the extension is enabled, FALSE otherwise
	 */
	public function checkPhpExtension($extension) {
		return extension_loaded($extension);
	}
	
	/**
	 * Checks if the TMP and all its subdirectories are readable and writable
	 * @return boolean TRUE if the TMP and all its subdirectories are readable and writable, FALSE otherwise
	 */
	public function checkTmp() {		
		$tmp = new Folder(TMP);
		
		foreach($tmp->tree(TMP, FALSE, 'dir') as $dir) {
			if(!is_readable($dir) || !is_writable($dir))
				return FALSE;
		}
		
		return TRUE;
	}
	
	/**
	 * Checks if the thumbnail directory is readable and writable
	 * @return boolean TRUE if the thumbnail directory is readable and writable, FALSE otherwise
	 */
	public function checkThumbs() {
		return is_readable(TMP.'thumbs') && is_writable(TMP.'thumbs');
	}
	
	/**
	 * Clear the cache
	 * @return boolean TRUE if the cache was successfully cleared, FALSE otherwise
	 */
	public function clearCache() {
		return Cache::clear(false);
	}
	
	/**
	 * Clear thumbnails
	 * @return boolean TRUE if thumbnails were successfully cleared, FALSE otherwise
	 */
	public function clearThumbs() {
		$dir = new Folder($tmp = TMP.'thumbs');
		$files = $dir->find('.*');
		
		$success = TRUE;
		
		foreach($files as $file) {
			$file = new File($tmp.DS.$file);
			if(!$file->delete() && $success)
				$success = FALSE;
		}
		
		return $success;
	}
	
	/**
	 * Gets the Apache modules list
	 * @return array Modules list
	 */
	public function getApacheModules() {
		return apache_get_modules();
	}
	
	/**
	 * Gets the cache size
	 * @return int Cache size
	 */
	public function getCacheSize() {
		$cache = new Folder(CACHE);
		return $cache->dirsize();
	}
	
	/**
	 * Gets the PHP extensions list
	 * @return array Extensions list
	 */
	public function getPhpExtensions() {
		return get_loaded_extensions();
	}
	
	/**
	 * Gets the current version of PHP
	 * @return string Current version of PHP
	 */
	public function getPhpVersion() {
		return PHP_VERSION;
	}
	
	/**
	 * Gets the thumbnails size
	 * @return int Thumbnails size
	 */
	public function getThumbsSize() {
		$thumbs = new Folder(TMP.'thumbs');
		return $thumbs->dirsize();
	}
}