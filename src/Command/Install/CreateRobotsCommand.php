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
use MeTools\Console\Command;
use Tools\Filesystem;

/**
 * Creates the `robots.txt` file
 */
class CreateRobotsCommand extends Command
{
    /**
     * Hook method for defining this command's option parser
     * @param \Cake\Console\ConsoleOptionParser $parser The parser to be defined
     * @return \Cake\Console\ConsoleOptionParser
     */
    protected function buildOptionParser(ConsoleOptionParser $parser): ConsoleOptionParser
    {
        return $parser->setDescription(__d('me_tools', 'Creates the {0} file', 'robots.txt'));
    }

    /**
     * Creates the `robots.txt` file
     * @param \Cake\Console\Arguments $args The command arguments
     * @param \Cake\Console\ConsoleIo $io The console io
     * @return void
     */
    public function execute(Arguments $args, ConsoleIo $io): void
    {
        $filename = Filesystem::instance()->concatenate(WWW_ROOT, 'robots.txt');
        $this->createFile($io, $filename, 'User-agent: *' . PHP_EOL .
            'Disallow: /admin/' . PHP_EOL . 'Disallow: /ckeditor/' . PHP_EOL .
            'Disallow: /css/' . PHP_EOL . 'Disallow: /js/' . PHP_EOL .
            'Disallow: /vendor/');
    }
}
