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

use BadMethodCallException;
use Cake\Console\Shell as CakeShell;
use Cake\Filesystem\Folder;
use Exception;

/**
 * Base class for command-line utilities for automating programmer chores
 * @method int|bool comment(string|array|null $message = null, int $newlines = 1, int $level = Shell::NORMAL) Convenience method for out() that wraps message between <comment /> tag
 * @method int|bool question(string|array|null $message = null, int $newlines = 1, int $level = Shell::NORMAL) Convenience method for out() that wraps message between <question /> tag
 */
class Shell extends CakeShell
{
    /**
     * Magic method. It provides `comment()` and `question()` methods
     * @param string $name Method name
     * @param array $arguments Method arguments
     * @return int|bool The number of bytes returned from writing to stdout
     * @since 2.18.0
     * @throws BadMethodCallException
     */
    public function __call($name, $arguments)
    {
        if (in_array($name, ['comment', 'question'])) {
            $arguments[0] = sprintf('<%s>%s</%s>', $name, empty($arguments[0]) ? null : $arguments[0], $name);

            return call_user_func_array([$this, 'out'], $arguments);
        }

        throw new BadMethodCallException(sprintf('The `%s` method does not exist', $name));
    }

    /**
     * Rewrites the header for the shell
     * @return void
     */
    protected function _welcome()
    {
    }

    /**
     * Internal method to check if a file already exists and output a warning at
     *  the verbose level
     * @param string $path Path
     * @return bool
     */
    protected function verboseIfFileExists($path)
    {
        if (!file_exists($path)) {
            return false;
        }

        $this->verbose(__d('me_tools', 'File or directory `{0}` already exists', rtr($path)));

        return true;
    }

    /**
     * Copies a file
     * @param string $source Source file
     * @param string $dest Destination file
     * @return bool
     * @uses verboseIfFileExists()
     */
    public function copyFile($source, $dest)
    {
        if ($this->verboseIfFileExists($dest)) {
            return false;
        }

        //Checks if the source is readable and the destination is writable
        try {
            is_readable_or_fail($source);
            is_writable_or_fail(dirname($dest));
        } catch (Exception $e) {
            $this->err($e->getMessage());

            return false;
        }

        $this->verbose(__d('me_tools', 'File `{0}` has been copied', rtr($dest)));

        return safe_copy($source, $dest);
    }

    /**
     * Creates a directory.
     *
     * This method creates directories recursively.
     * @param string $path Directory path
     * @return bool
     * @uses folderChmod()
     * @uses verboseIfFileExists()
     */
    public function createDir($path)
    {
        if ($this->verboseIfFileExists($path)) {
            return false;
        }

        if (!safe_mkdir($path, 0777, true)) {
            $this->err(__d('me_tools', 'Failed to create file or directory `{0}`', rtr($path)));

            return false;
        }

        $this->verbose(__d('me_tools', 'Created `{0}` directory', rtr($path)));
        $this->folderChmod($path);

        return true;
    }

    /**
     * Creates a file at given path
     * @param string $path Where to put the file
     * @param string $contents Content to put in the file
     * @return bool
     * @uses verboseIfFileExists()
     */
    public function createFile($path, $contents)
    {
        return $this->verboseIfFileExists($path) ? false : parent::createFile($path, $contents);
    }

    /**
     * Creates a symbolic link
     * @param string $source Source file or directory
     * @param string $dest Destination file or directory
     * @return bool
     */
    public function createLink($source, $dest)
    {
        if ($this->verboseIfFileExists($dest)) {
            return false;
        }

        //Checks if the source is readable and the destination directory is writable
        try {
            is_readable_or_fail($source);
            is_writable_or_fail(dirname($dest));
        } catch (Exception $e) {
            $this->err($e->getMessage());

            return false;
        }

        $this->verbose(__d('me_tools', 'Link `{0}` has been created', rtr($dest)));

        return safe_symlink($source, $dest);
    }

    /**
     * Sets folder chmods.
     *
     * This method applies permissions recursively.
     * @param string $path Folder path
     * @param int $chmod Chmod
     * @return bool
     */
    public function folderChmod($path, $chmod = 0777)
    {
        if (!(new Folder())->chmod($path, $chmod, true)) {
            $this->err(__d('me_tools', 'Failed to set permissions on `{0}`', rtr($path)));

            return false;
        }

        $this->verbose(__d('me_tools', 'Setted permissions on `{0}`', rtr($path)));

        return true;
    }

    /**
     * Checks if a param exists
     * @param string $param Param name
     * @return bool
     * @since 2.17.2
     */
    public function hasParam($param)
    {
        return array_key_exists($param, $this->params);
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
