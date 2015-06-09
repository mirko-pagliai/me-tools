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
     * Does some routine installation tasks so people don't have to
     * @param \Composer\Script\Event $event The composer event object
	 * @uses App\Console\Installer::setFolderPermissions()
	 * @uses createWritableDirectories()
	 */
    public static function postInstall(Event $event) {
        $io = $event->getIO();

        static::createWritableDirectories(ROOT, $io);

        //Asks if the permissions should be changed
        if($io->isInteractive()) {
            $validator = function($arg) {
                if(in_array($arg, ['Y', 'y', 'N', 'n']))
                    return $arg;
				
                throw new Exception('This is not a valid answer. Please choose Y or n.');
            };
            $setFolderPermissions = $io->askAndValidate('<info>Set Folder Permissions ? (Default to Y)</info> [<comment>Y,n</comment>]? ', $validator, 10, 'Y');

            if(in_array($setFolderPermissions, ['Y', 'y']))
                parent::setFolderPermissions(ROOT, $io);
        }
		else
            parent::setFolderPermissions(ROOT, $io);
    }
	
	/**
	 * Creates some directories
     * @param string $dir The application's root directory
     * @param \Composer\IO\IOInterface $io IO interface to write to console
	 * @uses MeTools\Utility\Thumbs::photo()
	 * @uses MeTools\Utility\Thumbs::remote()
	 * @uses MeTools\Utility\Thumbs::video()
	 */
    public static function createWritableDirectories($dir, $io) {
        $paths = [dirname(Thumbs::photo()), Thumbs::photo(), Thumbs::remote(), Thumbs::video()];

        foreach($paths as $path)
            if(!file_exists($path) && mkdir($path))
                $io->write(sprintf('Created `%s` directory', str_replace(ROOT, NULL, $path)));
    }
}
