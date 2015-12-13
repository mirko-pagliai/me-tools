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

use Cake\Filesystem\File;
use Cake\Filesystem\Folder;
use Cake\Network\Exception\InternalErrorException;
use MeTools\Core\Plugin;
use MeTools\Utility\Unix;

/**
 * An utility to handle assets.
 * 
 * You can use this utility by adding:
 * <code>
 * use MeTools\Utility\Asset;
 * </code>
 */
class Asset {
	/**
	 * Parses paths and for each path returns an array with the full path and the last modification time
     * @param string|array $path String or array of css/js files
	 * @param string $extension Extension (`css` or `js`)
	 * @return array
	 * @uses MeTools\Core\Plugin::all()
	 * @uses MeTools\Core\Plugin::path()
	 */
	protected static function _parsePath($path, $extension) {
		$paths = is_array($path) ? $path : [$path];
		$plugins = Plugin::all();
		
		foreach($paths as $k => $path) {
			$plugin = pluginSplit($path);
			
			if(in_array($plugin[0], $plugins))
				$path = $plugin[1];
			
			if(substr($path, 0, 1) == '/')
				$path = substr($path, 1);
			else
				$path = $extension.DS.$path;
			
			if(in_array($plugin[0], $plugins))
				$path = Plugin::path($plugin[0], 'webroot'.DS.$path);
			else
				$path = WWW_ROOT.$path;
						
			$paths[$k] = [$path = sprintf('%s.%s', $path, $extension), filemtime($path)];
		}
		
		return $paths;
	}
	
	/**
	 * Checks if the folder is writable
	 * @return boolean
	 * @uses folder()
	 */
	public static function check() {
		return folder_is_writable(self::folder());
	}
	
	/**
     * Clears all asset files
     * @return boolean
	 * @uses check()
	 * @uses folder()
	 */
	public static function clear() {
		if(!self::check())
			return FALSE;
		
		$success = TRUE;
		
		//Deletes each file
        foreach((new Folder(self::folder()))->findRecursive() as $file)
            if(!(new File($file))->delete() && $success)
                $success = FALSE;
		
        return $success;
	}
	
	/**
	 * Gets the main folder path
	 * @return string Folder path
	 */
	public static function folder() {
		return WWW_ROOT.'assets'.DS;
	}

	/**
	 * Gets the asset for a path. The asset will be created, if doesn't exist
     * @param string|array $path String or array of css/js files
	 * @param string $extension Extension (`css` or `js`)
	 * @return string
	 * @see https://github.com/jakubpawlowicz/clean-css clean-css
	 * @see https://github.com/mishoo/UglifyJS2 UglifyJS
	 * @throws InternalErrorException
	 * @uses _parsePaths()
	 */
	public static function get($path, $extension) {		
		//For each path, gets the full path and the modification time
		$path = self::_parsePath($path, $extension);
		
		//Sets asset full path (`$asset`) and www path (`$www`)
		$asset = WWW_ROOT.'assets'.DS.sprintf('%s.%s', md5(serialize($path)), $extension);
		$www = sprintf('/assets/%s.%s', md5(serialize($path)), $extension);
		
		if(!is_readable($asset)) {
			//Checks if the target directory is writeable
			if(!is_writeable($target = WWW_ROOT.'assets'))
				throw new InternalErrorException(__d('me_tools', 'The directory {0} is not writable', rtr($target)));
		
			//Reads the content of all paths
			$content = implode(PHP_EOL, array_map(function($path) { return file_get_contents($path[0]); }, $path));
						
			//Writes the file
			if(!(new \Cake\Filesystem\File($asset, TRUE, 0777))->append($content, TRUE))
				throw new InternalErrorException(__d('me_tools', 'Impossible to create the file {0}', rtr($asset)));
			
			//Compresses CSS
			if($extension == 'css' && $bin = Unix::which('cleancss'))
				exec(sprintf('%s -o %s --s0 %s', $bin, $asset, $asset));
			//Compresses JS
			elseif($extension == 'js' && $bin = Unix::which('uglifyjs'))
				exec(sprintf('%s %s --compress --mangle -o %s', $bin, $asset, $asset));
			//Else, if the file type is unknown
			else
				throw new InternalErrorException(__d('me_tools', 'The file type {0} is unknown', $mime));
		}
		
		return $www;
	}
	
    /**
     * Gets the folder size
     * @return int
	 * @uses folder()
     */
    public static function size() {
        return dirsize(self::folder());
    }
}