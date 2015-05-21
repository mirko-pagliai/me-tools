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
 */
namespace MeTools\View\Helper;

use Cake\Core\Configure;
use Cake\View\Helper;
use Cake\View\View;

/**
 * Layout Helper.
 * 
 * This helper allows you to gerate the layouts.
 */
class LayoutHelper extends Helper {
	/**
	 * Helpers
	 * @var array
	 */
	public $helpers = ['Html' => ['className' => 'MeTools.Html']];

	/**
     * Adds a css file to the layout.
	 * 
	 * It uses the first argument if debugging is disabled, otherwise the second argument if debugging is enabled.
     * @param mixed $path Css filename or an array of css filenames, to be used if debugging is disabled
	 * @param type $pathForDebug Css filename or an array of css filenames, to be used if debugging is enabled
	 * @param array $options Array of options and HTML attributes
     * @return string Html, `<link>` or `<style>` tag
	 * @uses Cake\Core\Configure::read()
	 */
	public function css($path, $pathForDebug, array $options = []) {
		return $this->Html->css(Configure::read('debug') ? $pathForDebug : $path, $options);
	}
	
    /**
     * Alias for `script()` method
     * @see script()
     */
    public function js() {
        return call_user_func_array(array(get_class(), 'script'), func_get_args());
    }
	
	/**
     * Adds a js file to the layout.
	 * 
	 * It uses the first argument if debugging is disabled, otherwise the second argument if debugging is enabled.
     * @param mixed $url Javascript files as string or array, to be used if debugging is disabled
	 * @param type $urlForDebug Javascript files as string or array, to be used if debugging is enabled
	 * @param array $options Array of options and HTML attributes
     * @return mixed String of `<script />` tags or NULL if `$inline` is FALSE or if `$once` is TRUE
	 * and the file has been included before
	 * @uses Cake\Core\Configure::read()
	 */
    public function script($url, $urlForDebug, array $options = []) {
		return $this->Html->script(Configure::read('debug') ? $urlForDebug : $url, $options);
	}
	
	/**
	 * Adds the `viewport` meta tag as required by Bootstrap.
	 * @param bool $zooming Enabled or disabled zooming capabilities on mobile devices
     * @return string Html code
     * @see http://getbootstrap.com/css/#overview-mobile Bootstrap documentation
     * @uses MeTools\View\Helper\HtmlHelper::meta()
	 */
	public function viewport($zooming = FALSE) {
		$content = 'width=device-width, initial-scale=1';
		
		if(!$zooming)
			$content = sprintf('%s, %s', $content, 'maximum-scale=1, user-scalable=no');
			
		return $this->Html->meta(am(['name' => 'viewport'], compact('content')));
	}
}