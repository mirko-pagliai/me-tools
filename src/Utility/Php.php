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
 * @copyright	Copyright (c) 2016, Mirko Pagliai for Nova Atlantis Ltd
 * @license		http://www.gnu.org/licenses/agpl.txt AGPL License
 * @link		http://git.novatlantis.it Nova Atlantis Ltd
 */
namespace MeTools\Utility;

/**
 * An utility to handle PHP.
 * 
 * You can use this utility by adding:
 * <code>
 * use MeTools\Utility\Php;
 * </code>
 */
class Php {
    /**
     * Checks if an extension is enabled.
     * @param string $extension Extension to be checked
     * @return mixed TRUE if the extension is enabled, FALSE otherwise. NULL if cannot check
     */
    public static function extension($extension) {
		if(!function_exists('extension_loaded'))
			return FALSE;
		
        return extension_loaded($extension);
    }
}