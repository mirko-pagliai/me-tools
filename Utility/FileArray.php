<?php
/**
 * FileArray utility
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
 * @copyright	Copyright (c) 2013, Mirko Pagliai for Nova Atlantis Ltd
 * @license		http://www.gnu.org/licenses/agpl.txt AGPL License
 * @link		http://git.novatlantis.it Nova Atlantis Ltd
 * @package		MeTools\Utility
 */

/**
 * Provides several methods for using a text file as database.
 * It can count, delete, edit, check, search, retrieve and insert data.
 * 
 * You can use this utility by adding in your controller:
 * <code>
 * App::uses('FileArray', 'MeTools.Utility');
 * </code>
 */
class FileArray  {
	/**
	 * File content
	 * @var mixed Content or FALSE 
	 */
	private $content = FALSE;
	
	/**
	 * File object
	 * @var object File object
	 */
	private $file;
	
	/**
	 * File path
	 * @var string File path
	 */
	public $path;
	
	/**
	 * Contruct. Sets the File object and the content.
	 * It will create the file, if it doesn't exist
	 * @param string $path File path
	 * @uses __read() to reat the file content
	 * @uses content to set the file content
	 * @uses file to set the file object
	 * @uses path to set the file path
	 */
	public function __construct($path) {
		//If the file exists, it checks if that is writable. Otherwise, it checks if the directory is writable
		if(file_exists($this->path = $path)) {
			if(!is_writable($path))
				throw new InternalErrorException(__d('me_tools', 'The file %s exists, but is not writable', $path));
		}
		elseif(file_exists(dirname($path)) && !is_writable(dirname($path)))
			throw new InternalErrorException(__d('me_tools', 'The directory %s exists, but is not writable', dirname($path)));
		
		$this->file = new File($path, TRUE);
		$this->content = $this->__read();
	}
	
	/**
	 * Internal function to filter data
	 * @param array $conditions Conditions
	 * @return mixed Results that meet the conditions, otherwise an empty array
	 * @uses content to get the file content
	 */
	private function __filter($conditions = array()) {
		if(empty($this->content))
			return array();
		
		if(empty($conditions) || !is_array($conditions))
			return $this->content;
		
		$results = array();
		
		foreach($this->content as $k => $record) {
			//Note: array_diff_assoc() return an array containing all the values from array1 that are not present in array2
			//So, all values of array1 are included in array2 when array_diff_assoc() returns an empty array
			$diff = array_diff_assoc($conditions, $record);
			//If all the conditions are included in the current record
			if(!count($diff))
				$results[$k] = $record;
		}
		
		return $results;
	}
	
	/**
	 * Internal function to read data
	 * @return array Data or an empty array if the data don't exist
	 * @uses file to get the file object
	 */
	private function __read() {		
		//Gets existing data
		$data = json_decode($this->file->read(), TRUE);
		
		if(empty($data))
			return array();
		
		//Sorts and returns data
		ksort($data);
		return $data;
	}
	
	/**
	 * Internal function to write data
	 * @return boolean TRUE on success, otherwise FALSE
	 * @uses file to get the file object
	 */
	private function __write() {		
		$this->content = empty($this->content) ? NULL : $this->content;
		
		return $this->file->write(json_encode($this->content, JSON_FORCE_OBJECT));
	}
	
	/**
	 * Gets the number (count) of records
	 * @param array $conditions Conditions
	 * @return int Number of records
	 */
	public function count($conditions=array()) {		
		return count($this->__filter($conditions));
	}
	
	/**
	 * Deletes a record
	 * @param mixed $key Record key
	 * @return boolean Success
	 */
	public function delete($key) {
		if(!$this->exists($key))
			return FALSE;
		
		unset($this->content[$key]);
		
		return $this->__write();
	}
	
	/**
	 * Edits a record
	 * @param mixed $key Key
	 * @param mixed $data Data
	 * @return boolean Success
	 */
	public function edit($key, $data = array()) {
		if(!$this->exists($key))
			return FALSE;

		$this->content[$key] = $data;
		
		return $this->__write();
	}
	
	/**
	 * Checks if a key already exists
	 * @param mxied $key Key to check
	 * @return boolean TRUE if the key already exists, otherwise FALSE
	 */
	public function exists($key) {
		if(empty($this->content))
			return FALSE;
		
		return key_exists(Inflector::slug($key, '-'), $this->content);
	}
	
	/**
	 * Finds records.
	 * 
	 * The type should be "first" (default), "count" and "all" or a record key
	 * @param string $type Search type ("first", "count", "all" or a record key
	 * @return mixed Results
	 */
	public function find($type = 'first') {		
		switch($type) {
			case 'first':
				return $this->getFirst();
				break;
			case 'count':
				return $this->count();
				break;
			case 'all':
				return $this->getAll();
				break;
			default:
				return $this->findByKey($type);
				break;
		}
	}
	
	/**
	 * Finds a record by its key
	 * @param mixed $key Key
	 * @return mixed Record founded
	 */
	public function findByKey($key) {
		if(!$this->exists($key))
			return FALSE;
		
		return $this->content[$key];		
	}
	
	/**
	 * Gets all records
	 * @param array $conditions Conditions
	 * @return array All records
	 */
	public function getAll($conditions = array()) {
		return $this->__filter($conditions);
	}
	
	/**
	 * Gets the first record
	 * @param array $conditions Conditions
	 * @return array First record founded
	 */
	public function getFirst($conditions=array()) {
		$results = $this->__filter($conditions);
		return reset($results);
	}
	
	/**
	 * Inserts a new record
	 * @param mixed $data Record data
	 * @param mixed $key Record key
	 * @return boolean TRUE on success, otherwise FALSE
	 * @throws InternalErrorException
	 * @uses exists() to check if the key exists
	 * @uses content to read and set the file content
	 * @uses path to get the file path
	 */
	public function insert($data, $key = NULL) {
		if(!$this->content) {
			$this->content = array();
			$key = empty($key) ? 1 : $key;
		}
		
		if(!empty($key)) {
			if($this->exists($key = Inflector::slug($key, '-')))
				throw new InternalErrorException(__d('me_tools', 'There\'s already a record with this key in %s', $this->path));

			$this->content[$key] = $data;
		}
		else
			$this->content[] = $data;
		
		return $this->__write();
	}
}