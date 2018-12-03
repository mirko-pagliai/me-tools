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
 * Creates symbolic links for vendor assets
 */
class CreateVendorsLinksCommand extends Command
{
    /**
     * Assets for which create symbolic links.
     * The key must be relative to `vendor/` and the value must be relative
     *  to `webroot/vendor/`
     * @var array
     */
    public $links = [
        'eonasdan' . DS . 'bootstrap-datetimepicker' . DS . 'build' => 'bootstrap-datetimepicker',
        'components' . DS . 'jquery' => 'jquery',
        'components' . DS . 'moment' . DS . 'min' => 'moment',
        'newerton' . DS . 'fancy-box' . DS . 'source' => 'fancybox',
        'npm-asset' . DS . 'fortawesome--fontawesome-free' => 'font-awesome',
        'twbs' . DS . 'bootstrap' . DS . 'dist' => 'bootstrap',
    ];

    /**
     * Hook method for defining this command's option parser
     * @param ConsoleOptionParser $parser The parser to be defined
     * @return ConsoleOptionParser
     */
    protected function buildOptionParser(ConsoleOptionParser $parser)
    {
        $parser->setDescription(__d('me_tools', 'Creates symbolic links for vendor assets'));

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
        foreach ($this->links as $origin => $target) {
            $this->createLink(
                $io,
                ROOT . 'vendor' . DS . $origin,
                WWW_ROOT . 'vendor' . DS . $target
            );
        }

        return null;
    }
}
