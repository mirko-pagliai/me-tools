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
 * @since       2.16.9
 */
namespace MeTools;

use Cake\Console\CommandCollection;
use Cake\Core\BasePlugin;
use Cake\Core\PluginApplicationInterface;

/**
 * Plugin class
 */
class Plugin extends BasePlugin
{
    /**
     * Load all the application configuration and bootstrap logic
     * @param \Cake\Core\PluginApplicationInterface $app The host application
     * @return void
     * @since 2.17.3
     */
    public function bootstrap(PluginApplicationInterface $app): void
    {
        /** @var \Cake\Http\BaseApplication $app */
        parent::bootstrap($app);

        $app->addOptionalPlugin('Assets');
    }
}
