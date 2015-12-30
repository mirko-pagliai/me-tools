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
 * @see			http://api.cakephp.org/3.1/class-Cake.Console.Shell.html Shell
 */
namespace MeTools\Console;

use Cake\Console\Shell as CakeShell;

/**
 * Base class for command-line utilities for automating programmer chores.
 * 
 * Rewrites {@link http://api.cakephp.org/3.1/class-Cake.Console.Shell.html Shell}.
 */
class Shell extends CakeShell {
	/**
	 * Rewrites the header for the shell
	 */
	protected function _welcome() { }
	
	/**
	 * Output a comment message
	 * @param string|array|null $message A string or an array of strings to output
	 * @param int $newlines Number of newlines to append
	 * @param int $level The message's output level, see above
	 * @return int|bool Returns the number of bytes returned from writing to stdout
	 * @see http://api.cakephp.org/3.1/class-Cake.Console.Shell.html#_out
	 * @uses Cake\Console\Shell::out()
	 */
	protected function comment($message = NULL, $newlines = 1, $level = Shell::NORMAL) {
		return parent::out(sprintf('<comment>%s</comment>', $message), $newlines, $level);
	}
	
	/**
	 * Output an info message
	 * @param string|array|null $message A string or an array of strings to output
	 * @param int $newlines Number of newlines to append
	 * @param int $level The message's output level, see above
	 * @return int|bool Returns the number of bytes returned from writing to stdout
	 * @see http://api.cakephp.org/3.1/class-Cake.Console.Shell.html#_out
	 * @uses Cake\Console\Shell::out()
	 */
	protected function info($message = NULL, $newlines = 1, $level = Shell::NORMAL) {
		return parent::out(sprintf('<info>%s</info>', $message), $newlines, $level);
	}
	
	/**
	 * Output a question message
	 * @param string|array|null $message A string or an array of strings to output
	 * @param int $newlines Number of newlines to append
	 * @param int $level The message's output level, see above
	 * @return int|bool Returns the number of bytes returned from writing to stdout
	 * @see http://api.cakephp.org/3.1/class-Cake.Console.Shell.html#_out
	 * @uses Cake\Console\Shell::out()
	 */
	protected function question($message = NULL, $newlines = 1, $level = Shell::NORMAL) {
		return parent::out(sprintf('<question>%s</question>', $message), $newlines, $level);
	}
	
	/**
	 * Output a success message
	 * @param string|array|null $message A string or an array of strings to output
	 * @param int $newlines Number of newlines to append
	 * @param int $level The message's output level, see above
	 * @return int|bool Returns the number of bytes returned from writing to stdout
	 * @see http://api.cakephp.org/3.1/class-Cake.Console.Shell.html#_out
	 * @uses Cake\Console\Shell::out()
	 */
	protected function success($message = NULL, $newlines = 1, $level = Shell::NORMAL) {
		return parent::out(sprintf('<success>%s</success>', $message), $newlines, $level);
	}
	
	/**
	 * Output a warning message
	 * @param string|array|null $message A string or an array of strings to output
	 * @param int $newlines Number of newlines to append
	 * @param int $level The message's output level, see above
	 * @return int|bool Returns the number of bytes returned from writing to stdout
	 * @see http://api.cakephp.org/3.1/class-Cake.Console.Shell.html#_out
	 * @uses Cake\Console\Shell::out()
	 */
	protected function warning($message = NULL, $newlines = 1, $level = Shell::NORMAL) {
		return parent::out(sprintf('<warning>%s</warning>', $message), $newlines, $level);
	}
}