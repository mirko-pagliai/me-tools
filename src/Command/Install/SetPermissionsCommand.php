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
use MeTools\Command\Command;
use Symfony\Component\Filesystem\Exception\IOException;

/**
 * Sets directories permissions
 */
class SetPermissionsCommand extends Command
{
    /**
     * Hook method for defining this command's option parser
     * @param \Cake\Console\ConsoleOptionParser $parser The parser to be defined
     * @return \Cake\Console\ConsoleOptionParser
     */
    protected function buildOptionParser(ConsoleOptionParser $parser): ConsoleOptionParser
    {
        return $parser->setDescription(__d('me_tools', 'Sets directories permissions'));
    }

    /**
     * Sets directories permissions
     * @param \Cake\Console\Arguments $args The command arguments
     * @param \Cake\Console\ConsoleIo $io The console io
     * @return int
     * @throws \ErrorException
     */
    public function execute(Arguments $args, ConsoleIo $io): int
    {
        foreach (Configure::read('MeTools.WritableDirs') as $dir) {
            if (!file_exists($dir)) {
                $io->verbose(__d('me_tools', 'File or directory `{0}` does not exist', rtr($dir)) . '. ', 0);
                $io->verbose(__d('me_tools', 'Skip'));

                continue;
            }

            try {
                $this->getFilesystem()->chmod($dir, 0777, 0000, true);
            } catch (IOException $e) {
                $io->error($e->getMessage());

                return self::CODE_ERROR;
            }

            if ($this->isVerbose($io)) {
                $io->success(__d('me_tools', 'Set permissions on `{0}`', rtr($dir)));
            }
        }

        return self::CODE_SUCCESS;
    }
}
