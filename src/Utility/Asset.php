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
     * Gets the folder size
     * @return int
	 * @uses folder()
     */
    public static function size() {
        return (new Folder(self::folder()))->dirsize();
    }
}