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

use App\Console\Installer as AppInstaller;
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
class Installer extends AppInstaller {
	/**
	 * Assets for which create symbolic links.
	 * The key must be relative to `vendor/`, the value must be relative to `webroot/vendor/`
	 * @see createSymbolicLinkToVendor()
	 * @var array
	 */
	protected static $linksToAssets = [
		'components/bootstrap-datetimepicker/build'	=> 'bootstrap-datetimepicker',
		'components/jquery'							=> 'jquery',
		'components/moment/min'						=> 'moment',
		'fortawesome/font-awesome'					=> 'font-awesome',
		'twbs/bootstrap/dist'						=> 'bootstrap'
	];

	/**
	 * Creates a symbolic link to vendor asset.
	 * 
	 * For example:
	 * <pre>createSymbolicLinkToVendor('components/jquery', 'jquery');</pre>
	 * will create a symbolic link from `vendor/components/jquery` to `webroot/vendor/jquery`.
	 * @param string $from Name, relative to `vendor/`
	 * @param string $to Name, relative to `webroot/vendor/`
     * @param \Composer\Script\Event $event The composer event object
	 */
	public static function createSymbolicLinkToVendor($from, $to, $event) {
		$io = $event->getIO();
		
		//Creates the target directory (`webroot/vendor/`)
		if(!file_exists($webrootDir = ROOT.DS.'webroot'.DS.'vendor') && mkdir($webrootDir))
			$io->write(sprintf('Created `%s` directory', str_replace(ROOT, NULL, $webrootDir)));
		
		//Deletes the link, if it already exists
		if(file_exists($to = $webrootDir.DS.$to))
			unlink($to);
		
		$from = $event->getComposer()->getConfig()->get('vendor-dir').DS.$from;
				
		//Creates the symbolic link
		if(@symlink($from, $to))
			$io->write(sprintf('Created symbolic link to `%s`', str_replace(ROOT, NULL, $to)));

		else
			$io->write(sprintf('Failed to create a symbolic link to `%s`', str_replace(ROOT, NULL, $to)));
	}
	
	/**
	 * Creates some (writable) directories
     * @param string $dir The application's root directory
     * @param \Composer\IO\IOInterface $io IO interface to write to console
	 * @uses App\Console\Installer::createWritableDirectories()
	 * @uses MeTools\Utility\Thumbs::photo()
	 * @uses MeTools\Utility\Thumbs::remote()
	 * @uses MeTools\Utility\Thumbs::video()
	 */
    public static function createWritableDirectories($dir, $io) {
        foreach([
			WWW_ROOT.'files',
			dirname(Thumbs::photo()),
			Thumbs::photo(),
			Thumbs::remote(),
			Thumbs::video()
		] as $path)
			if(!file_exists($path) && mkdir($path))
				$io->write(sprintf('Created `%s` directory', str_replace(ROOT, NULL, $path)));
				
		//Creates `logs` and `tmp` directories
		parent::createWritableDirectories($dir, $io);
    }
	
	/**
	 * Occurs after the autoloader has been dumped, either during install/update, or via the dump-autoload command.
     * @param \Composer\Script\Event $event The composer event object
	 * @uses linksToAssets
	 * @uses createSymbolicLinkToVendor()
	 * @uses createWritableDirectories()
	 * @uses setFolderPermissions()
	 * @see https://getcomposer.org/doc/articles/scripts.md
	 * @todo Needs to check for root/sudo
	 */
	public static function postAutoloadDump(Event $event) {
        $io = $event->getIO();
		
		//Creates some (writable) directories
        static::createWritableDirectories(ROOT, $io);
		
		//If the shell is interactive
        if($io->isInteractive()) {
            $validator = function($arg) {
                if(in_array($arg, ['Y', 'y', 'N', 'n']))
                    return $arg;
				
                throw new Exception('This is not a valid answer. Please choose Y or n.');
            };
			
			//Asks if the permissions should be changed
            $setFolderPermissions = $io->askAndValidate('<info>Set folder permissions? (Default to Y)</info> [<comment>Y, n</comment>]? ', $validator, 10, 'Y');

            if(in_array($setFolderPermissions, ['Y', 'y']))
                self::setFolderPermissions(ROOT, $io);
			
			//Asks if the symbolic links to vendors should be created
			$createSymbolicLinkToVendor = $io->askAndValidate('<info>Create symbolic links to vendors? (Default to Y)</info> [<comment>Y, n</comment>]? ', $validator, 10, 'Y');
			
            if(in_array($createSymbolicLinkToVendor, ['Y', 'y']))
				foreach(self::$linksToAssets as $from => $to)
					self::createSymbolicLinkToVendor($from, $to, $event);
        }
		else {
            self::setFolderPermissions(ROOT, $io);

			foreach(self::$linksToAssets as $from => $to)
				self::createSymbolicLinkToVendor($from, $to, $event);
		}
	}

    /**
     * Set globally writable permissions on some directories
     * @param string $dir The application's root directory
     * @param \Composer\IO\IOInterface $io IO interface to write to console
     */
    public static function setFolderPermissions($dir, $io) {
        //Change the permissions on a path and output the results
        $changePerms = function ($path, $perms, $io) {
            //Get current permissions in decimal format so we can bitmask it
            $currentPerms = octdec(substr(sprintf('%o', fileperms($path)), -4));
            if(($currentPerms & $perms) == $perms)
                return;
			
            $res = chmod($path, $currentPerms | $perms);
            if($res)
                $io->write('Permissions set on '.$path);
            else
                $io->write('Failed to set permissions on '.$path);
        };

        $walker = function ($dir, $perms, $io) use (&$walker, $changePerms) {
            $files = array_diff(scandir($dir), ['.', '..']);
            foreach ($files as $file) {
                $path = $dir.'/'.$file;

                if (!is_dir($path))
                    continue;

                $changePerms($path, $perms, $io);
                $walker($path, $perms, $io);
            }
        };

        $worldWritable = bindec('0000000111');
        $walker($dir.'/tmp', $worldWritable, $io);
        $changePerms($dir.'/tmp', $worldWritable, $io);
        $changePerms($dir.'/logs', $worldWritable, $io);
        $changePerms($dir.'/webroot/files', $worldWritable, $io);
    }
}