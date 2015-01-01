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
     * Checks if an Apache module is enabled.
     * @param string $module Name of the module to be checked
     * @return boolean TRUE if the module is enabled, FALSE otherwise
     * @uses getApacheModules()
     */
    public static function checkApacheModule($module) {
        return in_array($module, self::getApacheModules());
    }

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
     * Checks if the current version of PHP is equal to or greater than the required version.
	 * 
	 * CakePHP 2.x requires at least the `5.2.8` version.
     * @param string $required Required version of PHP
     * @return boolean TRUE if the current version is equal to or greater than the required version, FALSE otherwise
     * @uses getPhpVersion()
     */
    public static function checkPhpVersion($required = '5.2.8') {
        return version_compare(self::getPhpVersion(), $required, '>=');
    }

    /**
     * Checks if a PHP extension is enabled.
     * @param string $extension Name of the extension to be checked
     * @return boolean TRUE if the extension is enabled, FALSE otherwise
     */
    public static function checkPhpExtension($extension) {
        return extension_loaded($extension);
    }

    /**
     * Checks if the thumbnail directory is readable and writable.
     * @return boolean TRUE if the thumbnail directory is readable and writable, FALSE otherwise
	 * @uses dirIsWritable()
     */
    public static function checkThumbs() {
		return self::dirIsWritable(TMP.'thumbs'.DS.'photos') && self::dirIsWritable(TMP.'thumbs'.DS.'remotes') && self::dirIsWritable(TMP.'thumbs'.DS.'videos');
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
		
		$dir = new Folder(CACHE);
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
     * Gets the Apache modules list.
     * @return array Modules list
     */
    public static function getApacheModules() {
        return apache_get_modules();
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
     * Gets the MeTools version.
     * @return string MeTools version
     */
    public static function getMeToolsVersion() {
        return file_get_contents(App::pluginPath('MeTools').'version');
    }

    /**
     * Gets the PHP extensions list.
     * @return array Extensions list
     */
    public static function getPhpExtensions() {
        return get_loaded_extensions();
    }

    /**
     * Gets the current version of PHP.
     * @return string Current version of PHP
     */
    public static function getPhpVersion() {
        return PHP_VERSION;
    }
	
	/**
	 * Gets the version number for each plugin.
	 * @param string|array $except Plugins to exclude
	 * @return mixed array with the version number for each plugin
	 */
	public static function getPluginsVersion($except = NULL) {
		//Gets plugins
		$plugins = CakePlugin::loaded();
		
		//Removes the exceptions from the list
		if(is_string($except) || is_array($except)) {
			$except = is_array($except) ? $except : array($except);
			$plugins = array_diff($plugins, $except);
		}
		
		$version = array();
		
		//For each plugin, gets the name and the version number
		foreach($plugins as $name) {
			$folder = new Folder($path = CakePlugin::path($name));
			$files = $folder->find('version(\.txt)?');
			
			if(!empty($files[0]))
				$version[] = array('name' => $name, 'version' => trim(file_get_contents($path.$files[0])));
		}
		
		return $version;
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
	
	/**
	 * Checks if is the root user.
	 * @return boolean TRUE if is the root user, otherwise FALSE
	 */
	public static function is_root() {
		//`posix_getuid()` returns 0 if is the root user
		return !posix_getuid();
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
		
		return empty($whereis) ? FALSE : $whereis;
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
		
		return empty($which) ? FALSE : $which;
	}
}