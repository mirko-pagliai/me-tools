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

use Cake\Console\Arguments;
use Cake\Console\ConsoleIo;
use Cake\Console\ConsoleOptionParser;
use Cake\Core\Configure;
use MeTools\Console\Command;
use Tools\Filesystem;

/**
 * Creates symbolic links for vendor assets
 */
class CreateVendorsLinksCommand extends Command
{
    /**
     * Hook method for defining this command's option parser
     * @param \Cake\Console\ConsoleOptionParser $parser The parser to be defined
     * @return \Cake\Console\ConsoleOptionParser
     */
    protected function buildOptionParser(ConsoleOptionParser $parser): ConsoleOptionParser
    {
        return $parser->setDescription(__d('me_tools', 'Creates symbolic links for vendor assets'));
    }

    /**
     * Creates symbolic links for vendor assets
     * @param \Cake\Console\Arguments $args The command arguments
     * @param \Cake\Console\ConsoleIo $io The console io
     * @return void
     */
    public function execute(Arguments $args, ConsoleIo $io): void
    {
        $root = Filesystem::instance()->concatenate(ROOT, 'vendor');
        $wwwRoot = Filesystem::instance()->concatenate(WWW_ROOT, 'vendor');

        foreach (array_unique(Configure::read('VENDOR_LINKS')) as $origin => $target) {
            $this->createLink($io, $root . DS . $origin, $wwwRoot . DS . $target);
        }
    }
}
