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
 * @since       2.23.3
 */

namespace MeTools\Core;

use Cake\Core\Configure as BaseConfigure;

/**
 * Configuration class. Used for managing runtime configuration information
 */
class Configure extends BaseConfigure
{
    /**
     * Used to read information stored in Configure.
     *
     * This method looks for the same configuration key in all loaded plugins.
     *
     * For example, if the plugins are `PluginOne` and `PluginTwo` and `$var` is `myValue`, it will return an array with
     *  the merged values of `PluginOne.myValue` and `PluginTwo.myValue`, if they exist.
     * @param string $var Variable name
     * @return array<string, mixed>
     */
    public static function readFromPlugins(string $var): array
    {
        $plugins = array_filter(Plugin::all(), fn(string $plugin): bool => Configure::check($plugin . '.' . $var));
        $values = array_combine($plugins, array_map(fn(string $plugin): array => (array)Configure::read($plugin . '.' . $var), $plugins));

        return array_merge(...array_values($values));
    }
}
