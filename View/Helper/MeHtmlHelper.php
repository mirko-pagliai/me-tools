<?php
App::uses('HtmlHelper', 'View/Helper');

/**
 * Provides extended functionalities for HTML code.
 *   
 * Rewrites the {@link http://api.cakephp.org/2.4/class-HtmlHelper.html HtmlHelper}.
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
class MeHtmlHelper extends HtmlHelper {
	/**
	 * Adds a link to the breadcrumbs array. Rewrites <i>$this->Html->addCrumb()</i>
	 * @param string $name Text for link
	 * @param string $link URL for link (if empty it won't be a link)
	 * @param mixed $options HTML attributes
	 * @return void
	 */
	public function addCrumb($title, $link=null, $options=null) {
		//"escape" option default false
		$options['escape'] = empty($options['escape']) ? false : $options['escape'];

		//Add bootstrap icon to the title, if there's the 'icon' option
		$title = !empty($options['icon']) ? $this->icon($options['icon']).$title : $title;
		unset($options['icon']);

		return parent::addCrumb($title, $link, $options);
	}
	
	/**
	 * Cleans the value of an html attribute, removing blank spaces and duplicates
	 *
	 * For example, the string (and attribute value):
	 * <code>
	 * a a b  b c d e e e
	 * </code>
	 * will become:
	 * <code>
	 * a b c d e
	 * </code>
	 * @param string $value Attribute value
	 * @return string Cleaned value
	 */
	public function cleanAttribute($value) {
		//Trim and remove blank spaces
		$value = preg_replace('/\s+/', ' ', trim($value));
		//Remove duplicates
		$value = implode(' ', array_unique(explode(' ', $value)));

		return $value;
	}

	/**
	 * Adds a css file to the layout. Rewrites <i>$this->Html->css()</i>
	 * @param mixed $filename The css filename or an array of css files
	 * @param array $options HTML attributes
	 * @param string $rel The value of the generated tag's rel attribute. If null, 'stylesheet' will be use
	 * @return string CSS <link /> or <style /> tag, depending on the type of link
	 */
	public function css($filename, $options=array(), $rel=null) {
		//"inline" option default false
		$options['inline'] = empty($options['inline']) ? false : $options['inline'];

		return parent::css($filename, $rel, $options);
	}

	/**
	 * Ends CSS code. Rewrites <i>ViewBlock::end()</i>
	 */
	public function cssEnd() {
		$this->_View->end();
	}

	/**
	 * Starts CSS code. Rewrites <i>ViewBlock::start('css')</i>
	 */
	public function cssStart() {
		$this->_View->start('css');
	}

	/**
	 * Returns breadcrumbs as an (x)html list. Rewrites <i>$this->Html->getCrumbList()</i>
	 * @param array $options HTML attributes. Can also contain "separator" and "firstClass" options. The "lastClass" option is set automatically as required by Bootstrap
	 * @param mixed $startText The first crumb, if false it defaults to first crumb in array
	 * @return string Breadcrumbs (x)html list
	 */
	public function getCrumbList($options=array(), $startText=false) {
		//Add the "breadcrumb" class
		$options['class'] = empty($options['class']) ? 'breadcrumb' : $this->cleanAttribute($options['class'].' breadcrumb');

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

		return parent::getCrumbList($options, $startText);
	}

	/**
	 * Returns an icon
	 *
	 * Example:
	 * <code>
	 * echo $this->MeHtml->icon('icon-ok icon-white');
	 * </code>
	 * <code>
	 * echo $this->MeHtml->icon(array('icon-ok', 'icon-white'));
	 * </code>
	 *
	 * Look at {@link http://twitter.github.com/bootstrap/base-css.html#icons Bootstrap icons} and {@link http://fortawesome.github.com/Font-Awesome/#icons-new Font Awesome icons}
	 * @param array $icon Icon or icons as string or an array of icons
	 * @return string Html
	 */
	public function icon($icon=null) {
		//If array, implodes
		if(is_array($icon))
			$icon = implode(' ', $icon);
		return '<i class="'.$this->cleanAttribute($icon).'"></i> ';
	}

	/**
	 * Creates an HTML link. Rewrites <i>$this->Html->link()</i>
	 *
	 * You can use {@link http://twitter.github.com/bootstrap/base-css.html#icons bootstrap icons} using 'icon' option. Example:
	 * <code>
	 * echo $this->Html->link('my link', 'http://site.com', array('icon' => 'icon-search'));
	 * </code>
	 * @param string $title Link title
	 * @param mixed $url Cake-relative URL, array of URL parameters or external URL (starts with http://)
	 * @param array $options HTML attributes
	 * @param string $confirmMessage JavaScript confirmation message
	 * @return string Html
	 */
	public function link($title, $url=null, $options=array(), $confirmMessage=false) {
		//"escape" option default false
		$options['escape'] = empty($options['escape']) ? false : $options['escape'];

		//Adds bootstrap icon to the title, if there's the 'icon' option
		$title = !empty($options['icon']) ? $this->icon($options['icon']).$title : $title;
		unset($options['icon']);

		return parent::link($title, $url, $options, $confirmMessage);
	}

	/**
	 * Creates an HTML link with the appearance of a button, as required by {@link http://twitter.github.com/bootstrap/base-css.html#buttons Bootstrap}.
	 * 
	 * Uses the <i>$this->link()</i> method
	 * @param string $title Button title
	 * @param mixed $url Cake-relative URL, array of URL parameters or external URL (starts with http://)
	 * @param array $options HTML attributes
	 * @param string $confirmMessage JavaScript confirmation message
	 * @return string Html
	 */
	public function linkButton($title, $url=null, $options=array(), $confirmMessage=false) {
		//Adds the 'btn' class
		$options['class'] = empty($options['class']) ? 'btn' : $this->cleanAttribute($options['class'].' btn');

		return $this->link($title, $url, $options, $confirmMessage);
	}

	/**
	 * Creates an HTML link with the appearance of a button for {@link http://twitter.github.io/bootstrap/components.html#buttonDropdowns Bootstrap dropdowns}.
	 * @param string $title Button title
	 * @param mixed $url Cake-relative URL, array of URL parameters or external URL (starts with http://)
	 * @param array $options HTML attributes
	 * @return string Html
	 */
	public function linkDropdown($title, $url='#', $options=array()) {
		//Adds 'btn' and 'dropdown-toggle' classes
		$options['class'] = empty($options['class']) ? 'dropdown-toggle' : $this->cleanAttribute($options['class'].' dropdown-toggle');

		//Adds 'dropdown' data-toggle
		$options['data-toggle'] = 'dropdown';

		//"escape" option default false
		$options['escape'] = empty($options['escape']) ? false : $options['escape'];

		$title .= ' <span class="caret"></span>';

		return $this->link($title, '#', $options);
	}

	/**
	 * Creates a meta tag. Rewrites <i>$this->Html->meta()</i>
	 *
	 * For a custom meta tag, the first parameter should be set to an array. For example:
	 *
	 * <code>
	 * echo $this->MeHtml->meta(array('name' => 'robots', 'content' => 'noindex'));
	 * </code>
	 * @param string $type The title of the external resource
	 * @param mixed $url The address of the external resource or string for content attribute
	 * @param array $options Other attributes for the generated tag
	 * @return string Html
	 */
	public function meta($type, $url=null, $options=array()) {
		//"inline" option default false
		$options['inline'] = empty($options['inline']) ? false : $options['inline'];

		return parent::meta($type, $url, $options);
	}

	/**
	 * Adds a js file to the layout. Rewrites <i>$this->Html->script()</i>
	 *
	 * When used in the layout, remember to use the "inline" option (must be set to TRUE)
	 * @param mixed $url String or array of javascript files to include
	 * @param array $options HTML attributes
	 * @return mixed String of <script /> tags or null if $inline is false or if $once is true and the file has been included before
	 */
	public function script($url, $options=array()) {
		//"inline" option default false
		$options['inline'] = empty($options['inline']) ? false : $options['inline'];

		return parent::script($url, $options);
	}

	/**
	 * Generates a javascript code block containing $code. Rewrites <i>$this->Html->scriptBlock()</i>
	 * @param string $code The code to go in the script tag
	 * @param array $options HTML attributes
	 * @return string Html
	 */
	public function scriptBlock($code, $options = array()) {
		//"inline" option default false
		$options['inline'] = empty($options['inline']) ? false : $options['inline'];

		return parent::scriptBlock($code, $options);
	}

	/**
	 * Ends javascript code. Rewrites <i>$this->Html->scriptEnd()</i>
	 * @return mixed A script tag or null
	 */
	public function scriptEnd() {
		return parent::scriptEnd();
	}

	/**
	 * Starts javascript code. Rewrites <i>$this->Html->scriptStart()</i>
	 * @param array $options Options for the code block
	 * @return mixed A script tag or null
	 */
	public function scriptStart($options=array()) {
		//"inline" option default false
		$options['inline'] = empty($options['inline']) ? false : $options['inline'];

		return parent::scriptStart($options);
	}

	/**
	 * Returns a formatted block tag, ie `<div>`, `<span>`, `<p>`. Rewrites <i>$this->Html->tag()</i>
	 * @param string $name Tag name
	 * @param string $text Tag content. If null, only a start tag will be printed
	 * @param array $options HTML attributes
	 * @return string The formatted tag element
	 */
	public function tag($name, $text=null, $options=array()) {
		//"escape" option default false
		$options['escape'] = empty($options['escape']) ? false : $options['escape'];

		return parent::tag($name, $text, $options);
	}
}