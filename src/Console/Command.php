<?php
declare(strict_types=1);
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
 * @since       2.17.6
 */
namespace MeTools\Console;

use Cake\Console\Command as CakeCommand;
use Cake\Console\ConsoleIo;
use Cake\Filesystem\Folder;
use Exception;

/**
 * Base class for console commands
 */
abstract class Command extends CakeCommand
{
    /**
     * Internal method to check if a file already exists and output a warning at
     *  the verbose level
     * @param \Cake\Console\ConsoleIo $io The console io
     * @param string $path Path
     * @return bool
     */
    protected function verboseIfFileExists(ConsoleIo $io, string $path): bool
    {
        if (!file_exists($path)) {
            return false;
        }

        $io->verbose(__d('me_tools', 'File or directory `{0}` already exists', rtr($path)));

        return true;
    }

    /**
     * Copies a file
     * @param \Cake\Console\ConsoleIo $io The console io
     * @param string $source Source file
     * @param string $dest Destination file
     * @return bool
     * @uses verboseIfFileExists()
     */
    public function copyFile(ConsoleIo $io, string $source, string $dest): bool
    {
        if ($this->verboseIfFileExists($io, $dest)) {
            return false;
        }

        //Checks if the source is readable and the destination is writable
        try {
            is_readable_or_fail($source);
            is_writable_or_fail(dirname($dest));
        } catch (Exception $e) {
            $io->error($e->getMessage());

            return false;
        }

        copy($source, $dest);
        $io->verbose(__d('me_tools', 'File `{0}` has been copied', rtr($dest)));

        return true;
    }

    /**
     * Creates a directory.
     *
     * This method creates directories recursively.
     * @param \Cake\Console\ConsoleIo $io The console io
     * @param string $path Directory path
     * @return bool
     * @uses folderChmod()
     * @uses verboseIfFileExists()
     */
    public function createDir(ConsoleIo $io, string $path): bool
    {
        if ($this->verboseIfFileExists($io, $path)) {
            return false;
        }

        if (!@mkdir($path, 0777, true)) {
            $io->error(__d('me_tools', 'Failed to create file or directory `{0}`', rtr($path)));

            return false;
        }

        $io->verbose(__d('me_tools', 'Created `{0}` directory', rtr($path)));
        $this->folderChmod($io, $path);

        return true;
    }

    /**
     * Creates a file at given path
     * @param \Cake\Console\ConsoleIo $io The console io
     * @param string $path Where to put the file
     * @param string $contents Content to put in the file
     * @return bool
     * @uses verboseIfFileExists()
     */
    public function createFile(ConsoleIo $io, string $path, string $contents): bool
    {
        return $this->verboseIfFileExists($io, $path) ? false : $io->createFile($path, $contents);
    }

    /**
     * Creates a symbolic link
     * @param \Cake\Console\ConsoleIo $io The console io
     * @param string $source Source file or directory
     * @param string $dest Destination file or directory
     * @return bool
     */
    public function createLink(ConsoleIo $io, string $source, string $dest): bool
    {
        if ($this->verboseIfFileExists($io, $dest)) {
            return false;
        }

        //Checks if the source is readable and the destination directory is writable
        try {
            is_readable_or_fail($source);
            is_writable_or_fail(dirname($dest));
        } catch (Exception $e) {
            $io->error($e->getMessage());

            return false;
        }

        symlink($source, $dest);
        $io->verbose(__d('me_tools', 'Link `{0}` has been created', rtr($dest)));

        return true;
    }

    /**
     * Sets folder chmods.
     *
     * This method applies permissions recursively.
     * @param \Cake\Console\ConsoleIo $io The console io
     * @param string $path Folder path
     * @param int $chmod Chmod
     * @return bool
     */
    public function folderChmod(ConsoleIo $io, string $path, int $chmod = 0777): bool
    {
        if (!(new Folder())->chmod($path, $chmod, true)) {
            $io->error(__d('me_tools', 'Failed to set permissions on `{0}`', rtr($path)));

            return false;
        }

        $io->verbose(__d('me_tools', 'Setted permissions on `{0}`', rtr($path)));

        return true;
    }
}
