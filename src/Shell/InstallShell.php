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
namespace MeTools\Shell;

use Cake\Filesystem\Folder;
use MeTools\Shell\AppShell;
use MeTools\Utility\Thumbs;

/**
 * Install shell command
 * <code>
 * bin/cake MeTools.install
 * </code>
 */
class InstallShell extends AppShell {
    /**
     * Performs all operations
	 * @uses folders()
     */
    public function all() {
		//Installs folders
		$this->folders();
    }
	
	/**
	 * Installs folders
	 * <code>
	 * bin/cake MeTools.install folders
	 * </code>
	 * @used Thumbs::photo()
	 * @used Thumbs::remote()
	 * @used Thumbs::video()
	 */
	public function folders() {
		$paths = [];
		
		//Adds thumbnails directories
		$paths += [Thumbs::photo(), Thumbs::remote(), Thumbs::video()];
		
		foreach($paths as $path) {
			//Skip if exists and is writeable
			if(is_writable($path))
				continue;
			
			$folder = new Folder();
			
			//Makes the directory as writable, if it's readable but not writable
			if(is_readable($path) && !is_writable($path)) {
				if($folder->chmod($path, 0777))
					$this->success(__d('me_tools', '{0} has been made writeable', $path));
				else
					$this->error(__d('me_tools', '{0} has not been made writeable', $path));
			}
			//Creates the directory, if it doesn't exist or is not readable
			elseif(!file_exists($path) || !is_readable($path)) {
				if($folder->create($path, 0777))
					$this->success(__d('me_tools', '{0} was created', $path));
				else
					$this->error(__d('me_tools', '{0} was not created', $path));
			}
			//In any other case, error
			else
				$this->error(__d('me_tools', 'Cannot create or modify the directory {0}', $path));
		}
	}
	
	/**
	 * Gets the option parser instance and configures it
	 * @return Cake\Console\ConsoleOptionParser ConsoleOptionParser
	 * @see http://api.cakephp.org/3.0/class-Cake.Console.Shell.html#_getOptionParser
	 */
	public function getOptionParser() {
		$parser = parent::getOptionParser();
		
		$parser->addSubcommands([
			'all'		=> ['help' => __d('me_tools', 'it performs all operations')],
			'folders'	=> ['help' => __d('me_tools', 'it install folders')]
		])->description(__d('me_tools', 'Performs various operations to install the application'));
		
		return $parser;
	}
}