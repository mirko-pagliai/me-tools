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
 */
namespace MeTools\Core;

use Cake\Core\Plugin as CakePlugin;
use LogicException;

/**
 * A utility to handle plugins
 */
class Plugin extends CakePlugin
{
    /**
     * Gets all loaded plugins.
     *
     * Available options are:
     *  - `core`, if `false` exclude the core plugins;
     *  - `exclude`, a plugin as string or an array of plugins to be excluded;
     *  - `order`, if `true` the plugins will be sorted.
     * @param array $options Options
     * @return array<string> Plugins
     * @uses \Cake\Core\Plugin::loaded()
     */
    public static function all(array $options = []): array
    {
        $options += ['core' => false, 'exclude' => [], 'order' => true];

        $plugins = parent::loaded();
        $plugins = $options['core'] ? $plugins : array_diff($plugins, ['DebugKit', 'Migrations', 'Bake']);
        $plugins = !$options['exclude'] ? $plugins : array_diff($plugins, (array)$options['exclude']);

        $key = array_search('MeTools', $plugins);
        if ($options['order'] && $key) {
            unset($plugins[$key]);
            array_unshift($plugins, 'MeTools');
        }

        return $plugins;
    }

    /**
     * Gets a path for a plugin.
     * It can also be used to get the path of a plugin file.
     * @param string $name Plugin name
     * @param string|null $file File
     * @param bool $check Checks if the file exists
     * @return string Path of the plugin or path of the path of a plugin file
     * @throws \ErrorException
     */
    public static function path(string $name, ?string $file = null, bool $check = false): string
    {
        $plugin = parent::path($name);
        if (!$file) {
            return $plugin;
        }

        $path = $plugin . $file;
        if ($check && !file_exists($path)) {
            throw new LogicException(__d('me_tools', 'File or directory `{0}` does not exist', $name . '.' . rtr($path)));
        }

        return $path;
    }
}
