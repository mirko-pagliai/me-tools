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
 * @copyright	Copyright (c) 2014, Mirko Pagliai for Nova Atlantis Ltd
 * @license		http://www.gnu.org/licenses/agpl.txt AGPL License
 * @link		http://git.novatlantis.it Nova Atlantis Ltd
 * @package		MeTools\Utility
 */

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
	 * Checks if a directory and its subdirectories are readable and writable
	 * @param string $path Path
	 * @return boolean TRUE if they are readable and writable, FALSE otherwise
	 */
	private static function _dirIsWritable($directory) {
		if(!is_readable($directory))
			return FALSE;
		
		$folder = new Folder();

        foreach($folder->tree($directory, FALSE, 'dir') as $dir) {
            if(!is_readable($dir) || !is_writable($dir))
                return FALSE;
        }

        return TRUE;
	}


	/**
     * Checks if an Apache module is active
     * @param string $module Name of the module to be checked
     * @return boolean TRUE if the module is enabled, FALSE otherwise
     * @uses getApacheModules() to get Apache's modules
     */
    public static function checkApacheModule($module) {
        return in_array($module, self::getApacheModules());
    }

    /**
     * Checks if the cache and all its subdirectories are readable and writable
     * @return boolean TRUE if if the cache is readable and writable, FALSE otherwise
	 * @uses _dirIsWritable to check if is readable and writable
     */
    public static function checkCache() {
		return self::_dirIsWritable(CACHE);
    }

    /**
     * Checks the cache status (if it's enabled)
     * @return boolean TRUE if the cache is enabled, FALSE otherwise
     */
    public static function checkCacheStatus() {
        return !Configure::read('Cache.disable');
    }

    /**
     * Checks if the current version of PHP is equal to or greater than the required version
     * @param string $required_version Required version of PHP
     * @return boolean TRUE if the current version of PHP is equal to or greater than the required version, FALSE otherwise
     * @uses getPhpVersion() to get the current version of PHP
     */
    public static function checkPhpVersion($required_version) {
        return version_compare(self::getPhpVersion(), $required_version, '>=');
    }

    /**
     * Checks if a PHP extension is enabled
     * @param string $extension Name of the extension to be checked
     * @return boolean TRUE if the extension is enabled, FALSE otherwise
     */
    public static function checkPhpExtension($extension) {
        return extension_loaded($extension);
    }

    /**
     * Checks if the thumbnail directory is readable and writable
     * @return boolean TRUE if the thumbnail directory is readable and writable, FALSE otherwise
	 * @uses _dirIsWritable to check if is readable and writable
     */
    public static function checkThumbs() {
		return self::_dirIsWritable(TMP.'thumbs');
    }

    /**
     * Checks if the TMP and all its subdirectories are readable and writable
     * @return boolean TRUE if the temporary directory is readable and writable, FALSE otherwise
	 * @uses _dirIsWritable to check if is readable and writable
     */
    public static function checkTmp() {
		return self::_dirIsWritable(TMP);
    }

    /**
     * Clears the cache
     * @return boolean TRUE if the cache was successfully cleared, FALSE otherwise
     */
    public static function clearCache() {
        return Cache::clear(FALSE);
    }

    /**
     * Clears thumbnails
     * @return boolean TRUE if thumbnails were successfully cleared, FALSE otherwise
     */
    public static function clearThumbs() {
        $dir = new Folder($tmp = TMP.'thumbs');
        $files = $dir->findRecursive('.+\..+');
        $success = TRUE;
		
        foreach($files as $file) {
            $file = new File($file);
            if(!$file->delete() && $success)
                $success = FALSE;
        }
		
        return $success;
    }

    /**
     * Gets the Apache modules list
     * @return array Modules list
     */
    public static function getApacheModules() {
        return apache_get_modules();
    }

    /**
     * Gets the cache size
     * @return int Cache size
     */
    public static function getCacheSize() {
        $cache = new Folder(CACHE);
        return $cache->dirsize();
    }

    /**
     * Gets the CakePHP version
     * @return string CakePHP version
     */
    public static function getCakeVersion() {
        return Configure::version();
    }

    /**
     * Gets the MeTools version
     * @return string MeTools version
     */
    public static function getMeToolsVersion() {
        return file_get_contents(App::pluginPath('MeTools').'version');
    }

    /**
     * Gets the PHP extensions list
     * @return array Extensions list
     */
    public static function getPhpExtensions() {
        return get_loaded_extensions();
    }

    /**
     * Gets the current version of PHP
     * @return string Current version of PHP
     */
    public static function getPhpVersion() {
        return PHP_VERSION;
    }

    /**
     * Gets the thumbnails size
     * @return int Thumbnails size
     */
    public static function getThumbsSize() {
        $thumbs = new Folder(TMP.'thumbs');
        return $thumbs->dirsize();
    }
	
	/**
	 * Executes the `whereis` command on Unix systems.
	 * 
	 * It locates the binary files for a command.
	 * @param string $command Command
	 * @return mixed Array of binary files, otherwise FALSE
	 */
	public static function whereis($command) {
		$whereis = explode(' ', exec(sprintf('whereis -b %s', $command)));
			
		unset($whereis[0]);
		
		if(empty($whereis))
			return FALSE;
		
		return $whereis;
	}
	
	/**
	 * Executes the `which` command on Unix systems.
	 * 
	 * It shows the full path of (shell) commands.
	 * @param string $command Command
	 * @return mixed Full path of command, otherwise FALSE
	 */
	public static function which($command) {
		$which = exec(sprintf('which %s', $command));
		
		if(empty($which))
			return FALSE;
		
		return $which;
	}
}