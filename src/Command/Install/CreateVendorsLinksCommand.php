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
use Cake\Core\Configure;
use MeTools\Console\Command;

/**
 * Creates symbolic links for vendor assets
 */
class CreateVendorsLinksCommand extends Command
{
    /**
     * Hook method for defining this command's option parser
     * @param ConsoleOptionParser $parser The parser to be defined
     * @return ConsoleOptionParser
     */
    protected function buildOptionParser(ConsoleOptionParser $parser)
    {
        return $parser->setDescription(__d('me_tools', 'Creates symbolic links for vendor assets'));
    }

    /**
     * Creates symbolic links for vendor assets
     * @param Arguments $args The command arguments
     * @param ConsoleIo $io The console io
     * @return null|int The exit code or null for success
     * @uses Command::createLink()
     */
    public function execute(Arguments $args, ConsoleIo $io)
    {
        foreach (Configure::read('VENDOR_LINKS') as $origin => $target) {
            $this->createLink(
                $io,
                add_slash_term(ROOT) . 'vendor' . DS . $origin,
                add_slash_term(WWW_ROOT) . 'vendor' . DS . $target
            );
        }

        return null;
    }
}
