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

use Cake\Filesystem\Folder;
use MeTools\Utility\System;

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
     * Checks if all thumbnail directories are readable and writable.
     * @return boolean TRUE if all thumbnail directories are readable and writable, FALSE otherwise
	 * @uses checkPhotos()
	 * @uses checkRemotes()
	 * @uses checkVideos()
     */
    public static function check() {
		return self::checkPhotos() && self::checkRemotes() && self::checkVideos();
    }
	
	/**
     * Checks if the photos directory is readable and writable
     * @return boolean TRUE if the photos directory is readable and writable, FALSE otherwise
	 * @uses getPhotosPath()
	 * @uses System::dirWritable()
	 */
    public static function checkPhotos() {
		return System::dirWritable(self::getPhotosPath());
	}
	
	/**
     * Checks if the remotes directory is readable and writable
     * @return boolean TRUE if the remotes directory is readable and writable, FALSE otherwise
	 * @uses getRemotesPath()
	 * @uses System::dirWritable()
	 */
    public static function checkRemotes() {
		return System::dirWritable(self::getRemotesPath());
	}
	
	/**
     * Checks if the videos directory is readable and writable
     * @return boolean TRUE if the videos directory is readable and writable, FALSE otherwise
	 * @uses getVideosPath()
	 * @uses System::dirWritable()
	 */
    public static function checkVideos() {
		return System::dirWritable(self::getVideosPath());
	}
	
    /**
     * Clears the thumbnails
     * @return boolean TRUE if the thumbnails are writable and were successfully cleared, FALSE otherwise
	 * @uses check()
     */
    public static function clear() {
		if(!self::check())
			return FALSE;
		
        $dir = new Folder(TMP.'thumbs');
		$success = TRUE;
		
		//For each file
        foreach($dir->findRecursive() as $file) {
            $file = new File($file);
			
			//Deletes the file
            if(!$file->delete() && $success)
                $success = FALSE;
        }
		
        return $success;
    }
	
	/**
	 * Gets a photo path.
	 * 
	 * If a filename is not specified, it returns the main directory path.
	 * @param string $file Filename, optional
	 * @return string Photos path
	 */
    public static function getPhotoPath($file = NULL) {
		$path =  TMP.'thumbs'.DS.'photos'.DS;
		
		if(!empty($file))
			$path .= $file;
		
		return $path;
	}
	
	/**
	 * Gets the path for a remote file.
	 * 
	 * If a filename is not specified, it returns the main directory path.
	 * @param string $file Filename, optional
	 * @return string Remotes path
	 */
    public static function getRemotePath($file = NULL) {
		$path =  TMP.'thumbs'.DS.'remotes'.DS;
		
		if(!empty($file))
			$path .= $file;
		
		return $path;
	}

    /**
     * Gets the thumbnails size
     * @return mixed Thumbnails size
	 * @uses check()
     */
    public static function getSize() {
		if(!self::check())
			return FALSE;
		
        $thumbs = new Folder(TMP.'thumbs');
        return $thumbs->dirsize();
    }
	
	/**
	 * Gets a video path.
	 * 
	 * If a filename is not specified, it returns the main directory path.
	 * @param string $file Filename, optional
	 * @return string Videos path
	 */
    public static function getVideoPath() {
		$path =  TMP.'thumbs'.DS.'videos'.DS;
		
		if(!empty($file))
			$path .= $file;
		
		return $path;
	}
}