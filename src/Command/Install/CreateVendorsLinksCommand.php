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
use MeTools\Command\Command;
use MeTools\Core\Configure;
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
     * @return int|null
     * @throws \ErrorException
     */
    public function execute(Arguments $args, ConsoleIo $io): ?int
    {
        if (!is_writable(WWW_VENDOR)) {
            $io->error(__d('me_tools', 'File or directory `{0}` is not writable', rtr(WWW_VENDOR)));

            return self::CODE_ERROR;
        }

        foreach (Configure::readFromPlugins('VendorLinks') as $origin => $target) {
            $origin = VENDOR . Filesystem::normalizePath($origin);
            $target = WWW_VENDOR . $target;

            if (!file_exists($origin)) {
                if ($this->isVerbose($io)) {
                    $io->warning(__d('me_tools', 'File or directory `{0}` does not exist', rtr($origin)) . '. ', 0);
                    $io->warning(__d('me_tools', 'Skip'));
                }

                continue;
            }

            if (file_exists($target) && readlink($target) === $origin) {
                $io->verbose(__d('me_tools', 'Link to `{0}` already exists', rtr($target)));

                continue;
            }

            Filesystem::instance()->symlink($origin, $target);
            if ($this->isVerbose($io)) {
                $io->success(__d('me_tools', 'Link from `{0}` to `{1}` has been created', rtr($origin), rtr($target)));
            }
        }

        return null;
    }
}
