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
use Exception;
use MeTools\Console\Command;

/**
 * Creates symbolic links for vendor assets
 */
class FixComposerJsonCommand extends Command
{
    /**
     * Hook method for defining this command's option parser
     * @param ConsoleOptionParser $parser The parser to be defined
     * @return ConsoleOptionParser
     */
    protected function buildOptionParser(ConsoleOptionParser $parser)
    {
        $parser->setDescription(__d('me_tools', 'Fixes {0}', 'composer.json'));
        $parser->addOption('path', [
            'help' => __d('me_tools', 'Path of the `{0}` file', 'composer.json'),
            'short' => 'p',
        ]);

        return $parser;
    }

    /**
     * Creates symbolic links for vendor assets
     * @param Arguments $args The command arguments
     * @param ConsoleIo $io The console io
     * @return null|int The exit code or null for success
     * @uses Command::createLink()
     * @uses $links
     */
    public function execute(Arguments $args, ConsoleIo $io)
    {
        $path = $args->getOption('path') ?: ROOT . DS . 'composer.json';

        try {
            is_writable_or_fail($path);
        } catch (Exception $e) {
            $io->err($e->getMessage());
            $this->abort();
        }

        //Gets and decodes the file
        $contents = json_decode(file_get_contents($path), true);

        if (empty($contents)) {
            $io->err(__d('me_tools', 'The file {0} does not seem a valid {1} file', rtr($path), 'composer.json'));
            $this->abort();
        }

        //Checks if the file has been fixed
        if (!empty($contents['config']['component-dir']) &&
            $contents['config']['component-dir'] === 'vendor/components'
        ) {
            $io->verbose(__d('me_tools', 'The file {0} doesn\'t need to be fixed', rtr($path)));

            return null;
        }

        $contents += ['config' => ['component-dir' => 'vendor/components']];

        file_put_contents($path, json_encode($contents, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));

        $io->verbose(__d('me_tools', 'The file {0} has been fixed', rtr($path)));

        return null;
    }
}
