<?php
App::uses('MeToolsAppHelper', 'MeTools.View/Helper');

/**
 * Provides extended functionalities for html.
 *
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
 * @copyright	Copyright (c) 2013, Mirko Pagliai for Nova Atlantis Ltd
 * @license		http://www.gnu.org/licenses/agpl.txt AGPL License
 * @link		http://git.novatlantis.it Nova Atlantis Ltd
 * @package		MeTools.View.Helper
 */
class MeHtmlHelper extends MeToolsAppHelper {
	/**
	 * Helpers used
	 * @var array Helpers name
	 */
	public $helpers = array('Html');

	/**
	 * Adds a link to the breadcrumbs array. Rewrite <i>$this->Html->addCrumb()</i>
	 *
	 * Look at {@link http://api.cakephp.org/2.4/class-HtmlHelper.html#_addCrumb CakePHP Api}
	 * @param string $name Text for link
	 * @param string $link URL for link (if empty it won't be a link)
	 * @param string|array $options Array of HTML attributes
	 * @return void
	 */
	public function addCrumb($name, $link=null, $options=null) {
		//"escape" option default false
		$options['escape'] = empty($options['escape']) ? false : $options['escape'];

		return $this->Html->addCrumb($name, $link, $options);
	}

	/**
	 * Add a css file to the layout. Rewrite <i>$this->Html->css()</i>
	 *
	 * Look at {@link http://api.cakephp.org/2.4/class-HtmlHelper.html#_css CakePHP Api}
	 * @param string|array $filename The css filename or an array of css files
	 * @param string $rel The value of the generated tag's rel attribute. If null, 'stylesheet' will be use
	 * @param array $options Array of HTML attributes
	 * @return string CSS <link /> or <style /> tag, depending on the type of link
	 */
	public function css($filename, $rel=null, $options=array()) {
		//"inline" option default false
		$options['inline'] = empty($options['inline']) ? false : $options['inline'];

		return $this->Html->css($filename, $rel, $options);
	}

	/**
	 * End CSS code. Rewrite <i>ViewBlock::end()</i>
	 *
	 * Look at {@link http://api.cakephp.org/2.4/class-ViewBlock.html#_start CakePHP Api}
	 */
	public function cssEnd() {
		$this->_View->end();
	}

	/**
	 * Start CSS code. Rewrite <i>ViewBlock::start('css')</i>
	 *
	 * Look at {@link http://api.cakephp.org/2.4/class-ViewBlock.html#_start CakePHP Api}
	 */
	public function cssStart() {
		$this->_View->start('css');
	}

	/**
	 * Returns breadcrumbs as a (x)html list. Rewrite <i>$this->Html->getCrumbList()</i>
	 *
	 * Look at {@link http://api.cakephp.org/2.4/class-HtmlHelper.html#_getCrumbList CakePHP Api}
	 * @param array $options Array of html attributes. Can also contain "separator" and "firstClass" options. The "lastClass" option is set automatically as required by Bootstrap
	 * @param string|array|boolean $startText The first crumb, if false it defaults to first crumb in array
	 * @return string breadcrumbs html list
	 */
	public function getCrumbList($options=array(), $startText=false) {
		//Add the "breadcrumb" class
		$options['class'] = empty($options['class']) ? 'breadcrumb' : $this->_cleanAttribute($options['class'].' breadcrumb');

		//Change the separator as required by Bootstrap
		if(!empty($options['separator']))
			$options['separator'] = '<span class="divider">'.$options['separator'].'</span>';

		//Add the "active" class to the last element
		$options['lastClass'] = 'active';

		//"escape" option default false
		if(!empty($startText)) {
			//If $startText is not an array, converts it into an array
			if(!is_array($startText))
				$startText = array('text' => $startText);
			$startText['escape'] = false;
		}

		return $this->Html->getCrumbList($options, $startText);
	}

	/**
	 * Create an HTML link. Rewrite <i>$this->Html->link()</i>
	 *
	 * You can use {@link http://twitter.github.com/bootstrap/base-css.html#icons bootstrap icons} using 'icon' option. Example:
	 * <code>
	 * echo $this->Html->link('my link', 'http://site.com', array('icon' => 'icon-search'));
	 * </code>
	 *
	 * Look at {@link http://api.cakephp.org/2.4/class-HtmlHelper.html#_link CakePHP Api}
	 * @param string $title Link title
	 * @param mixed $url Cake-relative URL, array of URL parameters or external URL (starts with http://)
	 * @param array $options Array of HTML attributes
	 * @param string $confirmMessage JavaScript confirmation message
	 * @return string Html
	 */
	public function link($title, $url=null, $options=array(), $confirmMessage=false) {
		//"escape" option default false
		$options['escape'] = empty($options['escape']) ? false : $options['escape'];

		//Add bootstrap icon to the title, if there's the 'icon' option
		$title = !empty($options['icon']) ? '<i class="'.$this->_cleanAttribute($options['icon']).'"></i> '.$title : $title;
		unset($options['icon']);

		//Add the 'tooltip' data-toggle
		$options['data-toggle'] = empty($options['data-toggle']) ? 'tooltip' : $this->_cleanAttribute($options['data-toggle'].' tooltip');

		return $this->Html->link($title, $url, $options, $confirmMessage);
	}

	/**
	 * Create an HTML link with the appearance of a button, as required by {@link http://twitter.github.com/bootstrap/base-css.html#buttons Bootstrap}.
	 * Use the <i>link()</i> method
	 * @param string $title Button title
	 * @param mixed $url Cake-relative URL, array of URL parameters or external URL (starts with http://)
	 * @param array $options Array of HTML attributes
	 * @param string $confirmMessage JavaScript confirmation message
	 * @return string Html
	 */
	public function linkButton($title, $url=null, $options=array(), $confirmMessage=false) {
		//Add the 'btn' class
		$options['class'] = empty($options['class']) ? 'btn' : $this->_cleanAttribute($options['class'].' btn');

		return $this->link($title, $url, $options, $confirmMessage);
	}

	/**
	 * Add a js file to the layout. Rewrite <i>$this->Html->script()</i>
	 *
	 * When used in the layout, remember to use the "inline" option (must be set to TRUE)
	 *
	 * Look at {@link http://api.cakephp.org/2.4/class-HtmlHelper.html#_script CakePHP Api}
	 * @param string|array $url String or array of javascript files to include
	 * @param array $options Array of options and html attributes
	 * @return mixed String of <script /> tags or null if $inline is false or if $once is true and the file has been included before
	 */
	public function script($url, $options=array()) {
		//"inline" option default false
		$options['inline'] = empty($options['inline']) ? false : $options['inline'];

		return $this->Html->script($url, $options);
	}

	/**
	 * Generate a javascript code block containing $code. Rewrite <i>$this->Html->scriptBlock()</i>
	 *
	 * Look at {@link http://api.cakephp.org/2.4/class-HtmlHelper.html#_scriptBlock CakePHP Api}
	 * @param string $code The code to go in the script tag
	 * @param array $options An array of html attributes
	 * @return string Html
	 */
	public function scriptBlock($code, $options = array()) {
		//"inline" option default false
		$options['inline'] = empty($options['inline']) ? false : $options['inline'];

		return $this->Html->scriptBlock($code, $options);
	}

	/**
	 * End javascript code. Rewrite <i>$this->Html->scriptEnd()</i>
	 *
	 * Look at {@link http://api.cakephp.org/2.4/class-HtmlHelper.html#_scriptEnd CakePHP Api}
	 * @return mixed A script tag or null
	 */
	public function scriptEnd() {
		return $this->Html->scriptEnd();
	}

	/**
	 * Start javascript code. Rewrite <i>$this->Html->scriptStart()</i>
	 *
	 * Look at {@link http://api.cakephp.org/2.4/class-HtmlHelper.html#_scriptStart CakePHP Api}
	 * @param array $options Options for the code block
	 * @return null
	 */
	public function scriptStart($options=array()) {
		//"inline" option default false
		$options['inline'] = empty($options['inline']) ? false : $options['inline'];

		return $this->Html->scriptStart($options);
	}
}