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
use MeTools\Command\Install\CreateDirectoriesCommand;
use MeTools\Command\Install\CreatePluginsLinksCommand;
use MeTools\Command\Install\CreateRobotsCommand;
use MeTools\Command\Install\CreateVendorsLinksCommand;
use MeTools\Command\Install\FixComposerJsonCommand;
use MeTools\Command\Install\RunAllCommand;
use MeTools\Command\Install\SetPermissionsCommand;

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

        //Sets symbolic links for vendor assets to be created
        Configure::write('VENDOR_LINKS', [
            'eonasdan' . DS . 'bootstrap-datetimepicker' . DS . 'build' => 'bootstrap-datetimepicker',
            'components' . DS . 'jquery' => 'jquery',
            'components' . DS . 'moment' . DS . 'min' => 'moment',
            'newerton' . DS . 'fancy-box' . DS . 'source' => 'fancybox',
            'npm-asset' . DS . 'fortawesome--fontawesome-free' => 'font-awesome',
            'twbs' . DS . 'bootstrap' . DS . 'dist' => 'bootstrap',
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
