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
 * @since       2.18.0
 */
namespace MeTools\Command\Install;

use Cake\Command\PluginAssetsSymlinkCommand;
use Cake\Console\Arguments;
use Cake\Console\ConsoleIo;
use Cake\Console\ConsoleOptionParser;
use MeTools\Command\Command;

/**
 * Creates symbolic links for plugins assets
 * @codeCoverageIgnore
 */
class CreatePluginsLinksCommand extends Command
{
    /**
     * Hook method for defining this command's option parser
     * @param \Cake\Console\ConsoleOptionParser $parser The parser to be defined
     * @return \Cake\Console\ConsoleOptionParser
     */
    protected function buildOptionParser(ConsoleOptionParser $parser): ConsoleOptionParser
    {
        return $parser->setDescription(__d('me_tools', 'Creates symbolic links for plugins assets'));
    }

    /**
     * Creates symbolic links for plugins assets
     * @param \Cake\Console\Arguments $args The command arguments
     * @param \Cake\Console\ConsoleIo $io The console io
     * @return int|null The exit code or null for success of the command
     * @deprecated 2.24.1 Use instead `PluginAssetsSymlinkCommand` (`bin/cake plugin assets symlink`)
     */
    public function execute(Arguments $args, ConsoleIo $io): ?int
    {
        deprecationWarning('Deprecated. Use instead `PluginAssetsSymlinkCommand` (`bin/cake plugin assets symlink`)');

        $argsAsArray = array_map(fn(string $argAsString): string => '--' . $argAsString, array_keys(array_filter($args->getOptions())));

        return $this->executeCommand(PluginAssetsSymlinkCommand::class, $argsAsArray, $io);
    }
}
