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
namespace MeTools\Command\Install;

use Cake\Console\Arguments;
use Cake\Console\ConsoleIo;
use Cake\Console\ConsoleOptionParser;
use Cake\Utility\Inflector;
use MeTools\Console\Command;
use MeTools\Core\Plugin;

/**
 * 'Creates symbolic links for plugins assets'
 */
class CreatePluginsLinksCommand extends Command
{
    /**
     * Hook method for defining this command's option parser
     * @param ConsoleOptionParser $parser The parser to be defined
     * @return ConsoleOptionParser
     */
    protected function buildOptionParser(ConsoleOptionParser $parser)
    {
        return $parser->setDescription(__d('me_tools', 'Creates symbolic links for plugins assets'));
    }

    /**
     * Creates symbolic links for plugins assets
     * @param Arguments $args The command arguments
     * @param ConsoleIo $io The console io
     * @return null|int The exit code or null for success
     */
    public function execute(Arguments $args, ConsoleIo $io)
    {
        $plugins = [];

        foreach (Plugin::loaded() as $plugin) {
            $srcPath = Plugin::path($plugin) . 'webroot';
            if (!is_dir($srcPath)) {
                $io->verbose(sprintf('Skipping plugin %s. It does not have webroot folder.', $plugin), 1);
                continue;
            }

            list($link, $destDir, $namespaced) = [Inflector::underscore($plugin), WWW_ROOT, false];
            $plugins[$plugin] = compact('destDir', 'link', 'namespaced', 'srcPath');
        }

        foreach ($plugins as $plugin => $config) {
            $io->verbose('For plugin: ' . $plugin);

            $dest = $config['destDir'] . $config['link'];
            if (file_exists($dest)) {
                $io->verbose('Link `' . rtr($dest) . '` already exists', 1);
                continue;
            }

            $this->createLink($io, $config['srcPath'], $dest);
        }

        $io->verbose('Done');

        return null;
    }
}
