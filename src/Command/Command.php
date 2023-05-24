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
namespace MeTools\Command;

use Cake\Command\Command as CakeCommand;
use Cake\Console\ConsoleIo;
use Exception;
use Symfony\Component\Filesystem\Exception\IOException;
use Tools\Exceptionist;
use Tools\Filesystem;

/**
 * Base class for console commands
 */
abstract class Command extends CakeCommand
{
    /**
     * Internal method, checks if a file already exists and outputs a warning at the verbose level
     * @param \Cake\Console\ConsoleIo $io The console io
     * @param string $path Path
     * @return bool
     * @throws \ErrorException
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
     * @throws \ErrorException
     */
    public function copyFile(ConsoleIo $io, string $source, string $dest): bool
    {
        if ($this->verboseIfFileExists($io, $dest)) {
            return false;
        }

        try {
            //Checks if the source is readable and the destination is writable
            Exceptionist::isReadable($source);
            Exceptionist::isWritable(dirname($dest));
            Filesystem::instance()->copy($source, $dest);
        } catch (Exception $e) {
            $io->error($e->getMessage());

            return false;
        }

        $io->verbose(__d('me_tools', 'File `{0}` has been copied', rtr($dest)));

        return true;
    }

    /**
     * Creates a directory.
     *
     * This method creates directories recursively.
     * @param \Cake\Console\ConsoleIo $io The console io
     * @param string $path Directory path
     * @param int $chmod Chmod
     * @return bool
     * @throws \ErrorException
     */
    public function createDir(ConsoleIo $io, string $path, int $chmod = 0777): bool
    {
        if ($this->verboseIfFileExists($io, $path)) {
            return false;
        }

        try {
            Filesystem::instance()->mkdir($path);
            $io->verbose(__d('me_tools', 'Created `{0}` directory', rtr($path)));
            $this->folderChmod($io, $path, $chmod);
        } catch (IOException $e) {
            $mkdirError = lcfirst(array_value_last(explode('mkdir(): ', $e->getMessage())));
            $io->error(__d('me_tools', 'Failed to create file or directory `{0}` with message: {1}', rtr($path), $mkdirError));

            return false;
        }

        return true;
    }

    /**
     * Creates a file at given path
     * @param \Cake\Console\ConsoleIo $io The console io
     * @param string $path Where to put the file
     * @param string $contents Content to put in the file
     * @return bool
     * @throws \ErrorException
     */
    public function createFile(ConsoleIo $io, string $path, string $contents): bool
    {
        return !$this->verboseIfFileExists($io, $path) && $io->createFile($path, $contents);
    }

    /**
     * Creates a symbolic link
     * @param \Cake\Console\ConsoleIo $io The console io
     * @param string $source Source file or directory
     * @param string $dest Destination file or directory
     * @return bool
     * @throws \ErrorException
     * @deprecated 2.24.1 Deprecated. Will be removed in a later release
     * @codeCoverageIgnore
     */
    public function createLink(ConsoleIo $io, string $source, string $dest): bool
    {
        deprecationWarning('Deprecated. Will be removed in a later release');

        if ($this->verboseIfFileExists($io, $dest)) {
            return false;
        }

        try {
            //Checks if the source is readable and the destination directory is writable
            Exceptionist::isReadable($source);
            Exceptionist::isWritable(dirname($dest));
            Filesystem::instance()->symlink($source, $dest, true);
        } catch (Exception $e) {
            $io->error($e->getMessage());

            return false;
        }

        $io->verbose(__d('me_tools', 'Link `{0}` has been created', rtr($dest)));

        return true;
    }

    /**
     * Sets folder permissions.
     *
     * This method applies permissions recursively.
     * @param \Cake\Console\ConsoleIo $io The console io
     * @param string $path Folder path
     * @param int $chmod Chmod
     * @return bool
     * @throws \ErrorException
     */
    public function folderChmod(ConsoleIo $io, string $path, int $chmod = 0777): bool
    {
        try {
            Filesystem::instance()->chmod($path, $chmod, 0000, true);
        } catch (IOException $e) {
            $io->error(__d('me_tools', 'Failed to set permissions on `{0}`', rtr($path)));

            return false;
        }

        $io->verbose(__d('me_tools', 'Set permissions on `{0}`', rtr($path)));

        return true;
    }

    /**
     * Checks if the console is verbose
     * @param \Cake\Console\ConsoleIo $io The console io
     * @return bool
     * @since 2.24.1
     */
    public function isVerbose(ConsoleIo $io): bool
    {
        return $io->level() === $io::VERBOSE;
    }
}
