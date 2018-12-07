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
 * @since       2.16.9
 */
namespace MeTools;

use Assets\Plugin as Assets;
use Cake\Core\BasePlugin;
use Cake\Core\Configure;
use Cake\Core\PluginApplicationInterface;
use MeTools\Command\CreateDirectoriesCommand;
use MeTools\Command\CreatePluginsLinksCommand;
use MeTools\Command\CreateRobotsCommand;
use MeTools\Command\CreateVendorsLinksCommand;
use MeTools\Command\FixComposerJsonCommand;
use MeTools\Command\RunAllCommand;
use MeTools\Command\SetPermissionsCommand;

/**
 * Plugin class
 */
class Plugin extends BasePlugin
{
    /**
     * Load all the application configuration and bootstrap logic
     * @param PluginApplicationInterface $app The host application
     * @return void
     * @since 2.17.3
     */
    public function bootstrap(PluginApplicationInterface $app)
    {
        parent::bootstrap($app);

        //Sets directories to be created and must be writable
        Configure::write('WRITABLE_DIRS', [
            LOGS,
            TMP,
            TMP . 'cache',
            TMP . 'cache' . DS . 'models',
            TMP . 'cache' . DS . 'persistent',
            TMP . 'cache' . DS . 'views',
            TMP . 'sessions',
            TMP . 'tests',
            WWW_ROOT . 'files',
            WWW_ROOT . 'vendor',
        ]);

        if (class_exists(Assets::class) && !$app->getPlugins()->has('Assets')) {
            $app->addPlugin(Assets::class);
        }
    }

    /**
     * Add console commands for the plugin
     * @param Cake\Console\CommandCollection $commands The command collection to update
     * @return Cake\Console\CommandCollection
     */
    public function console($commands)
    {
        $commands->add('me_tools.create_directories', CreateDirectoriesCommand::class);
        $commands->add('me_tools.create_plugins_links', CreatePluginsLinksCommand::class);
        $commands->add('me_tools.create_robots', CreateRobotsCommand::class);
        $commands->add('me_tools.create_vendors_links', CreateVendorsLinksCommand::class);
        $commands->add('me_tools.fix_composer_json', FixComposerJsonCommand::class);
        $commands->add('me_tools.install', RunAllCommand::class);
        $commands->add('me_tools.set_permissions', SetPermissionsCommand::class);

        return $commands;
    }
}
