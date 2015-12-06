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
 * @copyright	Copyright (c) 2015, Mirko Pagliai for Nova Atlantis Ltd
 * @license		http://www.gnu.org/licenses/agpl.txt AGPL License
 * @link		http://git.novatlantis.it Nova Atlantis Ltd
 * @see			https://github.com/jakubpawlowicz/clean-css clean-css
 * @see			https://github.com/mishoo/UglifyJS2 UglifyJS
 */

namespace MeTools\View\Helper;

use Cake\Core\Configure;
use Cake\Network\Exception\InternalErrorException;
use Cake\View\Helper;
use MeTools\Core\Plugin;
use MeTools\Utility\Unix;

/**
 * Asset Helper.
 * 
 * This helper allows you to gerate assets.
 * 
 * Before using the helper, you have install `clean-css` and `UglifyJS`.
 */
class AssetHelper extends Helper {
	/**
	 * Helpers
	 * @var array
	 */
	public $helpers = ['Html' => ['className' => 'MeTools.Html']];
	
	/**
	 * Loaded plugins
	 * @see __construct()
	 * @var array 
	 */
	protected $plugins;

	/**
	 * Construct
	 * @param \Cake\View\View $view The View this helper is being attached to
	 * @param array $config Configuration settings for the helper
	 * @throws InternalErrorException
	 * @uses MeTools\Core\Plugin::getAll()
	 * @uses $plugins
	 */
    public function __construct(\Cake\View\View $view, $config = []) {
        parent::__construct($view, $config);
		
		//Checks if the target directory is writeable
		if(!is_writeable($target = WWW_ROOT.'assets'))
			throw new InternalErrorException(__d('me_tools', 'The directory {0} is not writable', rtr($target)));
		
		//Gets all plugins
		$this->plugins = Plugin::getAll();
	}
	
	/**
	 * Parses paths and for each path returns an array with the full path and the last modification time
     * @param string|array $paths String or array of css/js files
	 * @param string $extension Extension (`css` or `js`)
	 * @return array
	 * @uses MeTools\Core\Plugin::path()
	 * @uses $plugins
	 */
	protected function _parsePaths($paths, $extension) {
		foreach(is_array($paths) ? $paths : [$paths] as $k => $path) {
			$plugin = pluginSplit($path);
			
			if(in_array($plugin[0], $this->plugins))
				$path = $plugin[1];
			
			if(substr($path, 0, 1) == '/')
				$path = substr($path, 1);
			else
				$path = $extension.DS.$path;
			
			if(in_array($plugin[0], $this->plugins))
				$path = Plugin::path($plugin[0], 'webroot'.DS.$path);
			else
				$path = WWW_ROOT.$path;
						
			$paths[$k] = [$path = sprintf('%s.%s', $path, $extension), filemtime($path)];
		}
		
		return $paths;
	}

	/**
     * Compresses and adds a css file to the layout.
     * @param string|array $path String or array of css files
	 * @param array $options Array of options and HTML attributes
     * @return string Html, `<link>` or `<style>` tag
	 * @see https://github.com/jakubpawlowicz/clean-css clean-css
	 * @throws InternalErrorException
	 * @uses MeTools\View\Helper\HtmlHelper:css()
	 * @uses MeTools\Utility\Unix::which()
	 * @uses _parsePaths()
	 */
	public function css($path, array $options = []) {
		//If debug is enabled, returns
		if(Configure::read('debug'))
			return $this->Html->css($path, $options);
		
		//For each path, gets the full path and the modification time
		$path = $this->_parsePaths($path, 'css');
		
		//Sets asset full path (`$asset`) and www path (`$www`)
		$asset = WWW_ROOT.'assets'.DS.sprintf('%s.css', md5(serialize($path)));
		$www = sprintf('/assets/%s.css', md5(serialize($path)));
		
		if(!is_readable($asset)) {
			//Checks for Clean-css
			if(!($bin = Unix::which('cleancss')))
				throw new InternalErrorException(__d('me_tools', 'I can\'t find `{0}`', 'Clean-css'));
		
			//Reads all paths
			$content = implode(PHP_EOL, array_map(function($path) { return file_get_contents($path[0]); }, $path));
			
			//Creates the file
			if(!file_put_contents($asset, $content))
				throw new InternalErrorException(__d('me_tools', 'Impossible to create the file {0}', rtr($asset)));
			
			//Compresses
			exec(sprintf('%s -o %s --s0 %s', $bin, $asset, $asset));
		}
		
		return $this->Html->css($www, $options);
	}
	
    /**
     * Alias for `script()` method
     * @see script()
     */
    public function js() {
        return call_user_func_array(array(get_class(), 'script'), func_get_args());
    }
	
	/**
     * Compresses and adds js files to the layout
     * @param string|array $path String or array of js files
	 * @param array $options Array of options and HTML attributes
     * @return mixed String of `<script />` tags or NULL if `$inline` is FALSE or if `$once` is TRUE
	 * @see https://github.com/mishoo/UglifyJS2 UglifyJS
	 * @throws InternalErrorException
	 * @uses MeTools\View\Helper\HtmlHelper:script()
	 * @uses MeTools\Utility\Unix::which()
	 * @uses _parsePaths()
	 */
	public function script($url, array $options = []) {
		//If debug is enabled, returns
		if(Configure::read('debug'))
			return $this->Html->script($url, $options);
		
		//For each path, gets the full path and the modification time
		$path = $this->_parsePaths($url, 'js');
		
		//Sets asset full path (`$asset`) and www path (`$www`)
		$asset = WWW_ROOT.'assets'.DS.sprintf('%s.js', md5(serialize($path)));
		$www = sprintf('/assets/%s.js', md5(serialize($path)));
		
		if(!is_readable($asset)) {
			if(!($bin = Unix::which('uglifyjs')))
				throw new InternalErrorException(__d('me_tools', 'I can\'t find `{0}`', 'UglifyJS'));
		
			//Reads all paths
			$content = implode(PHP_EOL, array_map(function($path) { return file_get_contents($path[0]); }, $path));
			
			//Creates the file
			if(!file_put_contents($asset, $content))
				throw new InternalErrorException(__d('me_tools', 'Impossible to create the file {0}', rtr($asset)));
		
			//Compresses
			exec(sprintf('%s %s --compress --mangle -o %s', $bin, $asset, $asset));
		}
		
		return $this->Html->script($www, $options);
	}
}