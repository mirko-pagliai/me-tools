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
use Tools\Exceptionist;
use Tools\Filesystem;

/**
 * Base class for console commands
 */
abstract class Command extends CakeCommand
{
    /**
     * Internal method to get a `Filesystem()` instance
     * @return \Tools\Filesystem
     */
    protected function getFilesystem(): Filesystem
    {
        return Filesystem::instance();
    }

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
