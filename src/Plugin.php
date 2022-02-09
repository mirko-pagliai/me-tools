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

use Assets\Plugin as Assets;
use Cake\Console\CommandCollection;
use Cake\Core\BasePlugin;
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
     * @param \Cake\Core\PluginApplicationInterface $app The host application
     * @return void
     * @since 2.17.3
     */
    public function bootstrap(PluginApplicationInterface $app): void
    {
        /** @var \Cake\Http\BaseApplication $app */
        parent::bootstrap($app);

        if (class_exists(Assets::class) && !$app->getPlugins()->has('Assets')) {
            $plugin = new Assets();
            $plugin->bootstrap($app);
            $app->addPlugin($plugin);
        }
    }

    /**
     * Add console commands for the plugin
     * @param \Cake\Console\CommandCollection $commands The command collection to update
     * @return \Cake\Console\CommandCollection
     */
    public function console(CommandCollection $commands): CommandCollection
    {
        return $commands->add('me_tools.create_directories', CreateDirectoriesCommand::class)
            ->add('me_tools.create_plugins_links', CreatePluginsLinksCommand::class)
            ->add('me_tools.create_robots', CreateRobotsCommand::class)
            ->add('me_tools.create_vendors_links', CreateVendorsLinksCommand::class)
            ->add('me_tools.fix_composer_json', FixComposerJsonCommand::class)
            ->add('me_tools.install', RunAllCommand::class)
            ->add('me_tools.set_permissions', SetPermissionsCommand::class);
    }
}
