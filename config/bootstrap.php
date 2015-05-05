<?php

if(!function_exists('addDefault')) {
    /**
     * Alias for `addOptionDefault()` function
     */
	function addDefault() {
		return call_user_func_array('addOptionDefault', func_get_args());
	}
}

if(!function_exists('addValue')) {
    /**
     * Alias for `addOptionValue()` function
     */
	function addValue() {
		return call_user_func_array('addOptionValue', func_get_args());
	}
}

if(!function_exists('addOptionDefault')) {
	/**
	 * Adds a default value to an option
	 * @param string $name Option name
	 * @param string $value Option value
	 * @param array $options Options
	 * @return array Options
	 */
	function addOptionDefault($name, $value, $options) {
		$options[$name] = empty($options[$name]) ? $value : $options[$name];
		
		return $options;
	}
}

if(!function_exists('addOptionValue')) {
	/**
	 * Adds the value to an option
	 * @param string $name Option name
	 * @param string $values Option values
	 * @param array $options Options
	 * @return array Options
	 */
	function addOptionValue($name, $values, $options) {
		//If values are an array or multiple arrays, turns them into a string
		if(is_array($values))
			$values = implode(' ', array_map(function($v) {
				return is_array($v) ? implode(' ', $v) : $v;
			}, $values));
								
		//Merges passed values with current values
		$values = empty($options[$name]) ? explode(' ', $values) : am(explode(' ', $options[$name]), explode(' ', $values));
		
		//Removes empty values and duplicates, then turns into a string
		$options[$name] = implode(' ', array_unique(array_filter($values)));
		
		return $options;
	}
}

if(!function_exists('af')) {
	/**
	 * Cleans an array, removing empty values (`array_filter()`)
	 * @param array $array Array
	 * @return array Array
	 */
	function af($array) {
		return is_array($array) ? array_filter($array) : $array;
	}
}

if(!function_exists('am')) {
	/**
	* Merge a group of arrays.
	* Accepts variable arguments. Each argument will be converted into an array and then merged.
	* @return array All array parameters merged into one
	*/
	function am() {
		foreach(func_get_args() as $arg)
			$array = array_merge(empty($array) ? [] : $array, is_array($arg) ? $arg : [$arg]);
		
		return $array;
	}
}

if(!function_exists('folder_is_writable')) {
	/**
	 * Checks if a directory and its subdirectories are readable and writable
	 * @param string $dir Directory path
	 * @return boolean
	 */
	function folder_is_writable($dir) {
		if(!is_readable($dir) || !is_writable($dir))
			return FALSE;
		
		$folder = new \Cake\Filesystem\Folder();

        foreach($folder->tree($dir, FALSE, 'dir') as $subdir)
            if(!is_readable($subdir) || !is_writable($subdir))
                return FALSE;

        return TRUE;
	}
}

if(!function_exists('folder_is_writeable')) {
    /**
     * Alias for `folder_is_writable()` function
     */
	function folder_is_writeable() {
		return call_user_func_array('folder_is_writable', func_get_args());
	}
	
}

if(!function_exists('fv')) {
	/**
	 * Returns the first value of an array
	 * @param array $array Array
	 * @return mixed First value
	 */
	function fv($array) {
		if(empty($array) || !is_array($array))
			return NULL;
		
		return array_values($array)[0];
	}
}