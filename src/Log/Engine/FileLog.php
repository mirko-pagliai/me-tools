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
 * @see			http://api.cakephp.org/3.2/class-Cake.Log.Engine.FileLog.html FileLog
 */
namespace MeTools\Log\Engine;

use Cake\Filesystem\File;
use Cake\Filesystem\Folder;
use Cake\Log\Engine\FileLog as CakeFileLog;
use Cake\Network\Exception\InternalErrorException;

/**
 * File Storage stream for Logging. Writes logs to different files based on the level of log it is.
 * 
 * Rewrites {@link http://api.cakephp.org/3.2/class-Cake.Log.Engine.FileLog.html FileLog}.
 */
class FileLog extends CakeFileLog {
	/**
	 * Gets all log files
	 * @return array Log files
	 */
	public static function all() {
		//Gets log files
		$files = (new Folder(LOGS))->find('[^\.]+\.log(\.[^\-]+)?', TRUE);
		
		//For each file, the array key will be the filename without extension
		foreach($files as $k => $file) {
			$files[pathinfo($file, PATHINFO_FILENAME)] = $file;
			unset($files[$k]);
		}
		
		return $files;
	}
	
    /**
     * Checks if the logs directory is readable and writable
     * @return boolean
     */
	public static function check() {
		return folder_is_writable(LOGS);
	}
	
	/**
     * Clears all log files
     * @return boolean
	 * @uses check()
	 */
	public static function clear() {
		if(!self::check())
			return FALSE;
		
		$success = TRUE;
		
		//Deletes each file
        foreach((new Folder(LOGS))->findRecursive() as $file)
            if(!(new File($file))->delete() && $success)
                $success = FALSE;
		
        return $success;
	}
	
	/**
	 * Gets a log file
	 * @param string $log Log name
	 * @return string Log content
	 * @throws InternalErrorException
	 */
	public static function get($log) {
		if(!is_readable($file = LOGS.$log))
			throw new InternalErrorException(__d('me_tools', 'File or directory `{0}` not readable', $file));
		
		return @file_get_contents($file);
	}
	
	/**
	 * Parses a log file
	 * @param string $log Log name
	 * @return array
	 * @uses get()
	 */
	public static function parse($log) {
		return array_map(function($log) {
			preg_match('/^'.
				'([\d\-]+\s[\d:]+)\s(Error: Fatal Error|Error|Notice: Notice|Warning: Warning)(\s\(\d+\))?:\s([^\n]+)\n'.
				'(Exception Attributes:\s((.(?!Request|Referer|Stack|Trace))+)\n)?'.
				'(Request URL:\s([^\n]+)\n)?'.
				'(Referer URL:\s([^\n]+)\n)?'.
				'(Stack Trace:\n(.+))?'.
				'(Trace:\n(.+))?(.+)?'.
			'/si', $log, $matches);
			
			switch($matches[2]) {
				case 'Error: Fatal Error':
					$type = 'fatal';
					break;
				case 'Error':
					$type = 'error';
					break;
				case 'Notice: Notice':
					$type = 'notice';
					break;
				case 'Warning: Warning':
					$type = 'warning';
					break;
				default:
					$type = 'unknown';
					break;
			}
			
			return (object) af([
				'datetime'		=> \Cake\I18n\FrozenTime::parse($matches[1]),
				'type'			=> $type,
				'error'			=> $matches[4],
				'attributes'	=> empty($matches[6]) ? NULL : $matches[6],
				'url'			=> empty($matches[9]) ? NULL : $matches[9],
				'referer'		=> empty($matches[11]) ? NULL : $matches[11],
				'stack_trace'	=> empty($matches[13]) ? (empty($matches[16]) ? NULL : $matches[16]) : $matches[13],
				'trace'			=> empty($matches[15]) ? NULL : $matches[15]
			]);
		}, af(preg_split('/[\r\n]{2,}/', self::get($log))));
	}
	
	/**
	 * Gets the logs size
	 * @return int Logs size
	 */
	public static function size() {
        return dirsize(LOGS);
	}
}