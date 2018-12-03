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
 * @since       2.18.0
 */
namespace MeTools\Command;

use Cake\Console\Arguments;
use Cake\Console\ConsoleIo;
use Cake\Console\ConsoleOptionParser;
use MeTools\Console\Command;

/**
 * Sets directories permissions
 */
class SetPermissionsCommand extends Command
{
    /**
     * Paths to be created and made writable
     * @var array
     */
    public $paths = [
        LOGS,
        TMP,
        TMP . 'cache',
        TMP . 'cache' . DS . 'models',
        TMP . 'cache' . DS . 'persistent',
        TMP . 'cache' . DS . 'views',
        TMP . 'sessions',
        TMP . 'tests',
        WWW_ROOT . 'files',
        WWW_ROOT . 'vendor',
    ];

    /**
     * Hook method for defining this command's option parser
     * @param ConsoleOptionParser $parser The parser to be defined
     * @return ConsoleOptionParser
     */
    protected function buildOptionParser(ConsoleOptionParser $parser)
    {
        $parser->setDescription(__d('me_tools', 'Sets directories permissions'));

        return $parser;
    }

    /**
     * Sets directories permissions
     * @param Arguments $args The command arguments
     * @param ConsoleIo $io The console io
     * @return null|int The exit code or null for success
     * @uses Command::folderChmod
     */
    public function execute(Arguments $args, ConsoleIo $io)
    {
        foreach ($this->paths as $path) {
            $this->folderChmod($io, $path);
        }

        return null;
    }
}
