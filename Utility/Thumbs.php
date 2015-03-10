<?php
/**
 * Thumbs utility
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
 * @package		MeTools\Utility
 */

App::uses('System', 'MeTools.Utility');

/**
 * An utility to handle the thumbnails.
 * 
 * You can use this utility by adding in your controller:
 * <code>
 * App::uses('Thumbs', 'MeTools.Utility');
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
	 * @uses System::dirIsWritable()
	 */
    public static function checkPhotos() {
		return System::dirIsWritable(self::getPhotosPath());
	}
	
	/**
     * Checks if the remotes directory is readable and writable
     * @return boolean TRUE if the remotes directory is readable and writable, FALSE otherwise
	 * @uses getRemotesPath()
	 * @uses System::dirIsWritable()
	 */
    public static function checkRemotes() {
		return System::dirIsWritable(self::getRemotesPath());
	}
	
	/**
     * Checks if the videos directory is readable and writable
     * @return boolean TRUE if the videos directory is readable and writable, FALSE otherwise
	 * @uses getVideosPath()
	 * @uses System::dirIsWritable()
	 */
    public static function checkVideos() {
		return System::dirIsWritable(self::getVideosPath());
	}
	
	/**
	 * Gets the photos path
	 * @return string Photos path
	 */
    public static function getPhotosPath() {
		return TMP.'thumbs'.DS.'photos';
	}
	
	/**
	 * Gets the remotes path
	 * @return string Remotes path
	 */
    public static function getRemotesPath() {
		return TMP.'thumbs'.DS.'remotes';
	}
	
	/**
	 * Gets the videos path
	 * @return string Videos path
	 */
    public static function getVideosPath() {
		return TMP.'thumbs'.DS.'videos';
	}
}