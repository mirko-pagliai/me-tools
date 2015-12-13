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
namespace MeTools\Utility;

use Cake\Filesystem\File;
use Cake\Filesystem\Folder;

/**
 * An utility to handle thumbnails.
 * 
 * You can use this utility by adding:
 * <code>
 * use MeTools\Utility\Thumbs;
 * </code>
 */
class Thumbs {
    /**
     * Checks if all thumbnail directories are readable and writable
     * @return boolean
	 * @uses checkPhotos()
	 * @uses checkRemotes()
	 * @uses checkVideos()
     */
    public static function checkAll() {
		return self::checkPhotos() && self::checkRemotes() && self::checkVideos();
    }
	
	/**
     * Checks if the photos directory is readable and writable
     * @return boolean
	 * @uses photo()
	 */
    public static function checkPhotos() {
		return folder_is_writable(self::photo());
	}
	
	/**
     * Checks if the remotes directory is readable and writable
     * @return boolean
	 * @uses remote()
	 */
    public static function checkRemotes() {
		return folder_is_writable(self::remote());
	}
	
	/**
     * Checks if the videos directory is readable and writable
     * @return boolean
	 * @uses video()
	 */
    public static function checkVideos() {
		return folder_is_writable(self::video());
	}
	
    /**
     * Clears the thumbnails
     * @return boolean
	 * @uses checkAll()
     */
    public static function clear() {
		if(!self::checkAll())
			return FALSE;
		
		$success = TRUE;
		
		//Deletes each file
        foreach((new Folder(TMP.'thumbs'))->findRecursive() as $file)
            if(!(new File($file))->delete() && $success)
                $success = FALSE;
		
        return $success;
    }
	
	/**
	 * Gets a photo path.
	 * 
	 * If a filename is not specified, it returns the main directory path
	 * @param string $file Filename (optional)
	 * @return string
	 */
    public static function photo($file = NULL) {
		$path =  TMP.'thumbs'.DS.'photos'.DS;
		
		return empty($file) ? $path : $path.$file;
	}
	
	/**
	 * Gets the path for a remote file.
	 * 
	 * If a filename is not specified, it returns the main directory path
	 * @param string $file Filename (optional)
	 * @return string
	 */
    public static function remote($file = NULL) {
		$path =  TMP.'thumbs'.DS.'remotes'.DS;
		
		return empty($file) ? $path : $path.$file;
	}

    /**
     * Gets the thumbnails size
     * @return int Thumbnails size
     */
    public static function size() {
        return dirsize(TMP.'thumbs');
    }
	
	/**
	 * Gets a video path.
	 * 
	 * If a filename is not specified, it returns the main directory path
	 * @param string $file Filename (optional)
	 * @return string
	 */
    public static function video($file = NULL) {
		$path =  TMP.'thumbs'.DS.'videos'.DS;
		
		return empty($file) ? $path : $path.$file;
	}
}