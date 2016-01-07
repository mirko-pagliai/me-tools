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

if(!function_exists('addDefault')) {
    /**
     * Alias for `addOptionDefault()` function
     */
	function addDefault() {
		return call_user_func_array('addOptionDefault', func_get_args());
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

if(!function_exists('addValue')) {
    /**
     * Alias for `addOptionValue()` function
     */
	function addValue() {
		return call_user_func_array('addOptionValue', func_get_args());
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

if(!function_exists('dirsize')) {
	/**
	 * Returns the size in bytes of a directory and its contents
	 * @param string $path Full path
	 * @return int Size in bytes
	 * @uses Cake\Filesystem\Folder::dirsize()
	 */
	function dirsize($path) {
		return (new \Cake\Filesystem\Folder($path))->dirsize();
	}
}

if(!function_exists('fk')) {
	/**
	 * Returns the first key of an array
	 * @param array $array Array
	 * @return string First key
	 */
	function fk($array) {
		if(empty($array) || !is_array($array))
			return NULL;
		
		return current(array_keys($array));
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

        foreach((new \Cake\Filesystem\Folder())->tree($dir, FALSE, 'dir') as $subdir)
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

if(!function_exists('get_client_ip')) {
	/**
	 * Gets the client IP
	 * @return string Client IP
	 * @see http://stackoverflow.com/a/15699240/1480263
	 */
	function get_client_ip() {
		if(!empty($_SERVER['HTTP_CLIENT_IP']))
			return $_SERVER['HTTP_CLIENT_IP'];
		elseif(!empty($_SERVER['HTTP_X_FORWARDED_FOR']))
			return $_SERVER['HTTP_X_FORWARDED_FOR'];
		elseif(!empty($_SERVER['HTTP_X_FORWARDED']))
			return $_SERVER['HTTP_X_FORWARDED'];
		elseif(!empty($_SERVER['HTTP_FORWARDED_FOR']))
			return $_SERVER['HTTP_FORWARDED_FOR'];
		elseif(!empty($_SERVER['HTTP_FORWARDED']))
			return $_SERVER['HTTP_FORWARDED'];
		elseif(!empty($_SERVER['REMOTE_ADDR']))
			return $_SERVER['REMOTE_ADDR'];
		else
			return 'UNKNOWN';
	}
}

if(!function_exists('is_json')) {
	/**
	 * Checks if a string is JSON
	 * @param string $string
	 * @return bool
	 */
	function is_json($string) {
		@json_decode($string);
		return json_last_error() === JSON_ERROR_NONE;
	}
}

if(!function_exists('is_localhost')) {
	/**
	 * Checks if is localhost
	 * @return bool
	 */
    function is_localhost() {		
		return empty($_SERVER['REMOTE_ADDR']) ? FALSE : in_array($_SERVER['REMOTE_ADDR'], ['127.0.0.1', '::1']);
	}
}

if(!function_exists('is_remote')) {
    /**
     * Alias for `folder_is_writable()` function
     */
	function is_remote() {
		return call_user_func_array('is_url', func_get_args());
	}
}

if(!function_exists('is_url')) {
	/**
	 * Checks whether a url is invalid
	 * @param string $url Url
	 * @return bool
	 */
	function is_url($url) {
		return (bool) preg_match("/\b(?:(?:https?|ftp):\/\/|www\.)[-a-z0-9+&@#\/%?=~_|!:,.;]*[-a-z0-9+&@#\/%=~_|]/i", $url);
	}
}

if(!function_exists('rtr')) {
	/**
	 * Returns the relative path (to the APP root) of an absolute path
	 * @param string $path Absolute path
	 * @return string Relativa path
	 */
	function rtr($path) {
		return str_replace(ROOT.DS, NULL, $path);
	}
}