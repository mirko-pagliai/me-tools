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
}