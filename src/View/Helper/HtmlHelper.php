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
 * @see			http://api.cakephp.org/3.2/class-Cake.View.Helper.HtmlHelper.html HtmlHelper
 */
namespace MeTools\View\Helper;

use Cake\View\Helper\HtmlHelper as CakeHtmlHelper;

/**
 * Provides functionalities for HTML code.
 * 
 * Rewrites the {@link http://api.cakephp.org/3.2/class-Cake.View.Helper.HtmlHelper.html HtmlHelper}.
 * 
 * You should use this helper as an alias, for example:
 * <code>
 * public $helpers = ['Html' => ['className' => 'MeTools.Html']];
 * </code>
 */
class HtmlHelper extends CakeHtmlHelper {
	/**
	 * Method that is called automatically when the method doesn't exist.
	 * 
	 * If you pass no more than two parameters, it tries to generate a html tag with the name of the method.
	 * @param string $method Method to invoke
	 * @param array $params Array of params for the method
	 * @uses tag()
	 */
	public function __call($method, $params) {
		if(count($params) <= 2)
			return self::tag($method, empty($params[0]) ? NULL : $params[0], empty($params[1]) ? [] : $params[1]);
		
		parent::__call($method, $params);
	}
	
    /**
     * Add button class
	 * @param array $options Options
	 * @param string $class Class (eg. `default`, `primary`, `success`, etc)
	 * @param array $options Options
	 * @see http://getbootstrap.com/css/#buttons-options
	 */
    public function _addButtonClass($options, $class = 'default') {
        //If "class" doesn't contain a button style, adds the "btn-default" classes
        if(empty($options['class']) || !preg_match('/btn-(default|primary|success|info|warning|danger)/', $options['class']))
			return addValue('class', ['btn', sprintf('btn-%s', $class)], $options);
        
		return addValue('class', 'btn', $options);
    }
	
	/**
	 * Adds icon or icons to text
	 * @param string $text Text
	 * @param array $options Array of HTML attributes
	 * @return string Text with icon or icons
	 * @uses icon()
	 */
	public function _addIcon($text, $options) {
		if(!empty($options['icon-align']) && $options['icon-align'] === 'right')
			return empty($options['icon']) ? $text : sprintf('%s %s', $text, self::icon($options['icon']));
		
		return empty($options['icon']) ? $text : sprintf('%s %s', self::icon($options['icon']), $text);
	}
	
    /**
     * Alias for `_addIcon()` method
     * @see _addIcon()
     */
    public function _addIcons() {
        return call_user_func_array([get_class(), '_addIcon'], func_get_args());
    }

    /**
     * Returns an `<audio>` element.
     * @param string|array $path File path, relative to the `webroot/files/` directory or an array
	 * where each item itself can be a path string or an array containing `src` and `type` keys.
     * @param array $options Array of HTML attributes
     * @return string Html code
     * @uses media()
     */
    public function audio($path, array $options = []) {
        return self::media($path, am($options, ['tag' => 'audio']));
    }
	
    /**
     * Creates a badge, according to Bootstrap.
     * @param string $text Badge text
	 * @param array $options Array of options and HTML attributes
     * @return string Html code
     * @see http://getbootstrap.com/components/#badges Bootstrap documentation
	 * @uses tag()
     */
    public function badge($text, array $options = []) {
		$options = addValue('class', 'badge', $options);

        return self::tag('span', $text, $options);
    }
	
	/**
     * Creates a link with the appearance of a button.
     * 
     * This method creates a link with the appearance of a button.
     * To create a POST button, you should use the `postButton()` method provided by `FormHelper`.
     * Instead, to create a normal button, you should use the `button()` method provided by `FormHelper`.
     * @param string $title Button title
	 * @param string|array $url Cake-relative URL or array of URL parameters or external URL
	 * @param array $options Array of options and HTML attributes
	 * @return string Html code
	 * @uses _addButtonClass()
	 * @uses link()
	 */
	public function button($title, $url = NULL, array $options = []) {
		$options = addValue('role', 'button', $options);
		$options = self::_addButtonClass($options);
		
		return self::link($title, $url, $options);
	}
	
	/**
     * Adds a css file to the layout.
     *
     * If it's used in the layout, you should set the `inline` option to `TRUE`.
     * @param mixed $path Css filename or an array of css filenames
	 * @param array $options Array of options and HTML attributes
     * @return string Html, `<link>` or `<style>` tag
	 */
	public function css($path, array $options = []) {
		$options = addDefault('block', TRUE, $options);
		
		return parent::css($path, $options);
	}

    /**
     * Ends capturing output for a CSS block.
     * 
     * To start capturing output, see the `cssStart()` method.
     * @see cssStart()
	 * @uses Cake\View\ViewBlock::end()
     */
    public function cssEnd() {
        $this->_View->end();
    }

    /**
     * Starts capturing output for a CSS block.
     * 
     * To end capturing output, see the `cssEnd()` method.
     * @see cssEnd()
	 * @uses Cake\View\ViewBlock::start()
     */
    public function cssStart() {
        $this->_View->start('css');
    }
	
	/**
	 * Returns a formatted DIV tag
	 * @param string $class CSS class name of the div element
	 * @param string $text String content that will appear inside the div element
	 * @param array $options Array of options and HTML attributes
     * @return string Html code
	 */
	public function div($class = NULL, $text = NULL, array $options = []) {
		return parent::div($class, is_null($text) ? '' : $text, $options);
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
	public function heading($text, array $options = []) {
		$type = empty($options['type']) || !preg_match('/^h[1-6]$/', $options['type']) ? 'h2' : $options['type'];
				
		if(!empty($options['small']) && is_string($options['small']))
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
	public function hr(array $options = []) {
		return self::tag('hr', NULL, $options);
	}
	
	/**
     * Returns icon or icons. Examples:
     * <code>
     * echo $this->Html->icon('home');
     * </code>
     * <code>
     * echo $this->Html->icon(['hand-o-right', '2x']);
     * </code>
	 * @param string|array $icon Icon or icons
	 * @param array $options Array of options and HTML attributes
     * @return string Html code
     * @see http://fortawesome.github.io/Font-Awesome Font Awesome icons
	 */
	public function icon($icon, array $options = []) {
        //Prepends the string "fa-" to any other class
		$icon = preg_replace('/(?<![^ ])(?=[^ ])(?!fa-)/', 'fa-', $icon);
		
		//Adds the "fa" class
		$options = addValue('class', ['fa', $icon], $options);
		
		return self::tag('i', ' ', $options);
	}

    /**
     * Alias for `icon()` method
     * @see icon()
     */
    public function icons() {
        return call_user_func_array([get_class(), 'icon'], func_get_args());
    }
	
	/**
	 * Create an `iframe` element.
	 * 
	 * You can use `$ratio` to create a responsive embed.
	 * @param string $url Url for the iframe
	 * @param array $options Array of options and HTML attributes
	 * @param string $ratio Ratio (`16by9` or `4by3`)
     * @return string Html code
	 * @see http://getbootstrap.com/components/#responsive-embed Responsive embed
	 * @uses tag()
	 * @uses div()
	 */
	public function iframe($url, array $options = [], $ratio = FALSE) {
		$options['src'] = $url;
		
		if($ratio === '16by9' || $ratio === '4by3') {
			$options = addValue('class', 'embed-responsive-item', $options);
			return self::div(sprintf('embed-responsive embed-responsive-%s', $ratio), self::tag('iframe', ' ', $options));
		}
		
		return self::tag('iframe', ' ', $options);
	}
	
	/**
	 * Creates a formatted `img` element.
	 * @param string $path Path to the image file, relative to the `app/webroot/img/` directory
	 * @param array $options Array of options and HTML attributes
     * @return string Html code
	 */
	public function image($path, array $options = []) {
		$options = addValue('class', 'img-responsive', $options);
		
		return parent::image($path, $options);
	}
	
    /**
     * Alias for `image()` method.
     * @see image()
     */
    public function img() {
        return call_user_func_array([get_class(), 'image'], func_get_args());
    }
	
    /**
     * Alias for `script()` method
     * @see script()
     */
    public function js() {
        return call_user_func_array([get_class(), 'script'], func_get_args());
    }

    /**
     * Create a label, according to the Bootstrap component.
     * 
     * This method creates only a label element. Not to be confused with the `label()` method provided by 
	 * the `Formhelper`, which creates a label for a form input.
     * 
     * Supported type are: `default`, `primary`, `success`, `info`, `warning` and `danger`.
     * @param string $text Label text
     * @param array $options HTML attributes of the list tag
     * @param string $type Label type
     * @return string Html code
     * @see http://getbootstrap.com/components/#labels Bootstrap documentation
	 * @uses tag()
     */
    public function label($text, array $options = [], $type = 'default') {
		$options = addValue('class', ['label', sprintf('label-%s', $type)], $options);

        return self::tag('span', $text, $options);
    }
	
    /**
     * Returns an elements list (`<li>`).
     * @param string|array $elements Elements list
     * @param array $options HTML attributes of the list tag
     * @return string Html code
	 * @uses tag()
     */
	public function li($elements, array $options = []) {
		if(!is_array($elements))
			return self::tag('li', $elements, $options);
				
		array_walk($elements, function(&$v, $k, $options){
			$v = self::tag('li', $v, $options);
		}, $options);
		
		return implode(PHP_EOL, $elements);
	}
	
	/**
	 * Creates an HTML link
	 * @param string $title The content to be wrapped by <a> tags
	 * @param string|array $url Cake-relative URL or array of URL parameters or external URL
	 * @param array $options Array of options and HTML attributes
	 * @return string Html code
	 * @uses _addIcon()
	 */
	public function link($title, $url = NULL, array $options = []) {
		$title = self::_addIcon($title, $options);
		unset($options['icon'], $options['icon-align']);
		
		$options = addDefault('title', $title, $options);
		$options['title'] = trim(h(strip_tags($options['title'])));

		$options = addDefault('escape', FALSE, $options);
		$options = addDefault('escapeTitle', FALSE, $options);
		
		return parent::link($title, $url, $options);
	}

    /**
     * Alias for `button()` method.
     * @see button()
     */
    public function linkButton() {
        return call_user_func_array([get_class(), 'button'], func_get_args());
    }

    /**
     * Returns an `<audio>` or `<video>` element.
     * @param string|array $path File path, relative to the `webroot/files/` directory or an array
	 * where each item itself can be a path string or an array containing `src` and `type` keys.
	 * @param array $options Array of options and HTML attributes
     * @return string Html code
     */
    public function media($path, array $options = []) {
		$options = addDefault('controls', !empty($options['controls']) || !isset($options['controls']), $options);

        return parent::media($path, $options);
    }

	/**
	 * Creates a link to an external resource and handles basic meta tags.
	 * @param string|array $type The title of the external resource
	 * @param string|array|null $content The address of the external resource or string for content attribute
	 * @param array $options Other attributes for the generated tag. If the type attribute is html,
	 *		rss, atom, or icon, the mime-type is returned
	 * @return string A completed `<link />` element
	 */
	public function meta($type, $content = NULL, array $options = []) {
		$options = addDefault('block', TRUE, $options);
		
        return parent::meta($type, $content, $options);
	}
	
	/**
     * Returns a list (`<ol>` or `<ul>` tag).
     * @param array $list Elements list
     * @param array $options HTML attributes of the list tag
     * @param array $itemOptions HTML attributes of the list items
     * @param string $tag Type of list tag (ol/ul)
     * @return string Html code
	 * @uses _addIcon()
	 */
	public function nestedList(array $list, array $options = [], array $itemOptions = []) {
		if(!empty($itemOptions['icon']))
			$options['icon'] = $itemOptions['icon'];
		
		if(!empty($options['icon'])) {
			$options = addValue('class', 'fa-ul', $options);
			$options = addValue('icon', 'li', $options);
			
			array_walk($list, function(&$v, $k, $options) {
				$v = self::_addIcon($v, $options);
			}, $options);
        }
			
		unset($options['icon'], $options['icon-align'], $itemOptions['icon']);
		
		return parent::nestedList($list, $options, $itemOptions);
	}

    /**
     * Returns an unordered list (`<ol>` tag).
     * @param array $list Elements list
     * @param array $options HTML attributes of the list tag
     * @param array $itemOptions HTML attributes of the list items
     * @return string Html code
     * @uses nestedList()
     */
    public function ol(array $list, array $options = [], array $itemOptions = []) {
        return self::nestedList($list, am($options, ['tag' => 'ol']), $itemOptions);
    }
	
    /**
     * Returns a formatted `<p>` tag.
     * @param type $class Class name
     * @param string $text Paragraph text
	 * @param array $options Array of options and HTML attributes
     * @return string Html code
	 * @uses _addIcon()
     */
    public function para($class, $text, array $options = []) {
		$text = self::_addIcon($text, $options);
        unset($options['icon'], $options['icon-align']);

        return parent::para($class, $text, $options);
    }
	
	/**
	 * Returns a `<pre>` tag.
	 * 
	 * To use with SyntaxHighlighter, you can use the `brush` option.
     * @param string $text Pre text
	 * @param array $options Array of options and HTML attributes
     * @return string Html code
	 * @uses tag()
	 */
	public function pre($text, array $options = []) {
		if(!empty($options['brush'])) {
			$options = addValue('class', sprintf('brush: %s', $options['brush']), $options);
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
     */
	public function script($url, array $options = []) {
		$options = addDefault('block', TRUE, $options);

        return parent::script($url, $options);
	}
	
    /**
     * Returns a Javascript code block.
     * @param string $code Javascript code
	 * @param array $options Array of options and HTML attributes
     * @return mixed A script tag or NULL
     */
    public function scriptBlock($code, array $options = []) {
		$options = addDefault('block', TRUE, $options);

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
     */
    public function scriptStart(array $options = []) {
		$options = addDefault('block', 'script_bottom', $options);

        return parent::scriptStart($options);
    }

	/**
	 * Returns the Shareaholic "share buttons".
	 * 
	 * Note that this code only renders the Shareaholic "share button".
	 * To add the "setup code", you have to use the `LayoutHelper`.
	 * @param string $app_id Shareaholic app ID
     * @return string Html code
	 * @see MeTools\View\Helper\LayoutHelper::shareaholic()
	 * @uses div()
	 */
	public function shareaholic($app_id) {
		return $this->div('shareaholic-canvas', ' ', ['data-app' => 'share_buttons', 'data-app-id' => $app_id]);
	}
	
    /**
     * Returns a formatted block tag.
     * @param string $name Tag name
     * @param string $text Tag content. If NULL, only a start tag will be printed
	 * @param array $options Array of options and HTML attributes
     * @return string Html code
	 * @uses _addIcon()
     */
    public function tag($name, $text = NULL, array $options = []) {
		if($name === 'button') {
			$options = addValue('role', 'button', $options);
			$options = self::_addButtonClass($options);
		}
		
		$text = self::_addIcon($text, $options);
		
		if(!empty($options['tooltip'])) {
			$options = addValue('data-toggle', 'tooltip', $options);
			$options = addDefault('title', $options['tooltip'], $options);
		}
		
        unset($options['icon'], $options['icon-align'], $options['tooltip']);

        return parent::tag($name, $text, $options);
    }

    /**
     * Returns an unordered list (`<ul>` tag).
     * @param array $list Elements list
     * @param array $options HTML attributes of the list tag
     * @param array $itemOptions HTML attributes of the list items
     * @return string Html code
     * @uses nestedList()
     */
    public function ul(array $list, array $options = [], array $itemOptions = []) {
        return self::nestedList($list, am($options, ['tag' => 'ul']), $itemOptions);
    }

    /**
     * Returns a `<video>` element
     * @param string|array $path File path, relative to the `webroot/files/` directory or an array
	 * where each item itself can be a path string or an array containing `src` and `type` keys.
	 * @param array $options Array of options and HTML attributes
     * @return string Html code
     * @uses media()
     */
    public function video($path, array $options = []) {
        return self::media($path, am($options, ['tag' => 'video']));
    }
	
	/**
	 * Adds the `viewport` meta tag. By default, it uses options as required by Bootstrap
	 * @param array $options Attributes for the generated tag. If the type attribute is html,
	 *		rss, atom, or icon, the mime-type is returned
     * @see http://getbootstrap.com/css/#overview-mobile Bootstrap documentation
     * @uses meta()
	 */
	public function viewport(array $options = []) {
		$default = [
			'initial-scale'	=> '1',
			'maximum-scale'	=> '1',
			'user-scalable'	=> 'no',
			'width'			=> 'device-width'
		];
		
		$content = http_build_query(am($default, $options), NULL, ', ');
		
		return self::meta(am(['name' => 'viewport'], compact('content')));
	}
	
	/**
	 * Adds a YouTube video.
	 * 
	 * You can use `$ratio` to create a responsive embed.
	 * @param string $id YouTube video ID
	 * @param array $options Array of options and HTML attributes
	 * @param string $ratio Ratio (`16by9` or `4by3`)
     * @return string Html code
	 * @uses iframe()
	 */
	public function youtube($id, array $options = [], $ratio = '16by9') {
		$options = addDefault('allowfullscreen', 'allowfullscreen', $options);
		$options = addDefault('height', 480, $options);
		$options = addDefault('width', 640, $options);
		
		return self::iframe(sprintf('https://www.youtube.com/embed/%s', $id), $options, $ratio);
	}
}