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
 * @see			http://api.cakephp.org/2.6/class-FormHelper.html FormHelper
 */
namespace MeTools\View\Helper;

use Cake\View\Helper\HtmlHelper;
use Cake\View\View;

/**
 * MeHtml helper
 */
class MeHtmlHelper extends HtmlHelper {
    /**
     * Add button class
	 * @param array $options Options
	 * @param string $class Class (eg. `default`, `primary`, `success`, etc)
	 * @param array $options Options
	 * @uses _addValue()
	 */
    public function _addButtonClass($options, $class = 'default') {
        //If "class" doesn't contain a button style, adds the "btn-default" classes
        if(empty($options['class']) || !preg_match('/btn-(default|primary|success|info|warning|danger)/', $options['class']))
			return self::_addValue('class', array('btn', sprintf('btn-%s', $class)), $options);
        
		return self::_addValue('class', 'btn', $options);
    }
	
    /**
     * Alias for `_addOptionDefault()` method
     * @see _addOptionDefault()
     */
    public function _addDefault() {
        return call_user_func_array(array(get_class(), '_addOptionDefault'), func_get_args());
    }
	
	/**
	 * Adds icon or icons to text
	 * @param string $text Text
	 * @param array $options Array of HTML attributes
	 * @return string Text with icon or icons
	 * @uses icon()
	 */
	public function _addIcon($text, $options) {
		return empty($options['icon']) ? $text : sprintf('%s %s', self::icon($options['icon']), $text);
	}
	
    /**
     * Alias for `_addIcon()` method
     * @see _addIcon()
     */
    public function _addIcons() {
        return call_user_func_array(array(get_class(), '_addIcon'), func_get_args());
    }
	
	/**
	 * Adds a default value to an option
	 * @param string $name Option name
	 * @param string $value Option value
	 * @param array $options Options
	 * @return array Options
	 */
	public function _addOptionDefault($name, $value, $options) {
		$options[$name] = empty($options[$name]) ? $value : $options[$name];
		
		return $options;
	}
	
	/**
	 * Adds the value to an option
	 * @param string $name Option name
	 * @param string $values Option values
	 * @param array $options Options
	 * @return array Options
	 */
	public function _addOptionValue($name, $values, $options) {
		//If values are an array or multiple arrays, turns them into a string
		if(is_array($values))
			$values = implode(' ', array_map(function($v) {
				return is_array($v) ? implode(' ', $v) : $v;
			}, $values));
								
		//Merges passed values with current values
		$values = empty($options[$name]) ? explode(' ', $values) : array_merge(explode(' ', $options[$name]), explode(' ', $values));
		
		//Removes empty values and duplicates, then turns into a string
		$options[$name] = implode(' ', array_unique(array_filter($values)));
		
		return $options;
	}
    /**
     * Alias for `_addOptionValue()` method
     * @see _addOptionValue()
     */
    public function _addValue() {
        return call_user_func_array(array(get_class(), '_addOptionValue'), func_get_args());
    }
	
	/**
     * Returns icon or icons. Examples:
     * <code>
     * echo $this->Html->icon('home');
     * </code>
     * <code>
     * echo $this->Html->icon(array('hand-o-right', '2x'));
     * </code>
	 * @param string|array $icon Icon or icons
	 * @param array $options Array of options and HTML attributes
     * @return string Html code
     * @see http://fortawesome.github.io/Font-Awesome Font Awesome icons
	 * @uses _addValue()
	 */
	public function icon($icon, array $options = []) {
        //Prepends the string "fa-" to any other class
		$icon = preg_replace('/(?<![^ ])(?=[^ ])(?!fa-)/', 'fa-', $icon);
		
		//Adds the "fa" class
		$options = self::_addValue('class', array('fa', $icon), $options);
		
		return self::tag('i', ' ', $options);
	}

    /**
     * Alias for `icon()` method
     * @see icon()
     */
    public function icons() {
        return call_user_func_array(array(get_class(), 'icon'), func_get_args());
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
     * Creates a link with the appearance of a button.
     * 
     * This method creates a link with the appearance of a button.
     * To create a POST button, you should use the `postButton()` method provided by `MeFormHelper`.
     * Instead, to create a normal button, you should use the `button()` method provided by `MeFormHelper`.
     * @param string $title Button title
	 * @param string|array $url Cake-relative URL or array of URL parameters or external URL
	 * @param array $options Array of options and HTML attributes
	 * @return string Html code
	 * @uses _addButtonClass()
	 * @uses _addValue()
	 * @uses link()
	 */
	public function button($title, $url = NULL, array $options = []) {
		$options = self::_addValue('role', 'button', $options);
		$options = self::_addButtonClass($options);
		
		return self::link($title, $url, $options);
	}
	
	/**
	 * Creates an HTML link
	 * @param string $title The content to be wrapped by <a> tags
	 * @param string|array $url Cake-relative URL or array of URL parameters or external URL
	 * @param array $options Array of options and HTML attributes
	 * @return string Html code
	 * @uses _addDefault()
	 * @uses _addIcon()
	 */
	public function link($title, $url = NULL, array $options = []) {
		$title = self::_addIcon($title, $options);
		unset($options['icon']);
		
		$options = self::_addDefault('title', $title, $options);
		$options['title'] = trim(h(strip_tags($options['title'])));

		$options = self::_addDefault('escape', FALSE, $options);
		$options = self::_addDefault('escapeTitle', FALSE, $options);
		
		return parent::link($title, $url, $options);
	}
}