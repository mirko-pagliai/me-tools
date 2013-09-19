<?php

/**
 * Provides methods for using text file as database.
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
 * @package		MeTools.Utility
 */
class FileArray  {
	/**
	 * File content
	 * @var array 
	 */
	private $content = null;
	
	/**
	 * File object
	 * @var object 
	 */
	private $file = false;
	
	/**
	 * File path
	 * @var string 
	 */
	public $path = null;
	
	/**
	 * Contruct. Sets the File object, the path and the content
	 * @param string $path File path
	 */
	public function __construct($path = false) {
		//Set the File object
		$this->file = new File($path);
		//Set the path
		$this->path = $path;
		//Read the content
		$this->content = $this->__read();
	}
	
	/**
	 * Internal function to filter data
	 * @param array $conditions Conditions
	 * @return mixed Results or NULL
	 */
	private function __filter($conditions=array()) {
		//Returns all content if there're no conditions
		if(empty($conditions) || !is_array($conditions))
			return $this->content;
		
		$results = array();
		
		foreach($this->content as $k => $record) {
			//Note: array_diff_assoc() return an array containing all the values from array1 that are not present in array2
			//So, all the values of array1 are included in array2 when array_diff_assoc() returns an empty array
			$diff = array_diff_assoc($conditions, $record);
			//If all the conditions are included in the current record
			if(!count($diff))
				$results[$k] = $record;
		}
		
		return !empty($results) ? $results : null;
	}
	
	/**
	 * Internal function to read data
	 * @return mixed Existing data. NULL if there's no existing data or if the file doesn't exist
	 */
	private function __read() {
		//Returns NULL if the file doesn't exist
		if(!$this->file->exists())
			return null;
		
		//Gets existing data
		$data = json_decode($this->file->read(), true);
		
		//Returns NULL if there's no existing data
		if(empty($data))
			return null;
		
		//Sorts and return data
		ksort($data);
		return $data;
	}
	
	/**
	 * Internal function to write data
	 * @param mixed $data Data to insert
	 * @return boolean Success
	 */
	private function __write($data) {
		return $this->file->write(json_encode($data, JSON_FORCE_OBJECT));
	}
	
	/**
	 * Gets the record count
	 * @param array $conditions Conditions
	 * @return int Count
	 */
	public function count($conditions=array()) {		
		//If there're conditions
		if(!empty($conditions) && is_array($conditions))
			return count($this->__filter($conditions));
		else
			return count($this->content);
	}
	
	/**
	 * Deletes a record
	 * @param mixed $key Record key
	 * @return boolean Success
	 */
	public function delete($key) {
		//Returns FALSE if the key doesn't exists
		if(!$this->exists($key))
			return false;
		
		//Unsets the record
		unset($this->content[$key]);
		//Writes data
		$success = $this->__write($this->content);
		//Updates the content
		$this->content = $this->__read();
		
		return $success;
	}
	
	/**
	 * Edits a record
	 * @param mixed $key Key
	 * @param mixed $data Data
	 * @return boolean Success
	 */
	public function edit($key, $data=array()) {
		//Returns FALSE if the key doesn't exists
		if(!$this->exists($key))
			return false;

		//Edits data
		$this->content[$key] = $data;
		//Writes data
		$success = $this->__write($this->content);
		//Updates the content
		$this->content = $this->__read();
		
		return $success;
	}
	
	/**
	 * Checks if exists a record with a key
	 * @param int $key Key to check
	 * @return bool TRUE if already exists, else FALSE
	 */
	public function exists($key) {
		//Returns FALSE if there's no content
		if(empty($this->content))
			return false;
		
		return key_exists($key, $this->content);
	}
	
	/**
	 * Finds records
	 * @param string $type Search type. It supports "first" (default), "count" and "all"
	 * @return mixed Results
	 */
	public function find($type='first') {		
		switch($type) {
			//Finds the first record
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
				return null;
				break;
		}
	}
	
	/**
	 * Finds a record by its key
	 * @param mixed $key Key
	 * @return mixed Record
	 */
	public function findByKey($key) {
		//Returns FALSE if the key doesn't exists
		if(!$this->exists($key))
			return false;
		
		return $this->content[$key];		
	}
	
	/**
	 * Gets all records
	 * @param array $conditions Conditions
	 * @return mixed All records or NULL
	 */
	public function getAll($conditions=array()) {
		//If there're conditions
		if(!empty($conditions) && is_array($conditions))
			return $this->__filter($conditions);
		else
			return $this->content;
	}
	
	/**
	 * Gets the first record
	 * @param array $conditions Conditions
	 * @return mixed First record founded or NULL
	 */
	public function getFirst($conditions=array()) {
		//Returns NULL if there's no content
		if(empty($this->content))
			return null;
		
		//If there're conditions
		if(!empty($conditions) && is_array($conditions)) {
			$results = $this->__filter($conditions);
			return !empty($results) ? reset($results) : null;
		}
		else
			return reset($this->content);
	}
	
	/**
	 * Inserts a new record
	 * @param mixed $key Data key
	 * @param mixed $data Data
	 * @return bool Success
	 * @throws InternalErrorException
	 */
	public function insert($key=null, $data=array()) {
		//If existing data are empty
		if(empty($this->content)) {
			//Inizializes the array
			$this->content = array();
			//If the key is empty, the key will be "1"
			$key = empty($key) ? 1 : $key;
		}
		
		//If the key is not empty
		if(!empty($key)) {
			//Checks if the key already exists
			if($this->exists($key))
				throw new InternalErrorException(__('There\'s already a record with this key in %s', $this->path));

			//Adds passed data, with their key, to existing data
			$this->content[$key] = $data;
		}
		//Else, if the key is empty, pushes passed data
		else
			array_push($this->content, $data);
		
		//Writes
		$success = $this->__write($this->content);
		//Updates the content
		$this->content = $this->__read();
		
		return $success;
	}
}