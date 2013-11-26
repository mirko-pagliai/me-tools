<?php
App::uses('HtmlHelper', 'View/Helper');

/**
 * Provides functionalities for HTML code.
 * 
 * You should use this helper as an alias, for example:
 * <pre>public $helpers = array('Html' => array('className' => 'MeTools.MeHtml'));</pre>
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
 * @package		MeTools\View\Helper
 */
class MeHtmlHelper extends HtmlHelper {
	/**
	 * Helpers
	 * @var array
	 */
	public $helpers = array('Form' => array('className' => 'MeTools.MeForm'));
	
	/**
	 * Cleans values to be used as html attributes, removing blank spaces and duplicates. For example:
	 * <pre>a a b  b c d e e e</pre>
	 * will become:
	 * <pre>a b c d e</pre>
	 * @param mixed $value Value as string or array
	 * @return string Cleaned value
	 */
	public function __clean() {
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
	 * Returns an audio element
	 * @param string|array $path File path, relative to the `webroot/{$options['pathPrefix']}` directory
	 * or an array where each item itself can be a path string or an array containing `src` and `type` keys.
	 * @param array $options Array of HTML attributes
	 * @return string Html, audio tag
	 */
	public function audio($path, $options = array()) {
		return self::media($path, am($options, array('tag' => 'audio')));
	}
	
	/**
	 * Creates a badge, according to the Bootstrap component.
	 * Look at {@link http://getbootstrap.com/components/#badges Bootstrap documentation}.
	 * @param string $text Badge text
	 * @param array $options HTML attributes
	 * @return string Html, badge element
	 */
	public function badge($text, $options = array()) {
		$options['class'] = empty($options['class']) ? 'badge' : self::__clean('badge', $options['class']);
		
		return self::tag('span', $text, $options);
	}
	
	/**
	 * Creates a link with the appearance of a button.
	 * 
	 * Note: this method creates a normal link with the appearance of a button.
	 * To create a POST button, you should use the `postButton()` method of the `MeForm` helper.
	 * Instead, to create a normal button, you should use the `button()` method` of the `MeForm` helper.
	 * @param string $title Link title
	 * @param mixed $url Cake-relative URL, array of URL parameters or external URL (starts with http://)
	 * @param array $options HTML attributes
	 * @param string $confirmMessage JavaScript confirmation message
	 * @return string Html, link
	 */
	public function button($title, $url = '#', $options = array(), $confirmMessage = false) {
		$options['class'] = self::__getBtnClass($options);
		
		return self::link($title, $url, $options, $confirmMessage);
	}

	/**
	 * Adds a css file to the layout.
	 *
	 * If it's used in the layout, you should set the `inline` option to `TRUE`
	 * @param mixed $path Css filename or an array of css filenames
	 * @param array $options HTML attributes
	 * @return string Html, `link` or `style` tag
	 */
	public function css($path, $options = array()) {
		//"inline" option default FALSE
		$options['inline'] = empty($options['inline']) ? false : $options['inline'];

		return parent::css($path, $options);
	}
	
	/**
	 * Ends capturing output for a CSS block.
	 */
	public function cssEnd() { $this->_View->end(); }

	/**
	 * Starts capturing output for a CSS block.
	 */
	public function cssStart() { $this->_View->start('css'); }
	
	/**
	 * Creates a dropdown menu, according to the Bootstrap component.
	 * Look at {@link http://getbootstrap.com/components/#dropdowns Bootstrap documentation}.
	 * @param string $title Dropdown link title
	 * @param array $titleOptions HTML attributes for the link title
	 * @param array $links Array of links for the menu
	 * @param array $itemOptions Options for each item of the menu
	 * @param array $ulOptions Options for the menu
	 * @param array $divOptions Options for the div wrapper
	 * @return string Html, dropdown menu
	 */
	public function dropdown($title, $titleOptions = array(), $links = array(), $itemOptions = array(), $ulOptions = array(), $divOptions = array()) {
		$title = self::linkDropdown($title, $titleOptions);
		
		$ulOptions['class'] = empty($ulOptions['class']) ? 'dropdown-menu' : self::__clean('dropdown-menu', $ulOptions['class']);
		$ulOptions['role'] = empty($ulOptions['role']) ? 'menu' : self::__clean('menu', $ulOptions['role']);
		$itemOptions['role'] = empty($itemOptions['role']) ? 'presentation' : self::__clean('presentation', $itemOptions['role']);
		$ul = self::ul($links, $ulOptions, $itemOptions);
		
		$divOptions['class'] = empty($divOptions['class']) ? 'dropdown' : self::__clean('dropdown', $divOptions['class']);
		
		return self::div($divOptions['class'], PHP_EOL.$title.PHP_EOL.$ul.PHP_EOL, $divOptions);
	}
	
	/**
	 * Returns the breadcrumb as a sequence of links.
	 * 
	 * Note that it's better to use the `getCrumbList()` method, which offers better compatibility with Bootstrap.
	 * @param string $separator Crumbs separator
	 * @param string|array|boolean $startText The first crumb. If is an array, accepted keys are "text", "url" and "icon"
	 * @return string Html, breadcrumb
	 */
	public function getCrumbs($separator = '/', $startText = false) {
		$separator = $this->tag('span', trim($separator), array('class' => 'separator'));
		
		return $this->div('breadcrumb', parent::getCrumbs($separator, $startText));
	}
	
	/**
	 * Returns the breadcrumb as a (x)html list.
	 * 
	 * Note that the `lastClass` option is set automatically as required by Bootstrap and separators (`separator` option) are automatically 
	 * added by Bootstrap in CSS through :before and content. So you should not use any of these two options.
	 * @param array $options HTML attributes
	 * @param string|array|boolean $startText The first crumb. If is an array, accepted keys are "text", "url" and "icon"
	 * @return string Html, breadcrumb
	 */
	public function getCrumbList($options = array(), $startText = false) {
		$options['class'] = empty($options['class']) ? 'breadcrumb' : self::__clean('breadcrumb', $options['class']);
		
		//Separators are automatically added by Bootstrap in CSS through :before and content.
		unset($options['separator']);
		
		return parent::getCrumbList(am($options, array('lastClass' => 'active')), $startText);
	}
	
	/**
	 * Returns icons.
	 *
	 * Examples:
	 * <code>echo $this->Html->icon('fa-home');</code>
	 * <code>echo $this->Html->icon(array('fa-home', 'fa-fw'));</code>
	 *
	 * Look at {@link http://fortawesome.github.io/Font-Awesome Font Awesome icons}
	 * @param mixed $icon Icon or icons as string or array
	 * @return string Html, icons
	 */
	public function icon($icons = null) {
		return self::tag('i', ' ', array('class' => self::__clean('fa', $icons))).' ';
	}
	
	/**
	 * Creates an IMG element.
	 * @param string $path Image path (will be relative to `app/webroot/img/`)
	 * @param $options HTML attributes
	 * @return string Html, image
	 */
	 public function image($path, $options = array()) {
		 $options['class'] = empty($options['class']) ? 'img-responsive' : self::__clean('img-responsive', $options['class']);
		 
		return parent::image($path, $options);
	}
	
	/**
	 * Alias for `image()` method
	 */
	public function img() { 
		return call_user_func_array(array('MeHtmlHelper', 'image'), func_get_args());
	}
	
	/**
	 * Alias for `script()` method
	 */
	public function js() {
		return call_user_func_array(array('MeHtmlHelper', 'script'), func_get_args());
	}
	
	/**
	 * Create a label, according to the Bootstrap component.
	 * Look at {@link http://getbootstrap.com/components/#labels Bootstrap documentation}.
	 * 
	 * This method creates only a label element. Not to be confused with the `label()` method of the `MeForm` helper, which 
	 * creates a label for a form input.
	 * 
	 * Supported type are: `default`, `primary`, `success`, `info`, `warning` and `danger`
	 * @param string $text Label text
	 * @param array $options HTML attributes of the list tag
	 * @param string $type Label type
	 * @return string Html, label
	 */
	public function label($text, $options = array(), $type = 'default') {
		$type = self::__clean('label', 'label-'.$type);
		$options['class'] = empty($options['class']) ? $type : self::__clean($type, $options['class']);
		
		return self::tag('span', $text, $options);
	}

	/**
	 * Returns element list (`li`) out of an array.
	 * 
	 * If the element is an array, then the firse value is the element and the second value are options.
	 * @param array $list Element list
	 * @param array $options HTML attributes of the list tag
	 * @return string Html, element list
	 */
	public function li($elements, $options = array()) {		
		$html = '';
		
		foreach($elements as $element) {
			//If the element is an array, then the first value is the element and the second value are element options
			//TO-DO: bug with only 1 element?
			if(is_array($element)) {
				$elementOption = am($options, $element[1]);
				$element = $element[0];
			}
			else
				$elementOption = $options;
			
			$element = empty($elementOption['icon']) ? $element : self::icon($elementOption['icon']).$element;
			unset($elementOption['icon']);
			
			$html .= $this->tag('li', $element, $elementOption).PHP_EOL;
		}
		
		return $html;		
	}
	
	/**
	 * Creates a link.
	 * 
	 * Note: this method creates a normal link.
	 * To create a POST link, you should use the `postLink()` method of the `MeForm` helper.
	 * @param string $title Link title
	 * @param mixed $url Cake-relative URL, array of URL parameters or external URL (starts with http://)
	 * @param array $options HTML attributes
	 * @param string $confirmMessage JavaScript confirmation message
	 * @return string Html, link
	 */
	public function link($title, $url = '#', $options = array(), $confirmMessage = false) {
		$options['escape'] = empty($options['escape']) ? FALSE : $options['escape'];
		
		$title = empty($options['icon']) ? $title : self::icon($options['icon']).$title;
		unset($options['icon']);
		
		return parent::link($title, $url, $options, $confirmMessage);
	}

	/**
	 * Alias for `button()`
	 */
	public function linkButton() {
		return call_user_func_array(array('MeHtmlHelper', 'button'), func_get_args());
	}
	
	/**
	 * Creates a link for a dropdown menu, according to the Bootstrap component.
	 * Look at {@link http://getbootstrap.com/components/#dropdowns Bootstrap documentation}.
	 * 
	 * Note that this method creates only a link. To create a dropdown menu, you should use the `dropdown()` method.
	 * @param string $title Link title
	 * @param array $options HTML attributes
	 * @return string Html, link
	 */
	public function linkDropdown($title, $options = array()) {
		$options['class'] = empty($options['class']) ? 'dropdown-toggle' : self::__clean('dropdown-toggle', $options['class']);
		$options['data-toggle'] = empty($options['data-toggle']) ? 'dropdown' : self::__clean('dropdown', $options['data-toggle']);

		//Adds the caret icon to the title
		$title .= '&nbsp;'.self::icon('fa-caret-down');
		
		return $this->Form->button($title, $options);
	}
	
	/**
	 * Returns an audio/video element
	 * @param string|array $path File path, relative to the `webroot/{$options['pathPrefix']}` directory
	 * or an array where each item itself can be a path string or an array containing `src` and `type` keys.
	 * @param array $options Array of HTML attributes
	 * @return string Html, audio or video tag
	 */
	public function media($path, $options = array()) {
		$options['controls'] = isset($options['controls']) && empty($options['controls']) ? FALSE : TRUE;
		
		return parent::media($path, $options);
	}

	/**
	 * Creates a meta tag. 
	 *
	 * For a custom meta tag, the first parameter should be set to an array. For example:
	 * <code>echo $this->Html->meta(array('name' => 'robots', 'content' => 'noindex'));</code>
	 * @param string $type The title of the external resource
	 * @param mixed $url The address of the external resource or string for content attribute
	 * @param array $options Other attributes for the generated tag
	 * @return string Html, meta tag
	 */
	public function meta($type, $url = null, $options = array()) {
		$options['inline'] = empty($options['inline']) ? false : $options['inline'];

		return parent::meta($type, $url, $options);
	}
	
	/** 
	 * Returns a list (`ol`/`ul`) out of an array.
	 * @param array $list Element list
	 * @param array $options HTML attributes of the list tag
	 * @param array $itemOptions HTML attributes of the list items
	 * @param string $tag Type of list tag (ol/ul)
	 * @return string Html, ol/ul list
	 */
	 public function nestedList($list, $options = array(), $itemOptions = array(), $tag = 'ul') {
		 //Adds icons, if the "icon" item option exists
		 if(!empty($itemOptions['icon'])) {
			 $options['class'] = empty($options['class']) ? 'fa-ul' : self::__clean('fa-ul', $options['class']);
			 array_walk($list, function(&$v, $k, $icon) { $v = self::icon($icon).$v; }, $itemOptions['icon']);
		 }
		 
		 return parent::nestedList($list, $options, $itemOptions, $tag);
	 }
	
	/**
	 * Returns an ordered list (`ol`) out of an array.
	 * @param array $list Element list
	 * @param array $options HTML attributes of the list tag
	 * @param array $itemOptions HTML attributes of the list items
	 * @return string Html, ordered list
	 */
	public function ol($list, $options = array(), $itemOptions = array()) {
		return self::nestedList($list, $options, $itemOptions, 'ol');
	}

	/**
	 * Adds a js file to the layout.
	 * 
	 * If it's used in the layout, you should set the `inline` option to `TRUE`
	 * @param mixed $url Javascript files as string or array
	 * @param array $options HTML attributes
	 * @return mixed String of <script /> tags or NULL if $inline is FALSE or if $once is TRUE and the file has been included before
	 */
	public function script($url, $options = array()) {
		$options['inline'] = empty($options['inline']) ? false : $options['inline'];

		return parent::script($url, $options);
	}

	/**
	 * Returns a Javascript code block.
	 * @param string $code Javascript code
	 * @param array $options HTML attributes
	 * @return string Html, javascript code
	 */
	public function scriptBlock($code, $options = array()) {
		$options['inline'] = empty($options['inline']) ? FALSE : $options['inline'];

		return parent::scriptBlock($code, $options);
	}

	/**
	 * Ends Javascript code.
	 * @return mixed A script tag or NULL
	 */
	public function scriptEnd() {
		return parent::scriptEnd();
	}

	/**
	 * Starts Javascript code.
	 * @param array $options Options for the code block
	 * @return mixed A script tag or NULL
	 */
	public function scriptStart($options=array()) {
		//"inline" option default false
		$options['inline'] = empty($options['inline']) ? FALSE : $options['inline'];

		return parent::scriptStart($options);
	}
	
	/**
	 * Returns a formatted block tag.
	 * @param string $name Tag name.
	 * @param string $text Tag content. If NULL, only a start tag will be printed
	 * @param array $options HTML attributes
	 * @return string Html, tag element
	 */
	public function tag($name, $text = null, $options = array()) {
		$text = empty($options['icon']) ? $text : self::icon($options['icon']).$text;
		unset($options['icon']);
		
		return parent::tag($name, $text, $options);
	}
	
	/**
	 * Creates (or gets, if it already exists) and returns a thumbnail.
	 * 
	 * To get the thumb, you need to use the "width" and/or the "height" option. 
	 * For square thumbs, you need to use the "side" option.
	 * @param string $path Image path (will be relative to `app/webroot/`)
	 * @param array $options HTML attributes
	 * @return string Html, tag element
	 */
	public function thumb($path, $options = array()) {
		$path = self::thumbUrl($path, $options);
		unset($options['side'], $options['width'], $options['height']);
		
		return self::image($path, $options);
	}
	
	/**
	 * Creates (or gets, if it already exists) and returns the url for a thumbnail.
	 * 
	 * To get the thumb, you need to use the "width" and/or the "height" option. 
	 * For square thumbs, you need to use the "side" option.
	 * 
	 * Note that to directly display a thumb, you should use the `thumb()` method. This method only returns the url of the thumbnail.
	 * @param string $path Image path (will be relative to `app/webroot/`)
	 * @param array $options HTML attributes
	 * @return string Html, tag element
	 */
	public function thumbUrl($path, $options = array()) {
		//If the side is defined, then the width and height are NULL (we don't need these)
		if($options['side'] = empty($options['side']) ? null : $options['side'])
			$options['width'] = $options['height'] = null;
		else {
			$options['width'] = empty($options['width']) ? null : $options['width'];
			$options['height'] = empty($options['height']) ? null : $options['height'];
		}
		
		//If it's a miniature
		if($options['side'] || $options['width'] || $options['height'])
			$path = self::url(am(
				array('controller' => 'thumbs', 'action' => 'thumb', 'plugin' => 'me_tools', 'admin' => false), 
				explode('/', $path),
				array('?' => array('s' => $options['side'], 'w' => $options['width'], 'h' => $options['height']))
			), true);
		
		return $path;
	}
	
	/**
	 * Returns a tip block.
	 * 
	 * By default, the tip block will have a title. To change the title, use the "title" option. If the "title" option is 
	 * an array, you can use "text" and "options" keys. If you don't want to have a title, the "title" option should be `FALSE`.
	 * @param string|array $text Tip text, as string or array
	 * @param array $options HTML attributes
	 * @return Html, tip block.
	 */
	public function tip($text, $options = array()) {
		$text = is_array($text) ? $text : array($text);
		array_walk($text, function(&$v) { $v = self::para('tip-text', $v); });
		$text = implode(PHP_EOL, $text);
		
		$options['class'] = empty($options['class']) ? 'tip' : self::__clean('tip', $options['class']);
		
		if(!isset($options['title']) || $title = $options['title']) {
			if(empty($title))
				$title = array('text' => __d('me_tools', 'Tip'), 'icon' => 'fa-magic');
			elseif(!is_array($title))
				$title = array('text' => $title);
			
			$title['class'] = empty($title['class']) ? 'tip-title' : self::__clean('tip-title', $title['class']);
			$title['options'] = $title;
			unset($title['options']['text']);
			
			$text = self::tag('h4', $title['text'], $title['options']).PHP_EOL.$text;
		}
		unset($options['title']);
		
		return self::div($options['class'], PHP_EOL.$text.PHP_EOL, $options).PHP_EOL;
	}
	
	/**
	 * Returns an unordered list (`ul`) out of an array.
	 * @param array $list Element list
	 * @param array $options HTML attributes of the list tag
	 * @param array $itemOptions HTML attributes of the list items
	 * @return string Html, unordered list
	 */
	public function ul($list, $options = array(), $itemOptions = array()) {
		return self::nestedList($list, $options, $itemOptions, 'ul');
	}
	
	/**
	 * Returns a video element
	 * @param string|array $path File path, relative to the `webroot/{$options['pathPrefix']}` directory
	 * or an array where each item itself can be a path string or an array containing `src` and `type` keys.
	 * @param array $options Array of HTML attributes
	 * @return string Html, video tag
	 */
	public function video($path, $options = array()) {
		return self::media($path, am($options, array('tag' => 'video')));
	}	
}