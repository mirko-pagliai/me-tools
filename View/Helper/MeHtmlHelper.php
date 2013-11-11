<?php
App::uses('HtmlHelper', 'View/Helper');

/**
 * Provides functionalities for HTML code.
 * 
 * You should use this helper as an alias, for example:
 * <code>
 * public $helpers = array('Html' => array('className' => 'MeTools.MeHtml'));
 * </code>
 *   
 * MeHtmlHelper extends {@link http://api.cakephp.org/2.4/class-HtmlHelper.html HtmlHelper}.
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
	 * Cleans values to be used as html attributes, removing blank spaces and duplicates. For example:
	 * <pre>a a b  b c d e e e</pre>
	 * will become:
	 * <pre>a b c d e</pre>
	 * @param mixed $value Value as string or array
	 * @return string Cleaned value
	 */
	public function __clean($value) {
		$values = func_get_args();
				
		if(empty($values))
			return null;
			
		//If an argument is an array, turns it into a string
		$values = array_map(function($v) { return !is_array($v) ? $v : implode(' ', $v); }, $values);
		//Turns all arguments into a string
		$values = implode(' ', $values);
		
		return implode(' ', array_unique(array_filter(explode(' ', $values))));
	}
	
	/**
	 * Get classes for a button
	 * @param array $option Button options
	 * @return string Button classes
	 */
	public function __getBtnClass($option) {
		if(empty($option['class']))
			return 'btn btn-default';
		
		//If "class" doesn't contain a button style, adds "btn-default" to class
		if(!preg_match('/btn-[a-z]+/', $class = $option['class']))
			return self::__clean('btn', 'btn-default', $class);
		else
			return self::__clean('btn', $class);
	}
	
	/**
	 * Adds a link to the breadcrumb array. Rewrites <i>$this->Html->addCrumb()</i>
	 * 
	 * You can use {@link http://fortawesome.github.io/Font-Awesome Font Awesome icons}
	 * @param string $name Text for link
	 * @param string $link URL for link (if empty it won't be a link)
	 * @param mixed $options HTML attributes
	 * @return void
	 */
	public function addCrumb($title, $link=null, $options=null) {
		//Add the icon to the title, if the "icon" option exists
		if(!empty($options['icon'])) $title = self::icon($options['icon']).$title;
		unset($options['icon']);

		return parent::addCrumb($title, $link, $options);
	}

	/**
	 * Creates an HTML link with the appearance of a button, as required 
	 * by {@link http://getbootstrap.com/css/#buttons Bootstrap}. Uses the <i>$this->link()</i> method
	 *
	 * You can use {@link http://fortawesome.github.io/Font-Awesome Font Awesome icons}. For example:
	 * <code>
	 * echo $this->Html->linkButton('my link', 'http://site.com', array('icon' => 'fa-search'));
	 * </code>
	 * @param string $title Button title
	 * @param mixed $url Cake-relative URL, array of URL parameters or external URL
	 * @param array $options HTML attributes
	 * @param string $confirmMessage JavaScript confirmation message
	 * @return string Html, link with the appearance of a button
	 */
	public function button($title, $url='#', $options=array(), $confirmMessage=false) {
		$options['class'] = self::__getBtnClass($options);

		return $this->link($title, $url, $options, $confirmMessage);
	}

	/**
	 * Adds a css file to the layout. Rewrites <i>$this->Html->css()</i>
	 *
	 * When used in the layout, you have to remember to use the "inline" option (must be set to TRUE)
	 * @param mixed $path The css filename or an array of css files
	 * @param array $options HTML attributes
	 * @return string CSS <link /> or <style /> tag, depending on the type of link
	 */
	public function css($path, $options=array()) {
		//"inline" option default FALSE
		$options['inline'] = empty($options['inline']) ? false : $options['inline'];

		return parent::css($path, $options);
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
	 * Returns the breadcrumb as a sequence of separated links. Rewrites <i>$this->Html->getCrumbs()</i>
	 * 
	 * Note that it's better to use <i>$this->getCrumbList()</i>, which offers better compatibility with Bootstrap
	 *
	 * You can use {@link http://fortawesome.github.io/Font-Awesome Font Awesome icons} for the first crumb. For example:
	 * <code>
	 * echo $this->Html->getCrumbs('/', array('text' => 'Homepage', 'icon' => 'fa-home'));
	 * </code>
	 * @param string $separator Text to separate crumbs
	 * @param string|array|boolean $startText The first crumb. If is an array, the accepted keys are "text", "url" and "icon"
	 * @return string Html, breadcrumb
	 */
	public function getCrumbs($separator='/', $startText=false) {
		//Changes the separator as required by Bootstrap
		if(!empty($separator))
			$separator = '<span class="divider">'.$separator.'</span>';
		
		return $this->tag('div', parent::getCrumbs($separator, $startText), array('class' => 'breadcrumb'));
	}
	
	/**
	 * Returns the breadcrumb. Rewrites <i>$this->Html->getCrumbList()</i>
	 *
	 * You can use {@link http://fortawesome.github.io/Font-Awesome Font Awesome icons} for the first crumb. For example:
	 * <code>
	 * echo $this->Html->getCrumbList(null, array('text' => 'Homepage', 'icon' => 'fa-home'));
	 * </code>
	 * 
	 * Note that with Bootstrap 3 you don't need to set separators. Separators are automatically added in CSS through `:after` and `content`
	 * @param array $options HTML attributes. Can also contain "separator" and "firstClass" options. The "lastClass" option is set automatically as required by Bootstrap
	 * @param string|array|boolean $startText The first crumb. If is an array, the accepted keys are "text", "url" and "icon"
	 * @return string Html, breadcrumb
	 */
	public function getCrumbList($options=array(), $startText=false) {
		//Adds the "breadcrumb" class
		$options['class'] = empty($options['class']) ? 'breadcrumb' : self::__clean($options['class'].' breadcrumb');

		//Changes the separator as required by Bootstrap
		if(!empty($options['separator']))
			$options['separator'] = '<span class="divider">'.$options['separator'].'</span>';

		//Adds the "active" class to the last element
		$options['lastClass'] = 'active';

		return parent::getCrumbList($options, $startText);
	}
	
	/**
	 * Returns an icon.
	 *
	 * Examples:
	 * <code>echo $this->Html->icon('fa-home');</code>
	 * <code>echo $this->Html->icon(array('fa-home', 'fa-fw'));</code>
	 *
	 * Look at {@link http://fortawesome.github.io/Font-Awesome Font Awesome icons}
	 * @param mixed $icon Icon or icons as string or array
	 * @return string Html, icons
	 */
	public function icon($icons=null) {
		return self::tag('i', ' ', array('class' => self::__clean('fa', $icons))).' ';
	}
	
	/**
	 * Alias for <i>$this->Html->image()</i>
	 */
	public function img() {
		$args = func_get_args(); 
		return call_user_func_array(array('MeHtmlHelper', 'image'), $args);
	}
	
	/**
	 * Alias for <i>$this->Html->script()</i>
	 */
	public function js() {
		$args = func_get_args(); 
		return call_user_func_array(array('MeHtmlHelper', 'script'), $args);
	}

	/**
	 * Builds elements of a list (`li`) out of an associative array.
	 * 
	 * If the element is an array, then the firse value is the element and the second value are options.
	 * @param array $list Elements. 
	 * @param array $options HTML attributes
	 * @return string Html, elements of a list
	 */
	public function li($list, $options=array()) {		
		$html = '';
		foreach($list as $element) {
			//If the element is not an array
			if(!is_array($element))
				$html .= $this->tag('li', $element, $options);
			//If the element is an array, then the firse value is the element and the second value are options
			else
				$html .= $this->tag('li', $element[0], am($options, $element[1]));
		}
		
		return $html;		
	}
	
	/**
	 * Creates an HTML link. Rewrites <i>$this->Html->link()</i>
	 *
	 * You can use {@link http://fortawesome.github.io/Font-Awesome Font Awesome icons}. For example:
	 * <code>
	 * echo $this->Html->link('my link', 'http://site.com', array('icon' => 'fa-search'));
	 * </code>
	 * @param string $title Link title
	 * @param mixed $url Cake-relative URL, array of URL parameters or external URL (starts with http://)
	 * @param array $options HTML attributes
	 * @param string $confirmMessage JavaScript confirmation message
	 * @return string Html, link
	 */
	public function link($title, $url='#', $options=array(), $confirmMessage=false) {
		//Adds an icon to the title
		if(!empty($options['icon'])) $title = self::icon($options['icon']).$title;
		unset($options['icon']);
		
		//"escape" option default FALSE
		$options['escape'] = empty($options['escape']) ? false : $options['escape'];
		
		//Adds the tooltip, if there's the "tooptip" option
		if(!empty($options['tooltip'])) {
			$options['data-toggle'] = 'tooltip';
			$options['title'] = $options['tooltip'];
			unset($options['tooltip']);
		}

		return parent::link($title, $url, $options, $confirmMessage);
	}

	/**
	 * Alias for <i>$this->Html->button()</i>
	 */
	public function linkButton() {
		$args = func_get_args(); 
		return call_user_func_array(array('MeHtmlHelper', 'button'), $args);
	}

	/**
	 * Creates an HTML link with the appearance of a button 
	 * for {@link http://getbootstrap.com/components/#dropdowns Bootstrap dropdowns}. Uses the <i>$this->link()</i> method
	 *
	 * You can use {@link http://fortawesome.github.io/Font-Awesome Font Awesome icons}. For example:
	 * <code>
	 * echo $this->Html->linkButton('my link', 'http://site.com', array('icon' => 'fa-search'));
	 * </code>
	 * @param string $title Button title
	 * @param mixed $url Cake-relative URL, array of URL parameters or external URL
	 * @param array $options HTML attributes
	 * @return string Html, link for dropdown
	 */
	public function linkDropdown($title, $url='#', $options=array()) {
		//Adds 'dropdown-toggle' class
		$options['class'] = empty($options['class']) ? 'dropdown-toggle' : self::__clean($options['class'].' dropdown-toggle');

		//Adds 'dropdown' data-toggle option
		$options['data-toggle'] = 'dropdown';

		//"escape" option default FALSE
		$options['escape'] = empty($options['escape']) ? false : $options['escape'];

		//Adds the caret icon to the title
		$title .= '&nbsp;'.self::icon('fa-caret-down');

		return $this->link($title, $url, $options);
	}

	/**
	 * Creates a meta tag. Rewrites <i>$this->Html->meta()</i>
	 *
	 * For a custom meta tag, the first parameter should be set to an array. For example:
	 * <code>
	 * echo $this->Html->meta(array('name' => 'robots', 'content' => 'noindex'));
	 * </code>
	 * @param string $type The title of the external resource
	 * @param mixed $url The address of the external resource or string for content attribute
	 * @param array $options Other attributes for the generated tag
	 * @return string Html, meta tag
	 */
	public function meta($type, $url=null, $options=array()) {
		//"inline" option default FALSE
		$options['inline'] = empty($options['inline']) ? false : $options['inline'];

		return parent::meta($type, $url, $options);
	}
	
	/**
	 * Builds an ordered list (`ol`) out of an associative array. Rewrites <i>$this->Html->nestedList()</i>
	 * @param array $list Elements to list
	 * @param array $options HTML attributes of the list tag
	 * @param array $itemOptions HTML attributes of the list item (`li`) tag
	 * @return string Html, ordered list
	 */
	public function ol($list, $options=array(), $itemOptions=array()) {
		return parent::nestedList($list, $options, $itemOptions, 'ol');
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
		//"inline" option default FALSE
		$options['inline'] = empty($options['inline']) ? false : $options['inline'];

		return parent::script($url, $options);
	}

	/**
	 * Generates a Javascript code block. Rewrites <i>$this->Html->scriptBlock()</i>
	 * @param string $code The Javascript code
	 * @param array $options HTML attributes
	 * @return string Html, Javascript code
	 */
	public function scriptBlock($code, $options = array()) {
		//"inline" option default FALSE
		$options['inline'] = empty($options['inline']) ? false : $options['inline'];

		return parent::scriptBlock($code, $options);
	}

	/**
	 * Ends Javascript code. Rewrites <i>$this->Html->scriptEnd()</i>
	 * @return mixed A script tag or null
	 */
	public function scriptEnd() {
		return parent::scriptEnd();
	}

	/**
	 * Starts Javascript code. Rewrites <i>$this->Html->scriptStart()</i>
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
	 * @return string Html, tag element
	 */
	public function tag($name, $text=null, $options=array()) {
		//"escape" option default FALSE
		$options['escape'] = empty($options['escape']) ? false : $options['escape'];

		return parent::tag($name, $text, $options);
	}
	
	/**
	 * Creates (or gets, if it already exists) an image thumbnail and returns the html code. Uses the <i>$this->image()</i> method
	 * 
	 * To get the thumb, you need to use the "width" and/or the "height" option. 
	 * For square thumbs, you need to use the "side" option.
	 * @param string $path Image path (relative to the webroot)
	 * @param array $options HTML attributes
	 * @return string Html, tag element
	 */
	public function thumb($path, $options = array()) {
		//If the side is defined, then the width and height are NULL (we don't need these)
		if($options['side'] = empty($options['side']) ? null : $options['side'])
			$options['width'] = $options['height'] = null;
		else {
			$options['width'] = empty($options['width']) ? null : $options['width'];
			$options['height'] = empty($options['height']) ? null : $options['height'];
		}
		
		//If it's a miniature
		if($options['side'] || $options['width'] || $options['height']) {
			$path = $this->url(am(
				array('controller' => 'thumbs', 'action' => 'thumb', 'plugin' => 'me_tools', 'admin' => false), 
				explode('/', $path),
				array('?' => array('s' => $options['side'], 'w' => $options['width'], 'h' => $options['height']))
			), true);
			
			unset($options['side'], $options['width'], $options['height']);
		}
		
		return $this->image($path, $options);
	}
	
	/**
	 * Builds an unordered list (`ul`) out of an associative array. Rewrites <i>$this->Html->nestedList()</i>
	 * @param array $list Elements to list
	 * @param array $options HTML attributes of the list tag
	 * @param array $itemOptions HTML attributes of the list item (`li`) tag
	 * @return string Html, unordered list
	 */
	public function ul($list, $options=array(), $itemOptions=array()) {
		return parent::nestedList($list, $options, $itemOptions, 'ul');
	}
}