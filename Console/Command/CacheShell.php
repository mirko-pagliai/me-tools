<?php
/**
 * CacheShell
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

App::uses('MeToolsAppShell', 'MeTools.Console/Command');
App::uses('System', 'MeTools.Utility');

/**
 * This shell allows you to handle the cache.
 */
class CacheShell extends MeToolsAppShell {
	/**
	 * Clears the cache.
	 * @uses System::checkCache()
	 * @uses System::clearCache()
	 */
	public function clear() {
		if(!System::checkCache())
			$this->error('The cache is not writable');
		
		if(System::clearCache())
			$this->out(sprintf('<success>%s</success>', 'The cache has been cleared'));
		else
			$this->out(sprintf('<error>%s</error>', 'The cache has not been cleared'));
			
	}
}