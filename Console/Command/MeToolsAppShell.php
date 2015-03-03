<?php
/**
 * MeToolsAppShell
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
 * @package		MeTools\Console\Command
 */

App::uses('Unix', 'MeTools.Utility');

/**
 * Application level shell.
 */
class MeToolsAppShell extends Shell {
	/**
	 * Displays a header for the shell.
	 * 
	 * This method only resets the welcome message.
	 */
	protected function _welcome() { }
	
	/**
	 * Checks if the current user is the root user.
	 * @uses Unix::is_root()
	 */
	protected function is_root() {
		//Checks if is the root user
		if(!Unix::is_root())
			$this->error('this shell needs to be run by root (or using sudo)!');
	}
}