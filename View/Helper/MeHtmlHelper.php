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
 * @copyright	Copyright (c) 2015, Mirko Pagliai for Nova Atlantis Ltd
 * @license		http://www.gnu.org/licenses/agpl.txt AGPL License
 * @link		http://git.novatlantis.it Nova Atlantis Ltd
 * @package		MeTools\View\Helper
 * @see			http://api.cakephp.org/2.5/class-HtmlHelper.html HtmlHelper
 */
App::uses('HtmlHelper', 'View/Helper');

/**
 * Provides functionalities for HTML code.
 * 
 * Rewrites the {@link http://api.cakephp.org/2.5/class-HtmlHelper.html HtmlHelper}.
 * 
 * You should use this helper as an alias, for example:
 * <code>
 * public $helpers = array('Html' => array('className' => 'MeTools.MeHtml'));
 * </code>
 */
class MeHtmlHelper extends HtmlHelper {
	/**
	 * Method that is called automatically when the method doesn't exist.
	 * If you pass no more than two parameters, it tries to generate a html tag with the name of the method.
	 * Otherwise, it provides non fatal errors on missing method calls.
	 * @param string $method Method to invoke
	 * @param array $params Array of params for the method
	 * @return string Html code
	 * @see http://api.cakephp.org/2.5/class-Helper.html#___call CakePHP API
	 * @uses tag()
	 */
	public function __call($method, $params) {
		if(count($params) <= 2)
			return self::tag($method, empty($params[0]) ? NULL : $params[0], empty($params[1]) ? NULL : $params[1]);
		
		parent::__call($method, $params);
	}
	
    /**
     * Add button classes.
	 * @param array $options Array of HTML attributes
	 * @param string $defaultClass Default class (eg. `default`, `primary`, `success`, etc)
	 * @param array $options Array of HTML attributes
	 * @uses _addOptionValue()
	 */
    public function _addButtonClasses($options, $defaultClass = 'default') {
        //If "class" doesn't contain a button style, adds "btn-default" classes
        if(empty($options['class']) || !preg_match('/btn-(default|primary|success|info|warning|danger)/', $options['class']))
			return self::_addOptionValue('class', array('btn', sprintf('btn-%s', $defaultClass)), $options);
        
		return self::_addOptionValue('class', 'btn', $options);
    }
	
	/**
	 * Adds icons to text.
	 * @param string $text Text
	 * @param array $options Array of HTML attributes
	 * @return string Text with icons
	 * @uses icons()
	 */
	public function _addIcons($text, $options) {
		if(empty($options['icon']))
			return $text;
		
		return sprintf('%s %s', self::icons($options['icon']), $text);
	}
	
	/**
	 * Adds values to an option.
	 * @param string $name Option name
	 * @param string $values Option values
	 * @param array $options Array of HTML attributes
	 * @return array Array of HTML attributes
	 */
	public function _addOptionValue($name, $values, $options) {
		//If the values are an array or multiple arrays, turns them into a string
		if(is_array($values)) {
			//If a single value is an array, turns it into a string
			$values = array_map(function($v) {
				return is_array($v) ? implode(' ', $v) : $v;
			}, $values);
			//Turns all the values into a string
			$values = implode(' ', $values);
		}
		
		//Turns the values into an array
		$values = explode(' ', $values);
		
		//Turns the current value into an array
		$options[$name] = empty($options[$name]) ? NULL : explode(' ', $options[$name]);
				
		//Adds the values to the current value, removing empty values and duplicates, and turns it into a string
		$options[$name] = implode(' ', array_unique(array_filter(am($options[$name], $values))));
		
		return $options;
	}
	
	/**
	 * Adds a default value to an option.
	 * @param string $name Option name
	 * @param string $value Option value
	 * @param array $options Array of HTML attributes
	 * @return array Array of HTML attributes
	 */
	public function _addOptionDefault($name, $value, $options) {
		$options[$name] = empty($options[$name]) ? $value : $options[$name];
		
		return $options;
	}

    /**
     * Returns an `<audio>` element.
     * @param string|array $path File path, relative to the `webroot/files/` directory or an array
	 * where each item itself can be a path string or an array containing `src` and `type` keys.
     * @param array $options Array of HTML attributes
     * @return string Html code
     * @uses media()
     */
    public function audio($path, $options = array()) {
        return self::media($path, am($options, array('tag' => 'audio')));
    }

    /**
     * Creates a badge, according to Bootstrap.
     * @param string $text Badge text
     * @param array $options HTML attributes
     * @return string Html code
     * @see http://getbootstrap.com/components/#badges Bootstrap documentation
	 * @uses _addOptionValue()
	 * @uses span()
     */
    public function badge($text, $options = array()) {
		$options = self::_addOptionValue('class', 'badge', $options);

        return self::span($text, $options);
    }

	/**
     * Creates a link with the appearance of a button.
     * 
     * This method creates a link with the appearance of a button.
     * To create a POST button, you should use the `postButton()` method provided by `MeFormHelper`.
     * Instead, to create a normal button, you should use the `button()` method provided by `MeFormHelper`.
     * @param string $title Link title
	 * @param string|array $url Cake-relative URL or array of URL parameters or external URL
	 * @param array $options Array of options and HTML attributes
	 * @param string $confirmMessage JavaScript confirmation message
	 * @return string Html code
     * @see MeFormHelper::button(), MeFormHelper::postButton()
	 * @uses _addButtonClasses()
	 * @uses _addOptionValue()
     * @uses link()
	 */
    public function button($title, $url = '#', $options = array(), $confirmMessage = FALSE) {
		$options = self::_addOptionValue('role', 'button', $options);
		$options = self::_addButtonClasses($options);
		
		return self::link($title, $url, $options, $confirmMessage);
    }
	
    /**
     * Adds a css file to the layout.
     *
     * If it's used in the layout, you should set the `inline` option to `TRUE`.
     * @param mixed $path Css filename or an array of css filenames
	 * @param array $options Array of options and HTML attributes
     * @return string Html, `<link>` or `<style>` tag
	 * @uses _addOptionDefault()
     */
    public function css($path, $options = array()) {
		$options = self::_addOptionDefault('once', TRUE, $options);
		$options = self::_addOptionDefault('inline', FALSE, $options);
		
		if(!is_array($path))
			return parent::css($path, $options);
			
		array_walk($path, function(&$v, $k, $options) {
			$v = parent::css($v, $options);
		}, $options);
			
		return implode(PHP_EOL, $path);
    }

    /**
     * Ends capturing output for a CSS block.
     * 
     * To start capturing output, see the `cssStart()` method.
     * @see cssStart()
     */
    public function cssEnd() {
        $this->_View->end();
    }

    /**
     * Starts capturing output for a CSS block.
     * 
     * To end capturing output, see the `cssEnd()` method.
     * @see cssEnd()
     */
    public function cssStart() {
        $this->_View->start('css');
    }

    /**
     * Returns the breadcrumb as a links sequence.
     * 
     * Note that it's better to use the `getCrumbList()` method, which offers better compatibility with Bootstrap.
     * @param string $separator Crumbs separator
     * @param string|array|boolean $startText The first crumb. If is an array, accepted keys are "text", "url" and "icon"
     * @return string Html code
     * @see getCrumbList()
	 * @uses div()
	 * @uses span()
     */
    public function getCrumbs($separator = '/', $startText = FALSE) {
        if(is_array($startText) && empty($startText['text']))
            $startText['text'] = FALSE;

        $separator = self::span(trim($separator), array('class' => 'separator'));

        return self::div('breadcrumb', parent::getCrumbs($separator, $startText));
    }

    /**
     * Returns the breadcrumb as a (x)html list.
     * 
     * Note that the `lastClass` option is set automatically as required by Bootstrap and separators (`separator` option) are
	 * automatically  added by Bootstrap in CSS through :before and content. So you should not use any of these two options.
	 * @param array $options Array of options and HTML attributes
     * @param string|array|boolean $startText The first crumb. If is an array, accepted keys are `text`, `url` and `icon`
     * @return string Html code
	 * @uses _addOptionValue()
     */
    public function getCrumbList($options = array(), $startText = FALSE) {
        if(is_array($startText) && empty($startText['text']))
            $startText['text'] = FALSE;

		$options = self::_addOptionValue('class', 'breadcrumb', $options);
		$options = self::_addOptionValue('escape', FALSE, $options);
		
        //Separators are automatically added by Bootstrap in CSS through :before and content.
        unset($options['separator']);

        return parent::getCrumbList(am($options, array('lastClass' => 'active')), $startText);
    }
	
	/**
	 * Creates an heading. 
	 * 
	 * This method is useful if you want to create an heading with a secondary text, according to Bootstrap.
	 * In this case you have to use the `small` option.
	 * 
	 * By default, this method creates an `<h2>` tag. To create a different tag, you have to use the `type` option.
     * @param string $text heading content
	 * @param array $options Array of options and HTML attributes
     * @return string Html code
	 * @see http://getbootstrap.com/css/#type-headings Bootstrap documentation
	 * @uses small()
	 * @uses tag()
	 */
	public function heading($text = NULL, $options = array()) {
		$type = empty($options['type']) ? 'h2' : $options['type'];
				
		if(!empty($options['small']))
			$text = sprintf('%s %s', $text, self::small($options['small']));
		
		unset($options['type'], $options['small']);
		
		return self::tag($type, $text, $options);
	}
	
	/**
	 * Creates an horizontal rule (`<hr>` tag).
     * @param array $options HTML attributes
	 * @param array $options Array of options and HTML attributes
	 * @uses tag()
	 */
	public function hr($options = array()) {
		return self::tag('hr', NULL, $options);
	}

    /**
     * Alias for `icons()` method
     * @see icons()
     */
    public function icon() {
        return call_user_func_array(array(get_class(), 'icons'), func_get_args());
    }
	
	/**
     * Returns icons. Examples:
     * <code>
     * echo $this->Html->icons('home');
     * </code>
     * <code>
     * echo $this->Html->icons(array('hand-o-right', '2x'));
     * </code>
	 * @param string|array $icons Icons
	 * @param array $options Array of options and HTML attributes
     * @return string Html code
     * @see http://fortawesome.github.io/Font-Awesome Font Awesome icons
	 * @uses _addOptionValue()
	 * @uses tag()
	 */
	public function icons($icons, $options = array()) {
        //Prepends the string "fa-" to any other class
		$icons = preg_replace('/(?<![^ ])(?=[^ ])(?!fa-)/', 'fa-', $icons);
		//Adds the "fa" class
		$options = self::_addOptionValue('class', array('fa', $icons), $options);
		
		return self::tag('i', ' ', $options);
	}
	
	/**
	 * Create an `iframe` element.
	 * @param array $options Array of options and HTML attributes
     * @return string Html code
	 */
	public function iframe($options = array()) {
		return self::tag('iframe', ' ', $options);
	}
	
	/**
	 * Creates an `<img>` element.
	 * @param string $path Image path (will be relative to `app/webroot/img/`)
	 * @param $options Array of options and HTML attributes
	 * @return string Html code
	 * @uses _addOptionValue()
	 */
    public function image($path, $options = array()) {
		$options = self::_addOptionValue('class', 'img-responsive', $options);
		
        return parent::image($path, $options);
    }
	
    /**
     * Alias for `image()` method.
     * @see image()
     */
    public function img() {
        return call_user_func_array(array(get_class(), 'image'), func_get_args());
    }
	
    /**
     * Alias for `script()` method
     * @see script()
     */
    public function js() {
        return call_user_func_array(array(get_class(), 'script'), func_get_args());
    }

    /**
     * Create a label, according to the Bootstrap component.
     * 
     * This method creates only a label element. Not to be confused with the `label()` method provided by 
	 * the `MeFormhelper`, which creates a label for a form input.
     * 
     * Supported type are: `default`, `primary`, `success`, `info`, `warning` and `danger`.
     * @param string $text Label text
     * @param array $options HTML attributes of the list tag
     * @param string $type Label type
     * @return string Html code
     * @see http://getbootstrap.com/components/#labels Bootstrap documentation
	 * @uses _addOptionValue()
	 * @uses span()
     */
    public function label($text, $options = array(), $type = 'default') {
		$options = self::_addOptionValue('class', array('label', sprintf('label-%s', $type)), $options);

        return self::span($text, $options);
    }
	
    /**
     * Returns an elements list (`<li>`).
     * @param array $elements Elements list
     * @param array $options HTML attributes of the list tag
     * @return string Html code
	 * @uses tag()
     */
	public function li($elements, $options = array()) {
		if(!is_array($elements))
			return self::tag('li', $elements, $options);
				
		array_walk($elements, function(&$v, $k, $options){
			$v = self::tag('li', $v, $options);
		}, $options);
		
		return implode(PHP_EOL, $elements);
	}
	
	/**
	 * Creates an HTML link.
	 * @param string $title The content to be wrapped by <a> tags
	 * @param string|array $url Cake-relative URL or array of URL parameters or external URL
	 * @param array $options Array of options and HTML attributes
	 * @param string $confirmMessage JavaScript confirmation message
	 * @return string Html code
	 * @uses _addIcons()
	 * @uses _addOptionDefault()
	 */
	public function link($title, $url = '#', $options = array(), $confirmMessage = FALSE) {
		$title = self::_addIcons($title, $options);
		unset($options['icon']);
		
		$options = self::_addOptionDefault('title', $title, $options);
		$options['title'] = trim(h(strip_tags($options['title'])));
				
		$options = self::_addOptionDefault('escape', FALSE, $options);
		
        return parent::link($title, $url, $options, $confirmMessage);
	}

    /**
     * Alias for `button()`
     * @see button()
     */
    public function linkButton() {
        return call_user_func_array(array(get_class(), 'button'), func_get_args());
    }

    /**
     * Returns an `<audio>` or `<video>` element.
     * @param string|array $path File path, relative to the `webroot/files/` directory or an array
	 * where each item itself can be a path string or an array containing `src` and `type` keys.
	 * @param array $options Array of options and HTML attributes
     * @return string Html code
	 * @uses _addOptionDefault()
     */
    public function media($path, $options = array()) {
		$options = self::_addOptionDefault('controls', !empty($options['controls']) || !isset($options['controls']), $options);

        return parent::media($path, $options);
    }

    /**
     * Creates a `<meta>` tag. 
     *
     * For a custom `<meta>` tag, the first parameter should be set to an array. For example:
     * <code>echo $this->Html->meta(array('name' => 'robots', 'content' => 'noindex'));</code>
     * @param string $type The title of the external resource
     * @param mixed $url The address of the external resource or string for content attribute
     * @param array $options Other attributes for the generated tag
     * @return string Html code
	 * @uses _addOptionDefault()
     */
    public function meta($type, $url = NULL, $options = array()) {
		$options = self::_addOptionDefault('inline', FALSE, $options);
		
        return parent::meta($type, $url, $options);
    }
	
    /**
     * Returns a list (`<ol>` or `<ul>` tag).
     * @param array $list Elements list
     * @param array $options HTML attributes of the list tag
     * @param array $itemOptions HTML attributes of the list items
     * @param string $tag Type of list tag (ol/ul)
     * @return string Html code
	 * @uses _addIcons()
	 * @uses _addOptionValue()
     */
    public function nestedList($list, $options = array(), $itemOptions = array(), $tag = 'ul') {
		if(!empty($itemOptions['icon'])) {
			$options['icon'] = $itemOptions['icon'];
			unset($itemOptions['icon']);
		}
		
        if(!empty($options['icon'])) {
			$options = self::_addOptionValue('class', 'fa-ul', $options);
			$options = self::_addOptionValue('icon', 'li', $options);
			
			foreach($list as $k => $text)
				$list[$k] = self::_addIcons($text, $options);
			
			unset($options['icon']);
        }

		return parent::nestedList($list, $options, $itemOptions, $tag);
    }

    /**
     * Returns an ordered list (`<ol>` tag).
     * @param array $list Elements list
     * @param array $options HTML attributes of the list tag
     * @param array $itemOptions HTML attributes of the list items
     * @return string Html code
     * @uses nestedList() to create the list
     */
    public function ol($list, $options = array(), $itemOptions = array()) {
        return self::nestedList($list, $options, $itemOptions, 'ol');
    }

    /**
     * Returns a formatted `<p>` tag.
     * @param type $class Class name
     * @param string $text Paragraph text
	 * @param array $options Array of options and HTML attributes
     * @return string Html code
	 * @uses _addIcons()
     */
    public function para($class, $text, $options = array()) {
		$text = self::_addIcons($text, $options);
        unset($options['icon']);

        return parent::para($class, $text, $options);
    }
	
	/**
	 * Returns a `<pre>` tag.
	 * 
	 * For use with SyntaxHighlighter, you can use the `brush` option.
     * @param string $text Pre text
	 * @param array $options Array of options and HTML attributes
     * @return string Html code
	 * @uses _addOptionValue()
	 * @uses tag()
	 */
	public function pre($text, $options = array()) {
		if(!empty($options['brush'])) {
			$options = self::_addOptionValue('class', sprintf('brush: %s', $options['brush']), $options);
			unset($options['brush']);
		}
		
		return self::tag('pre', $text, $options);
	}
	
    /**
     * Adds a js file to the layout.
     * 
     * If it's used in the layout, you should set the `inline` option to `TRUE`.
     * @param mixed $url Javascript files as string or array
	 * @param array $options Array of options and HTML attributes
     * @return mixed String of `<script />` tags or NULL if `$inline` is FALSE or if `$once` is TRUE
	 * and the file has been included before
	 * @uses _addOptionDefault()
     */
    public function script($url, $options = array()) {
		$options = self::_addOptionDefault('inline', FALSE, $options);
		
		if(!is_array($url))
			return parent::script($url, $options);
				
		array_walk($url, function(&$v, $k, $options) {
			$v = parent::script($v, $options);
		}, $options);
			
		return implode(PHP_EOL, $url);
    }

    /**
     * Returns a Javascript code block.
     * @param string $code Javascript code
	 * @param array $options Array of options and HTML attributes
     * @return mixed A script tag or NULL
	 * @uses _addOptionDefault()
     */
    public function scriptBlock($code, $options = array()) {
		$options = self::_addOptionDefault('inline', FALSE, $options);

        return parent::scriptBlock($code, $options);
    }

    /**
     * Starts capturing output for Javascript code.
     * 
     * To end capturing output, you can use the `scriptEnd()` method.
     * 
     * To capture output with a single method, you can also use the `scriptBlock()` method.
     * @param array $options Options for the code block
     * @return mixed A script tag or NULL
     * @see scriptBlock()
	 * @uses _addOptionDefault()
     */
    public function scriptStart($options = array()) {
		$options = self::_addOptionDefault('inline', FALSE, $options);

        return parent::scriptStart($options);
    }

    /**
     * Returns a formatted block tag.
     * @param string $name Tag name
     * @param string $text Tag content. If NULL, only a start tag will be printed
	 * @param array $options Array of options and HTML attributes
     * @return string Html code
	 * @uses _addIcons()
     */
    public function tag($name, $text = NULL, $options = array()) {
		$text = self::_addIcons($text, $options);
        unset($options['icon']);

        return parent::tag($name, $text, $options);
    }
	
    /**
	 * Creates and returns a thumbnail of an image or a video.
     * 
     * To get a thumbnail, you have to use `width` and/or `height` options. 
     * To get a square thumbnail, you have to use the `side` option.
	 * 
	 * You can use the `height` option only for image files.
     * @param string $path Image path (absolute or relative to the webroot)
	 * @param array $options Array of options and HTML attributes
     * @return string Html code
	 * @uses _addOptionValue()
     * @uses thumbUrl()
     * @uses image() to
     */
    public function thumb($path, $options = array()) {
        $path = self::thumbUrl($path, $options);
        unset($options['side'], $options['width'], $options['height']);
		
		$options = self::_addOptionValue('class', 'thumb', $options);

        return self::image($path, $options);
    }
	
    /**
	 * Creates and returns the url for a thumbnail of an image or a video.
     * 
     * To get a thumbnail, you have to use `width` and/or `height` options. 
     * To get a square thumbnail, you have to use the `side` option.
	 * 
	 * You can use the `height` option only for image files.
     * 
     * Note that to directly display a thumbnail, you should use the `thumb()` method. 
	 * This method only returns the url of the thumbnail.
     * @param string $path Image path (absolute or relative to the webroot)
	 * @param array $options Array of options and HTML attributes
     * @return string Url
     * @see thumb()
     */
    public function thumbUrl($path, $options = array()) {		
        //If the side is defined, then the width and height are NULL (we don't need these)
        if($options['side'] = empty($options['side']) ? NULL : $options['side'])
            $options['width'] = $options['height'] = NULL;
        
        $options['width'] = empty($options['width']) ? NULL : $options['width'];
        $options['height'] = empty($options['height']) ? NULL : $options['height'];
		
		$ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
		
        return parent::url(am(array('controller' => 'thumbs', 'action' => 'thumb', 'plugin' => 'me_tools', 'admin' => FALSE, 'ext' => $ext), array('?' => array('s' => $options['side'], 'w' => $options['width'], 'h' => $options['height'])), array(base64_encode($path))), TRUE);
    }

    /**
     * Returns a tip block.
     * 
     * By default, the tip block will have a title. To change the title, use the `title` option. 
	 * If the `title` option is  If you don't want to have a title, the `title` option should be `FALSE`.
     * @param string $text Tip text
     * @param array $options HTML attributes
     * @return Html code
	 * @uses _addIcons()
	 * @uses _addOptionValue()
	 * @uses _addOptionDefault()
	 * @uses div()
	 * @uses h4()
	 * @uses para()
     */
    public function tip($text, $options = array()) {
        $text = is_array($text) ? $text : array($text);
        array_walk($text, function(&$v) {
            $v = self::para(NULL, $v);
        });
        $text = self::div('tip-text', implode(PHP_EOL, $text));
		
		if(!isset($options['title']) || $options['title']) {
			$options = self::_addOptionDefault('title', __d('me_tools', 'Tip'), $options);
			$options = self::_addOptionDefault('icon', 'magic', $options);
			$options['title'] = self::_addIcons($options['title'], $options);
			
			$text = self::h4($options['title'], array('class' => 'tip-title')).PHP_EOL.$text;
		}
		
		unset($options['icon'], $options['title']);        

		$options = self::_addOptionValue('class', 'tip', $options);
		
        return self::div($options['class'], $text, $options);
    }

    /**
     * Returns an unordered list (`<ul>` tag).
     * @param array $list Elements list
     * @param array $options HTML attributes of the list tag
     * @param array $itemOptions HTML attributes of the list items
     * @return string Html code
     * @uses nestedList() to create the list
     */
    public function ul($list, $options = array(), $itemOptions = array()) {
        return self::nestedList($list, $options, $itemOptions, 'ul');
    }

    /**
     * Returns a `<video>` element
     * @param string|array $path File path, relative to the `webroot/files/` directory or an array
	 * where each item itself can be a path string or an array containing `src` and `type` keys.
	 * @param array $options Array of options and HTML attributes
     * @return string Html code
     * @uses media()
     */
    public function video($path, $options = array()) {
        return self::media($path, am($options, array('tag' => 'video')));
    }
}