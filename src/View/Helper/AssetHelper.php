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
use MeTools\Utility\Asset;

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
     * Compresses and adds a css file to the layout
     * @param string|array $path String or array of css files
	 * @param array $options Array of options and HTML attributes
     * @return string Html, `<link>` or `<style>` tag
	 * @see https://github.com/jakubpawlowicz/clean-css clean-css
	 * @uses MeTools\View\Helper\HtmlHelper:css()
	 * @uses MeTools\Utility\Asset::get()
	 */
	public function css($path, array $options = []) {
		//Checks if the debug is enabled
		$path = Configure::read('debug') ? $path : Asset::get($path, 'css');
		
		return $this->Html->css(Asset::get($path, 'css'), $options);
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
	 * @uses MeTools\View\Helper\HtmlHelper:script()
	 * @uses MeTools\Utility\Asset::get()
	 */
	public function script($url, array $options = []) {
		//Checks if the debug is enabled
		$url = Configure::read('debug') ? $url : Asset::get($url, 'js');
		
		return $this->Html->script($url, $options);
	}
}