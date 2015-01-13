<?php
/**
 * DropdownHelper
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
 */

/**
 * Provides functionalities for creating dropdown menus, according to Bootstrap.
 */
class DropdownHelper extends AppHelper {
    /**
     * Helpers
     * @var array
     */
    public $helpers = array('Html' => array('className' => 'MeTools.MeHtml'));
	
    /**
     * Parses and handles title and options used to create a link or a button to open a dropdown.
     * 
     * You should not use this method directly, but `button()` or `link()`.
     * @param string $title Link/button title
     * @param array $options HTML attributes and options
     * @return array Array with title and options
	 * @see button(), link()
	 * @uses MeHtmlHelper::_addOptionValue()
	 * @uses MeHtmlHelper::icon()
     */
    private function _parseLink($title, $options = array()) {
		$title = sprintf('%s %s', $title, $this->Html->icon('caret-down'));
		
		$options = $this->Html->_addOptionValue('class', 'dropdown-toggle', $options);
		$options = $this->Html->_addOptionValue('data-toggle', 'dropdown', $options);

        return array($title, $options);
    }
	
    /**
     * Creates a button to open a dropdown menu, according to Bootstrap.
     * 
     * Note that this method creates only a button. To create a dropdown menu, you should use the `dropdown()` method.
     * @param string $title Button title
	 * @param array $options Array of options and HTML attributes
     * @return string Html code
     * @see http://getbootstrap.com/components/#dropdowns Bootstrap documentation
     * @uses _parseLink() to parse options
     * @uses MeHtmlHelper::button()
     */
    public function button($title, $options = array()) {
		//Backward compatibility, if they were passed 3 arguments
        if(func_num_args() === 3)
            $options = func_get_arg(2);

        list($title, $options) = self::_parseLink($title, $options);

        return $this->Html->button($title, '#', $options);
    }
	
	/**
	 * Creates a dropdown menu, according to Bootstrap. For example:
     * <code>
     * <div class="dropdown">
     *    <?php
     *       echo $this->Html->button('Open the dropdown', array('icon' => 'fa-bell'));
     *       echo $this->Html->dropdown(array(
     *          $this->Html->link('Github', 'http://github.com', array('icon' => 'fa-github')),
     *          $this->Html->link('Stack Overflow', 'http://stackoverflow.com', array('icon' => 'fa-stack-overflow'))
     *       ));
     *    ?>
     * </div>
     * </code>
     * @param array $links Array of links for the dropdown (you should use the `MeHtmlHelper::link()` method for each link)
     * @param array $ulOptions Options for the dropdown (`<ul>` element)
     * @param array $itemOptions Options for each item (`<li>` element)
     * @return string Html code
     * @see http://getbootstrap.com/components/#dropdowns Bootstrap documentation
	 * @uses MeHtmlHelper::_addOptionValue()
	 * @uses MeHtmlHelper::ul()
	 */
	public function dropdown($links = array(), $ulOptions = array(), $itemOptions = array()) {
		$ulOptions = $this->Html->_addOptionValue('class', 'dropdown-menu', $ulOptions);
		$ulOptions = $this->Html->_addOptionValue('role', 'menu', $ulOptions);
		$itemOptions = $this->Html->_addOptionValue('role', 'presentation', $itemOptions);
				
        return $this->Html->ul($links, $ulOptions, $itemOptions);
	}
	
	/**
     * Creates a link to open a dropdown menu, according to Bootstrap.
     * 
     * Note that this method creates only a link. To create a dropdown menu, you should use the `dropdown()` method.
     * @param string $title Link title
	 * @param array $options Array of options and HTML attributes
     * @return string Html code
     * @see dropdown()
     * @see http://getbootstrap.com/components/#dropdowns Bootstrap documentation
     * @uses _parseLink() to parse options
     * @uses MeHtmlHelper::link()
	 */
	public function link($title, $options = array()) {
		//Backward compatibility, if they were passed 3 arguments
        if(func_num_args() === 3)
            $options = func_get_arg(2);
		
        list($title, $options) = self::_parseLink($title, $options);

        return $this->Html->link($title, '#', $options);
		
	}
	
    /**
     * Alias for `dropdown()` method
     * @see dropdown()
     */
    public function menu() {
        return call_user_func_array(array(get_class(), 'dropdown'), func_get_args());
    }
}