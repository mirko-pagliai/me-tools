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
 * @see         http://api.cakephp.org/3.4/class-Cake.Core.Plugin.html Plugin
 */
namespace MeTools\Core;

use Cake\Core\Plugin as CakePlugin;

/**
 * An utility to handle plugins
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
     * @return array Plugins
     * @uses Cake\Core\Plugin::loaded()
     */
    public static function all(array $options = [])
    {
        $plugins = parent::loaded();

        $options = array_merge([
            'core' => false,
            'exclude' => [],
            'order' => true,
        ], $options);

        if (!$options['core']) {
            $plugins = array_diff($plugins, ['DebugKit', 'Migrations', 'Bake']);
        }

        if (!empty($options['exclude'])) {
            $plugins = array_diff($plugins, (array)$options['exclude']);
        }

        if ($options['order']) {
            $key = array_search(ME_TOOLS, $plugins);

            if ($key) {
                unset($plugins[$key]);
                array_unshift($plugins, ME_TOOLS);
            }
        }

        return $plugins;
    }

    /**
     * Gets a path for a plugin.
     * It can also be used to get the path of plugin files.
     * @param string $plugin Plugin name
     * @param string|array $file Files
     * @param bool $check Checks if the files exist
     * @return string|array|bool String or `false` if you asked the path of a
     *  plugin or of a single plugin file. Otherwise, an array if you asked
     *  the path of several plugin files
     */
    public static function path($plugin, $file = null, $check = false)
    {
        $plugin = parent::path($plugin);

        if (empty($file)) {
            return $plugin;
        }

        if (is_array($file)) {
            $path = [];

            foreach ($file as $fileName) {
                $filePath = $plugin . $fileName;

                if ($check && !is_readable($filePath)) {
                    continue;
                }

                $path[] = $filePath;
            }

            return $path;
        }

        $path = $plugin . $file;

        if ($check && !is_readable($path)) {
            return false;
        }

        return $path;
    }
}
