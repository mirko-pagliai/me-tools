<?php
/**
 * MeHtmlHelper
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
 * @see			http://api.cakephp.org/2.4/class-HtmlHelper.html HtmlHelper
 */
App::uses('HtmlHelper', 'View/Helper');

/**
 * Provides functionalities for HTML code.
 * 
 * Rewrites {@link http://api.cakephp.org/2.4/class-HtmlHelper.html HtmlHelper}.
 * 
 * You should use this helper as an alias, for example:
 * <code>
 * public $helpers = array('Html' => array('className' => 'MeTools.MeHtml'));
 * </code>
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
	 * Parses and handles title and options used to create a link or a button to open a dropdown.
	 * 
	 * You should not use this method directly, but `buttonDropdown()` or `linkDropdown()`.
	 * @param string $title Link/button title
	 * @param array $options HTML attributes
	 * @return array Title and options
	 */
	private function __parseLinkDropdown($title, $options = array()) {		
		$options['class'] = empty($options['class']) ? 'dropdown-toggle' : self::__clean('dropdown-toggle', $options['class']);
		$options['data-toggle'] = empty($options['data-toggle']) ? 'dropdown' : self::__clean('dropdown', $options['data-toggle']);

		$title .= '&nbsp;'.self::icon('fa-caret-down');
		
		return array($title, $options);
	}
	
	/**
	 * Returns an audio element
	 * @param string|array $path File path, relative to the `webroot/{$options['pathPrefix']}` directory
	 * or an array where each item itself can be a path string or an array containing `src` and `type` keys.
	 * @param array $options Array of HTML attributes
	 * @return string Html, audio tag
	 * @uses media()
	 */
	public function audio($path, $options = array()) {
		return self::media($path, am($options, array('tag' => 'audio')));
	}
	
	/**
	 * Creates a badge, according to the Bootstrap component.
	 * @param string $text Badge text
	 * @param array $options HTML attributes
	 * @return string Html, badge element
	 * @see http://getbootstrap.com/components/#badges Bootstrap documentation
	 */
	public function badge($text, $options = array()) {
		$options['class'] = empty($options['class']) ? 'badge' : self::__clean('badge', $options['class']);
		
		return self::tag('span', $text, $options);
	}
	
	/**
	 * Creates a link with the appearance of a button.
	 * 
	 * This method creates a link with the appearance of a button.
	 * To create a POST button, you should use the `postButton()` method provided by the `MeForm` helper.
	 * Instead, to create a normal button, you should use the `button()` method provided by the `MeForm` helper.
	 * @param string $title Link title
	 * @param mixed $url Cake-relative URL, array of URL parameters or external URL (starts with http://)
	 * @param array $options HTML attributes
	 * @param string $confirmMessage JavaScript confirmation message
	 * @return string Html, link
	 * @see MeFormHelper::button(), MeFormHelper::postButton()
	 * @uses link()
	 */
	public function button($title, $url = '#', $options = array(), $confirmMessage = false) {
		return self::link($title, $url, am($options, array('class' => self::__getBtnClass($options))), $confirmMessage);
	}
	
	/**
	 * Creates a button to open a dropdown menu, according to the Bootstrap component.
	 * 
	 * Note that this method creates only a button. To create a dropdown menu, you should use the `dropdown()` method.
	 * @param string $title Button title
	 * @param array $options HTML attributes
	 * @return string Html, button
	 * @see dropdown()
	 * @see http://getbootstrap.com/components/#dropdowns Bootstrap documentation
	 * @uses __parseLinkDropdown() to parse options
	 * @uses button() to get the button
	 */
	public function buttonDropdown($title, $options = array()) {
		//Backward compatibility, in which case they are 3 passed arguments
		if(func_num_args()===3)
			$options = func_get_arg(2);
		
		list($title, $options) = self::__parseLinkDropdown($title, $options);
				
		return self::button($title, '#', $options);
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
		/**
		 * From 2.4, the API for HtmlHelper::css() has been changed and the 
		 * method accepts only two options. This code ensures backward compatibility.
		 */
		if(!is_array($options)) {
			$rel = $options;
			$options = array();
			if($rel)
				$options['rel'] = $rel;
			
			if(func_num_args() > 2)
				$options = func_get_arg(2) + $options;
			
			unset($rel);
		}

		$options['inline'] = empty($options['inline']) ? false : $options['inline'];

		return parent::css($path, $options);
	}
	
	/**
	 * Ends capturing output for a CSS block.
	 * 
	 * To start capturing output, see the `cssStart()` method.
	 * @see cssStart()
	 */
	public function cssEnd() { $this->_View->end(); }

	/**
	 * Starts capturing output for a CSS block.
	 * 
	 * To end capturing output, see the `cssEnd()` method.
	 * @see cssEnd()
	 */
	public function cssStart() { $this->_View->start('css'); }
	
	/**
	 * Creates a dropdown, according to the Bootstrap component. For example:
	 * <code>
	 * <div class="dropdown">
	 *    <?php
	 *       echo $this->Html->buttonDropdown('Open the dropdown', array('icon' => 'fa-bell'));
	 *       echo $this->Html->dropdown(array(
	 *          $this->Html->link('Github', 'http://github.com', array('icon' => 'fa-github')),
	 *          'divider',
	 *          $this->Html->link('Stack Overflow', 'http://stackoverflow.com', array('icon' => 'fa-stack-overflow'))
	 *       ));
	 *    ?>
	 * </div>
	 * </code>
	 * @param array $links Array of links for the dropdown (you should use the `link()` method for each link) or "divider" to create a divider
	 * @param array $ulOptions Options for the dropdown
	 * @param array $itemOptions Options for each item (`li`) of the dropdown
	 * @return string Html, dropdown menu
	 * @see http://getbootstrap.com/components/#dropdowns Bootstrap documentation
	 * @uses ul() to create the ul element
	 */
	public function dropdown($links = array(), $ulOptions = array(), $itemOptions = array()) {		
		$ulOptions['class'] = empty($ulOptions['class']) ? 'dropdown-menu' : self::__clean('dropdown-menu', $ulOptions['class']);
		$ulOptions['role'] = empty($ulOptions['role']) ? 'menu' : self::__clean('menu', $ulOptions['role']);
		$itemOptions['role'] = empty($itemOptions['role']) ? 'presentation' : self::__clean('presentation', $itemOptions['role']);
		
		//Sets eventual separators
		 array_walk($links, function(&$v) {
			 if($v==='divider' || $v==='separator')
				 $v = array(null, array('class' => 'divider'));
		 });
		
		return self::ul($links, $ulOptions, $itemOptions);
	}
	
	/**
	 * Returns the breadcrumb as links sequence.
	 * 
	 * Note that it's better to use the `getCrumbList()` method, which offers better compatibility with Bootstrap.
	 * @param string $separator Crumbs separator
	 * @param string|array|boolean $startText The first crumb. If is an array, accepted keys are "text", "url" and "icon"
	 * @return string Html, breadcrumb
	 * @see getCrumbList()
	 */
	public function getCrumbs($separator = '/', $startText = false) {
		if(is_array($startText) && empty($startText['text']))
			$startText['text'] = FALSE;
		
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
		if(is_array($startText) && empty($startText['text']))
			$startText['text'] = FALSE;
		
		$options['class'] = empty($options['class']) ? 'breadcrumb' : self::__clean('breadcrumb', $options['class']);
		
		//Separators are automatically added by Bootstrap in CSS through :before and content.
		unset($options['separator']);
		
		return parent::getCrumbList(am($options, array('lastClass' => 'active')), $startText);
	}
	
	/**
	 * Returns icons. Examples:
	 * <code>
	 * echo $this->Html->icon('fa-home');
	 * </code>
	 * <code>
	 * echo $this->Html->icon(array('fa-home', 'fa-fw'));
	 * </code>
	 * @param mixed $icons Icon or icons as string or array
	 * @return string Html, icons
	 * @see http://fortawesome.github.io/Font-Awesome Font Awesome icons
	 */
	public function icon($icons = null) {
		return self::tag('i', ' ', array('class' => self::__clean('fa', $icons))).' ';
	}
	
	/**
	 * Alias for `icon()` method
	 * @see icon()
	 */
	public function icons() { 
		return call_user_func_array(array('MeHtmlHelper', 'icons'), func_get_args());
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
	 * @see image()
	 */
	public function img() { 
		return call_user_func_array(array('MeHtmlHelper', 'image'), func_get_args());
	}
	
	/**
	 * Alias for `script()` method
	 * @see script()
	 */
	public function js() {
		return call_user_func_array(array('MeHtmlHelper', 'script'), func_get_args());
	}
	
	/**
	 * Create a label, according to the Bootstrap component.
	 * 
	 * This method creates only a label element. Not to be confused with the `label()` method of the `MeForm` helper, which 
	 * creates a label for a form input.
	 * 
	 * Supported type are: `default`, `primary`, `success`, `info`, `warning` and `danger`
	 * @param string $text Label text
	 * @param array $options HTML attributes of the list tag
	 * @param string $type Label type
	 * @return string Html, label
	 * @see http://getbootstrap.com/components/#labels Bootstrap documentation
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
	 * @param array $elements Element list
	 * @param array $options HTML attributes of the list tag
	 * @return string Html, element list
	 * @todo bug with only 1 element?
	 */
	public function li($elements, $options = array()) {		
		$html = '';
		
		foreach($elements as $element) {
			//If the element is an array, then the first value is the element and the second value are element options
			if(is_array($element)) {
				$elementOption = am($options, $element[1]);
				$element = $element[0];
			}
			else
				$elementOption = $options;
			
			$element = empty($elementOption['icon']) ? $element : self::icon($elementOption['icon']).$element;
			unset($elementOption['icon']);
			
			$html .= $this->tag('li', $element, $elementOption);
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
	 * @see button()
	 */
	public function linkButton() {
		return call_user_func_array(array('MeHtmlHelper', 'button'), func_get_args());
	}
	
	/**
	 * Creates a link to open a dropdown menu, according to the Bootstrap component.
	 * 
	 * Note that this method creates only a link. To create a dropdown menu, you should use the `dropdown()` method.
	 * @param string $title Link title
	 * @param array $options HTML attributes
	 * @return string Html, link
	 * @see dropdown()
	 * @see http://getbootstrap.com/components/#dropdowns Bootstrap documentation
	 * @uses __parseLinkDropdown() to parse options
	 * @uses link() to get the link
	 */
	public function linkDropdown($title, $options = array()) {
		//Backward compatibility, in which case they are 3 passed arguments
		if(func_num_args()===3)
			$options = func_get_arg(2);
		
		list($title, $options) = self::__parseLinkDropdown($title, $options);
				
		return self::link($title, '#', $options);
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
		 if(!empty($itemOptions['icon'])) {
			 $options['class'] = empty($options['class']) ? 'fa-ul' : self::__clean('fa-ul', $options['class']);
			 array_walk($list, function(&$v, $k, $icon) { $v = self::icon($icon).$v; }, $itemOptions['icon']);
		 }
		 
		 return self::tag($tag, self::li($list, $itemOptions), $options);
	 }
	
	/**
	 * Returns an ordered list (`ol`) out of an array.
	 * @param array $list Element list
	 * @param array $options HTML attributes of the list tag
	 * @param array $itemOptions HTML attributes of the list items
	 * @return string Html, ordered list
	 * @uses nestedList() to create the list
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
	 * @return mixed String of <script /> tags or NULL if `$inline` is FALSE or if `$once` is TRUE and the file has been included before
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
	 * Ends capturing output for Javascript code.
	 * 
	 * To start capturing output, see the `scriptStart()` method.
	 * 
	 * To capture output with a single method, you can also use the `scriptBlock()` method.
	 * @return mixed A script tag or NULL
	 * @see scriptBlock()
	 * @see scriptStart()
	 */
	public function scriptEnd() {
		return parent::scriptEnd();
	}

	/**
	 * Starts capturing output for Javascript code.
	 * 
	 * To end capturing output, see the `scriptEnd()` method.
	 * 
	 * To capture output with a single method, you can also use the `scriptBlock()` method.
	 * @param array $options Options for the code block
	 * @return mixed A script tag or NULL
	 * @see scriptBlock()
	 * @see scriptEnd()
	 */
	public function scriptStart($options=array()) {
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
	 * @uses thumbUrl() to get the url thumb
	 * @uses image() to display the thumb
	 */
	public function thumb($path, $options = array()) {
		$path = self::thumbUrl($path, $options);
		unset($options['side'], $options['width'], $options['height']);
		
		return self::image($path, $options);
	}
	
	/**
	 * Creates (or gets, if it already exists) and returns the url for a thumbnail.
	 * 
	 * To get the thumb, you need to use `width` and/or `height` options. 
	 * For square thumbs, you need to use the `side` option.
	 * 
	 * Note that to directly display a thumb, you should use the `thumb()` method. This method only returns the url of the thumbnail.
	 * @param string $path Image path (will be relative to `app/webroot/`)
	 * @param array $options HTML attributes
	 * @return string Html, tag element
	 * @see thumb()
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
		$text = implode('', $text);
		
		$options['class'] = empty($options['class']) ? 'tip' : self::__clean('tip', $options['class']);
		
		if(!isset($options['title']) || $title = $options['title']) {
			if(empty($title))
				$title = array('text' => __d('me_tools', 'Tip'), 'icon' => 'fa-magic');
			elseif(!is_array($title))
				$title = array('text' => $title);
			
			$title['class'] = empty($title['class']) ? 'tip-title' : self::__clean('tip-title', $title['class']);
			$title['options'] = $title;
			unset($title['options']['text']);
			
			$text = self::tag('h4', $title['text'], $title['options']).$text;
		}
		unset($options['title']);
		
		return self::div($options['class'], $text, $options);
	}
	
	/**
	 * Returns an unordered list (`ul`) out of an array.
	 * @param array $list Element list
	 * @param array $options HTML attributes of the list tag
	 * @param array $itemOptions HTML attributes of the list items
	 * @return string Html, unordered list
	 * @uses nestedList() to create the list
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
	 * @uses media()
	 */
	public function video($path, $options = array()) {
		return self::media($path, am($options, array('tag' => 'video')));
	}	
}