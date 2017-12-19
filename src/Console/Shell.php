<?php
/**
 * This file is part of me-tools.
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright   Copyright (c) Mirko Pagliai
 * @link        https://github.com/mirko-pagliai/me-tools
 * @license     https://opensource.org/licenses/mit-license.php MIT License
 * @see         http://api.cakephp.org/3.4/class-Cake.Console.Shell.html Shell
 */
namespace MeTools\Console;

use Cake\Console\Shell as CakeShell;
use Cake\Filesystem\Folder;

/**
 * Base class for command-line utilities for automating programmer chores
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

        //Checks if the destination directory is writeable
        if (!is_writable(dirname($dest))) {
            $this->err(__d('me_tools', 'File or directory {0} not writeable', rtr(dirname($dest))));

            return false;
        }

        copy($source, $dest);

        $this->verbose(__d('me_tools', 'File {0} has been copied', rtr($dest)));

        return true;
    }

    /**
     * Creates a directory.
     *
     * This method creates directories recursively.
     * @param string $path Directory path
     * @return bool
     * @uses folderChmod()
     */
    public function createDir($path)
    {
        if (file_exists($path)) {
            $this->verbose(__d('me_tools', 'File or directory {0} already exists', rtr($path)));

            return false;
        }

        //@codingStandardsIgnoreLine
        $success = @mkdir($path, 0777, true);

        if (!$success) {
            $this->err(__d('me_tools', 'Failed to create file or directory {0}', rtr($path)));

            return false;
        }

        $this->verbose(__d('me_tools', 'Created {0} directory', rtr($path)));

        return $this->folderChmod($path, 0777);
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

        symlink($origin, $target);

        $this->verbose(__d('me_tools', 'Link {0} has been created', rtr($target)));

        return true;
    }

    /**
     * Sets folder chmods.
     *
     * This method applies permissions recursively.
     * @param string $path Folder path
     * @param int $chmod Chmod
     * @return bool
     */
    public function folderChmod($path, $chmod)
    {
        if (!(new Folder())->chmod($path, $chmod, true)) {
            $this->err(__d('me_tools', 'Failed to set permissions on {0}', rtr($path)));

            return false;
        }

        $this->verbose(__d('me_tools', 'Setted permissions on {0}', rtr($path)));

        return true;
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
