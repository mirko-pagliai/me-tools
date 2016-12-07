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
 * @author      Mirko Pagliai <mirko.pagliai@gmail.com>
 * @copyright   Copyright (c) 2016, Mirko Pagliai for Nova Atlantis Ltd
 * @license     http://www.gnu.org/licenses/agpl.txt AGPL License
 * @link        http://git.novatlantis.it Nova Atlantis Ltd
 * @see         http://api.cakephp.org/3.3/class-Cake.Console.Shell.html Shell
 */
namespace MeTools\Console;

use Cake\Console\Shell as CakeShell;

/**
 * Base class for command-line utilities for automating programmer chores.
 *
 * Rewrites {@link http://api.cakephp.org/3.3/class-Cake.Console.Shell.html Shell}.
 */
class Shell extends CakeShell
{
    /**
     * Rewrites the header for the shell
     * @return void
     */
    protected function _welcome()
    {
    }

    /**
     * Copies a file
     * @param string $source Source file
     * @param string $dest Destination
     * @return bool
     */
    public function copyFile($source, $dest)
    {
        //Checks if the source is readable
        if (!is_readable($source)) {
            $this->err(__d('me_tools', 'File or directory {0} not readable', rtr($source)));

            return false;
        }

        //Checks if the destination file already exists
        if (file_exists($dest)) {
            $this->verbose(__d('me_tools', 'File or directory {0} already exists', rtr($dest)));

            return false;
        }

        //@codingStandardsIgnoreLine
        $copy = @copy($source, $dest);

        if (!$copy) {
            $this->err(__d('me_tools', 'File {0} has not been copied', rtr($dest)));

            return false;
        }

        $this->verbose(__d('me_tools', 'File {0} has been copied', rtr($dest)));

        return true;
    }

    /**
     * Creates a file at given path
     * @param string $path Where to put the file
     * @param string $contents Content to put in the file
     * @return bool
     */
    public function createFile($path, $contents)
    {
        //Checks if the file already exist
        if (file_exists($path)) {
            $this->verbose(__d('me_tools', 'File or directory {0} already exists', rtr($path)));

            return false;
        }

        return parent::createFile($path, $contents);
    }

    /**
     * Creates a symbolic link
     * @param string $origin Origin file or directory
     * @param string $target Target link
     * @return bool
     */
    public function createLink($origin, $target)
    {
        //Checks if the origin file/directory is readable
        if (!is_readable($origin)) {
            $this->err(__d('me_tools', 'File or directory {0} not readable', rtr($origin)));

            return false;
        }

        //Checks if the link already exists
        if (file_exists($target)) {
            $this->verbose(__d('me_tools', 'File or directory {0} already exists', rtr($target)));

            return false;
        }

        //Checks if the target directory is writeable
        if (!is_writable(dirname($target))) {
            $this->err(__d('me_tools', 'File or directory {0} not writeable', rtr(dirname($target))));

            return false;
        }

        return symlink($origin, $target);
    }

    /**
     * Convenience method for out() that wraps message between <comment /> tag
     * @param string|array|null $message A string or an array of strings to
     *  output
     * @param int $newlines Number of newlines to append
     * @param int $level The message's output level, see above
     * @return int|bool Returns the number of bytes returned from writing to
     *  stdout
     */
    public function comment($message = null, $newlines = 1, $level = Shell::NORMAL)
    {
        return parent::out(sprintf('<comment>%s</comment>', $message), $newlines, $level);
    }

    /**
     * Convenience method for out() that wraps message between <question /> tag
     * @param string|array|null $message A string or an array of strings to
     *  output
     * @param int $newlines Number of newlines to append
     * @param int $level The message's output level, see above
     * @return int|bool Returns the number of bytes returned from writing to
     *  stdout
     */
    public function question($message = null, $newlines = 1, $level = Shell::NORMAL)
    {
        return parent::out(sprintf('<question>%s</question>', $message), $newlines, $level);
    }

    /**
     * Convenience method for err() that wraps message between <warning /> tag
     * @param string|array|null $message A string or an array of strings to
     *  output
     * @param int $newlines Number of newlines to append
     * @return int|bool Returns the number of bytes returned from writing to
     *  stdout
     */
    public function warning($message = null, $newlines = 1)
    {
        return parent::warn($message, $newlines);
    }
}
