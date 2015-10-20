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
namespace MeTools\Console;

use Composer\Script\Event;
use MeTools\Utility\Thumbs;
use Exception;

/**
 * Configure paths required to find CakePHP + general filepath
 * constants
 */
require 'config/paths.php';

/**
 * Provides installation hooks for when this application is installed via
 * composer. Customize this class to suit your needs.
 */
class Installer {
	/**
	 * Assets for which create symbolic links.
	 * The key must be relative to `vendor/` and the value must be relative to `webroot/vendor/`
	 * @var array
	 */
	protected static $links = [];
	
	/**
	 * Paths to be created and made writable
	 * @var array 
	 */
	protected static $paths = [];

	/**
	 * Creates symbolic links to vendor assets
     * @param \Composer\IO\IOInterface $io IO interface to write to console
	 * @uses $links
	 */
	public static function createSymbolicLinks($io) {
		//Creates the target directory (`webroot/vendor/`)
		if(!file_exists($destinationDir = WWW_ROOT.'vendor') && mkdir($destinationDir))
			$io->write(sprintf('Created `%s` directory', str_replace(ROOT.DS, NULL, $destinationDir)));
				
		foreach(self::$links as $origin => $destination) {
			$origin = ROOT.DS.'vendor'.DS.$origin;
			$destination = $destinationDir.DS.$destination;
			
			//Returns, if the link already exists
			if(file_exists($destination))
				continue;
			
			//Creates the symbolic link
			if(@symlink($origin, $destination))
				$io->write(sprintf('Created symbolic link to `%s`', str_replace(ROOT.DS, NULL, $destination)));
			else
				$io->write(sprintf('<error>Failed to create a symbolic link to `%s`</error>', str_replace(ROOT.DS, NULL, $destination)));
		}
	}
	
	/**
	 * Creates some directories
     * @param \Composer\IO\IOInterface $io IO interface to write to console
	 * @uses $paths
	 */
    public static function createDirectories($io) {
		foreach(self::$paths as $path)
			if(!file_exists($path)) {
				if(mkdir($path, 0777, TRUE))
					$io->write(sprintf('Created `%s` directory', str_replace(ROOT.DS, NULL, $path)));
				else
					$io->write(sprintf('<error>Failed to create directory `%s`</error>', str_replace(ROOT.DS, NULL, $path)));	
			}
    }
	
	/**
	 * Sets permissions on directories
     * @param \Composer\IO\IOInterface $io IO interface to write to console
	 * @uses $paths
	 */
	public static function setPermissions($io) {
		foreach(self::$paths as $path)
			if(!(new \Cake\Filesystem\Folder())->chmod($path, 0777))
                $io->write(sprintf('<error>Failed to set permissions on `%s`</error>', str_replace(ROOT.DS, NULL, $path)));	
	}
	
	/**
	 * Occurs after the autoloader has been dumped, either during install/update, or via the dump-autoload command.
     * @param \Composer\Script\Event $event The composer event object
	 * @uses MeTools\Utility\Thumbs::photo()
	 * @uses MeTools\Utility\Thumbs::remote()
	 * @uses MeTools\Utility\Thumbs::video()
	 * @uses MeTools\Utility\Unix::is_root()
	 * @uses createDirectories()
	 * @uses createSymbolicLinks()
	 * @uses setPermissions()
	 * @uses $links
	 * @uses $paths
	 * @see https://getcomposer.org/doc/articles/scripts.md
	 */
	public static function postAutoloadDump(Event $event) {
		//Assets for which create symbolic links
		self::$links = array_merge(self::$links, [
			'components/bootstrap-datetimepicker/build'	=> 'bootstrap-datetimepicker',
			'components/jquery'							=> 'jquery',
			'components/moment/min'						=> 'moment',
			'fortawesome/font-awesome'					=> 'font-awesome',
			'twbs/bootstrap/dist'						=> 'bootstrap'
		]);
		
		//Paths to be created and made writable
		self::$paths = array_merge(self::$paths, [
			LOGS,
			TMP.'cache',
			TMP.'cache'.DS.'models',
			TMP.'cache'.DS.'persistent',
			TMP.'cache'.DS.'views',
			TMP.'sessions',
			TMP.'tests',
			WWW_ROOT.'files',
			dirname(Thumbs::photo()),
			Thumbs::photo(),
			Thumbs::remote(),
			Thumbs::video()
		]);
		
        $io = $event->getIO();
		
		//Checks if the current user is the root user
		if(!\MeTools\Utility\Unix::is_root()) {
			$io->write('<error>You have to run this command as root user or using sudo</error>');
			exit;
		}
				
		//Creates some directories
        self::createDirectories($io);
		
		//If the shell is interactive
        if($io->isInteractive()) {
            $validator = function($arg) {
                if(in_array($arg, ['Y', 'y', 'N', 'n']))
                    return $arg;
				
                throw new Exception('This is not a valid answer. Please choose Y or n.');
            };
			
			//Asks if the permissions should be changed
            $ask = $io->askAndValidate('<info>Set folder permissions? (Default to Y)</info> [<comment>Y, n</comment>]? ', $validator, 10, 'Y');

            if(in_array($ask, ['Y', 'y']))
                self::setPermissions($io);
			
			//Asks if the symbolic links to vendors should be created
			$ask = $io->askAndValidate('<info>Create symbolic links to vendors? (Default to Y)</info> [<comment>Y, n</comment>]? ', $validator, 10, 'Y');
			
            if(in_array($ask, ['Y', 'y']))
				self::createSymbolicLinks($io);
        }
		else {
            self::setPermissions($io);
			self::createSymbolicLinks($io);
		}
	}
}