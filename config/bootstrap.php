<?php
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

if(!function_exists('ac')) {
	/**
	 * Cleans an array.
	 * Removes empty values (`array_filter()`) and duplicates (`ærray_unique()`)
	 * @param array $array Array
	 * @return array Array
	 */
	function ac($array) {
		return is_array($array) ? array_unique(array_filter($array)) : $array;
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