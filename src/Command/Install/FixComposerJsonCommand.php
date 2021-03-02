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
use Exception;
use MeTools\Console\Command;
use Tools\Exceptionist;
use Tools\Filesystem;

/**
 * Creates symbolic links for vendor assets
 */
class FixComposerJsonCommand extends Command
{
    /**
     * Hook method for defining this command's option parser
     * @param \Cake\Console\ConsoleOptionParser $parser The parser to be defined
     * @return \Cake\Console\ConsoleOptionParser
     */
    protected function buildOptionParser(ConsoleOptionParser $parser): ConsoleOptionParser
    {
        return $parser->setDescription(__d('me_tools', 'Fixes {0}', 'composer.json'))
            ->addOption('path', [
                'help' => __d('me_tools', 'Path of the `{0}` file', 'composer.json'),
                'short' => 'p',
            ]);
    }

    /**
     * Creates symbolic links for vendor assets
     * @param \Cake\Console\Arguments $args The command arguments
     * @param \Cake\Console\ConsoleIo $io The console io
     * @return void
     * @throws \Cake\Console\Exception\StopException
     */
    public function execute(Arguments $args, ConsoleIo $io): void
    {
        $Filesystem = new Filesystem();
        $path = (string)$args->getOption('path') ?: $Filesystem->concatenate(ROOT, 'composer.json');

        try {
            Exceptionist::isWritable($path);
        } catch (Exception $e) {
            $io->err($e->getMessage());
            $this->abort();
        }

        //Gets and decodes the file
        $contents = json_decode(file_get_contents($path) ?: '', true);

        if (empty($contents)) {
            $io->err(__d('me_tools', 'File `{0}` does not seem a valid {1} file', $Filesystem->rtr($path), 'composer.json'));
            $this->abort();
        }

        //Checks if the file has been fixed
        $message = __d('me_tools', 'File `{0}` doesn\'t need to be fixed', $Filesystem->rtr($path));
        if (empty($contents['config']['component-dir']) || $contents['config']['component-dir'] !== 'vendor/components') {
            $contents += ['config' => ['component-dir' => 'vendor/components']];
            $Filesystem->createFile($path, json_encode($contents, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
            $message = __d('me_tools', 'File `{0}` has been fixed', $Filesystem->rtr($path));
        }
        $io->verbose($message);
    }
}
