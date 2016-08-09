<?php
/**
 * This file is part of MeTools.
 *
 * MeTools is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * MeTools is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with MeTools.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @author		Mirko Pagliai <mirko.pagliai@gmail.com>
 * @copyright	Copyright (c) 2016, Mirko Pagliai for Nova Atlantis Ltd
 * @license		http://www.gnu.org/licenses/agpl.txt AGPL License
 * @link		http://git.novatlantis.it Nova Atlantis Ltd
 * @see			http://api.cakephp.org/3.3/class-Cake.Core.Plugin.html Plugin
 */
namespace MeTools\Core;

use Cake\Core\Plugin as CakePlugin;

/**
 * An utility to handle plugins.
 * 
 * Rewrites {@link http://api.cakephp.org/3.3/class-Cake.Core.Plugin.html Plugin}.
 */
class Plugin extends CakePlugin {    
    /**
     * Gets all loaded plugins.
     * 
     * Available options are:
     *  - `core`, if `FALSE` exclude the core plugins;
     *  - `exclude`, a plugin as string or an array of plugins to be excluded;
     *  - `order`, if `TRUE` the plugins will be sorted.
     * @param array $options Options
     * @return array Plugins
	 * @uses Cake\Core\Plugin::loaded()
     */
    public static function all(array $options = []) {
		$plugins = parent::loaded();
        
        $options = am([
            'core' => FALSE,
            'exclude' => [],
            'order' => TRUE
        ], $options);
        
        if(!$options['core']) {
            $plugins = array_diff($plugins, ['DebugKit', 'Migrations', 'Bake']);
        }
        
        if(!empty($options['exclude'])) {
            $plugins = array_diff($plugins, (array) $options['exclude']);
        }
        
        if($options['order']) {
            $key = array_search('MeTools', $plugins);
            
            if($key) {
                unset($plugins[$key]);
                array_unshift($plugins, 'MeTools');
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
     * @return string|array|bool String or `FALSE` if you asked the path of a 
     *  plugin or of a single plugin file. Otherwise, an array if you asked 
     *  the path of several plugin files
     */
    public static function path($plugin, $file = NULL, $check = FALSE) {
        $plugin = parent::path($plugin);
        
        if(empty($file)) {
            return $plugin;
        }
        
        if(is_array($file)) {
            $path = [];
            
            foreach($file as $fileName) {
                $filePath = $plugin.$fileName;
                
                if($check && !is_readable($filePath)) {
                    continue;
                }
                
                $path[] = $filePath;
            }
            
            return $path;
        }
        
        $path = $plugin.$file;

        if($check && !is_readable($path)) {
            return FALSE;
        }

        return $path;
    }
}