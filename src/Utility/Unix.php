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
 * An utility to handle Unix.
 * 
 * You can use this utility by adding:
 * <code>
 * use MeTools\Utility\Unix;
 * </code>
 */
class Unix {
	/**
	 * Checks if the current user is the root user.
	 * @return mixed TRUE if is the root user, otherwise FALSE. NULL if cannot check
	 */
	public static function is_root() {
		if(!function_exists('posix_getuid'))
			return;
		
		//`posix_getuid()` returns 0 if is the root user
		return !posix_getuid();
	}
	
	/**
	 * Executes the `whereis` command.
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
	 * Executes the `which` command.
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