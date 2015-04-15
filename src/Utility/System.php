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

use Cake\Filesystem\Folder;
use MeTools\Utility\MePlugin;

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
     * Alias for `getChangelogs()` method.
     * @see getChangelogs()
     */
    public static function changelogs() {
        return call_user_func_array([get_class(), 'getChangelogs'], func_get_args());
    }
	
	/**
	 * Checks if a directory and its subdirectories are readable and writable
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
     * Alias for `dirIsWritable()` method.
     * @see dirIsWritable()
     */
    public static function dirWritable() {
        return call_user_func_array([get_class(), 'dirIsWritable'], func_get_args());
    }
	
	/**
	 * Gets all changelog files. 
	 * 
	 * It searchs into `ROOT` and all loaded plugins.
	 * @uses MeTools\Utility\MePlugin::getPath()
	 * @return array Changelog files
	 */
	public static function getChangelogs() {
		//Set paths
		$paths = am([ROOT.DS], MePlugin::getPath());
		
		//Gets changelog files
		$files = ac(array_map(function($path) {
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