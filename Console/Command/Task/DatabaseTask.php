<?php

/**
 * DatabaseTask
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
 * @copyright	Copyright (c) 2014, Mirko Pagliai for Nova Atlantis Ltd
 * @license		http://www.gnu.org/licenses/agpl.txt AGPL License
 * @link		http://git.novatlantis.it Nova Atlantis Ltd
 * @package		MeTools\Console\Command\Task
 */

/**
 * A task to manage the database.
 */
class DatabaseTask extends Shell {
	/**
	 * Gets the table list.
	 * @return array tables
	 */
	private function _getTables() {
		$db = ConnectionManager::getDataSource('default');
		
		//Gets all tables
		$tables = $db->listSources();
		
		//Excludes the "sqlite_sequence" table, if it exists
		if(($key = array_search('sqlite_sequence', $tables)) !== FALSE)
			unset($tables[$key]);
		
		return $tables;
	}
	
	/**
	 * Checks if the database connection is working properly.
	 * @param boolean $out TRUE if you want to print output
	 */
	public function check($out = FALSE) {
		if(!is_readable($config = APP.'Config'.DS.'database.php'))
			$this->error(sprintf('the file %s doesn\'t exist or is not readable!', $config));
		
		try {
			ConnectionManager::getDataSource('default');
		}
		catch(Exception $connectionError) {
			$this->error('unable to connect to database!');
		}
		
		if($out)
			$this->out('<info>The database connection is working properly.</info>');
	}
	
	/**
	 * Creates the database tables.
	 * @param string $plugin Plugin to use
	 * @param boolean $out TRUE if you want to print output
	 */
	public function create($plugin = NULL, $out = FALSE) {
		if(!empty($plugin))
			$this->dispatchShell('schema', 'create', '--yes', '--quiet', '--plugin', $plugin);
		else
			$this->dispatchShell('schema', 'create', '--yes', '--quiet');
		
		if($out)
			$this->out('<info>The database tables have been created.</info>');
	}

	/**
	 * Truncates all tables in the database.
	 * @param boolean $out TRUE if you want to print output
	 */
	public function truncate($out = FALSE) {		
		foreach($this->_getTables() as $table) {
			$db = ConnectionManager::getDataSource('default');
			
			if(!$db->truncate($table))
				$this->error(sprintf('<error>The table `%s` has not been truncated.</error>', $table));
			elseif($out)
				$this->out(sprintf('The table `%s` has been truncated.', $table));
		}
		
		if($out)
			$this->out('<info>The database tables have been truncated.</info>');
	}
}