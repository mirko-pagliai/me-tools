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
 * @see			http://getbootstrap.com/components/#dropdowns Bootstrap documentation
 */
namespace MeTools\View\Helper;

use Cake\View\Helper;

/**
 * Provides functionalities for creating dropdown menus, according to Bootstrap.
 * 
 * The `menu()` method creates a full dropdown menu, with a link to open the menu and the menu itself.
 * 
 * Otherwise you can use the `button()` or the `link()`method, which generate a link or a button to open the menu, 
 * followed by the `dropdown()` method, which generates only the menu.
 */
class DropdownHelper extends Helper {
	/**
	 * Helpers
	 * @var array
	 */
	public $helpers = ['Html' => ['className' => 'MeTools.Html']];
	
    /**
     * Parses and handles title and options used to create a link or a button to open a dropdown.
     * 
     * You should not use this method directly, but `button()` or `link()`.
     * @param string $title Link/button title
     * @param array $options HTML attributes and options
     * @return array Array with title and options
	 * @see button(), link()
	 * @uses MeTools\View\Helper\HtmlHelper::icon()
     */
    protected function __parseLink($title, array $options = []) {
		$title = sprintf('%s %s', $title, $this->Html->icon('caret-down'));
		
		$options = addValue('class', 'dropdown-toggle', $options);
		$options = addValue('data-toggle', 'dropdown', $options);

        return [$title, $options];
    }
	
    /**
     * Creates a button to open a dropdown menu, according to Bootstrap.
     * 
     * Note that this method creates only a link. To create a full dropdown menu, you should use the `menu()` method.
     * @param string $title Button title
	 * @param array $options Array of options and HTML attributes
     * @return string Html code
     * @see menu()
     * @see http://getbootstrap.com/components/#dropdowns Bootstrap documentation
     * @uses MeTools\View\Helper\HtmlHelper::button()
     * @uses __parseLink()
     */
    public function button($title, array $options = []) {
		//Backward compatibility, if they were passed 3 arguments
		$options = func_num_args() === 3 ? func_get_arg(2) : $options;

        list($title, $options) = self::__parseLink($title, $options);

        return $this->Html->button($title, '#', $options);
    }
	
	/**
	 * Creates a dropdown menu.
     * 
     * Note that this method creates only a dropdown submenu, without the a link or a button to open the menu.
	 * To create a full dropdown menu, you should use the `menu()` method.
     * @param array $links Array of links for the dropdown (you should use the `HtmlHelper::link()` method for each link)
     * @param array $options Options for the dropdown (`<ul>` element)
     * @param array $itemOptions Options for each item (`<li>` element)
     * @return string Html code
     * @see menu()
     * @see http://getbootstrap.com/components/#dropdowns Bootstrap documentation
	 * @uses MeTools\View\Helper\HtmlHelper::ul()
	 */
	public function dropdown(array $links = [], array $options = [], array $itemOptions = []) {
		$options = addValue('class', 'dropdown-menu', $options);
		$options = addValue('role', 'menu', $options);
		$itemOptions = addValue('role', 'presentation', $itemOptions);
				
        return $this->Html->ul($links, $options, $itemOptions);
	}
	
	/**
     * Creates a link to open a dropdown menu, according to Bootstrap.
     * 
     * Note that this method creates only a link. To create a full dropdown menu, you should use the `menu()` method.
     * @param string $title Link title
	 * @param array $options Array of options and HTML attributes
     * @return string Html code
     * @see menu()
     * @see http://getbootstrap.com/components/#dropdowns Bootstrap documentation
     * @uses MeTools\View\Helper\HtmlHelper::link()
     * @uses __parseLink()
	 */
	public function link($title, array $options = []) {
		//Backward compatibility, if they were passed 3 arguments
		$options = func_num_args() === 3 ? func_get_arg(2) : $options;
		
        list($title, $options) = self::__parseLink($title, $options);

        return $this->Html->link($title, '#', $options);
	}
	
	/**
	 * Creates a full menu, according to Bootstrap. For example:
     * <code>
     * <div class="dropdown">
     *	<?php
     *		echo $this->Dropdown->menu('Open the dropdown', ['icon' => 'fa-bell'], [
     *			$this->Html->link('Github', 'http://github.com', ['icon' => 'fa-github']),
     *          $this->Html->link('Stack Overflow', 'http://stackoverflow.com', ['icon' => 'fa-stack-overflow'])
     *		]);
     *	?>
     * </div>
     * </code>
     * @param string $title Link title
	 * @param array $titleOptions Array of options and HTML attributes
     * @param array $links Array of links for the dropdown (you should use the `HtmlHelper::link()` method for each link)
     * @param array $dropdownOptions Options for the dropdown (`<ul>` element)
     * @param array $itemOptions Options for each item (`<li>` element)
     * @return string Html code
	 * @uses dropdown()
	 * @uses link()
	 */
    public function menu($title, array $titleOptions = [], array $links = [], array $dropdownOptions = [], array $itemOptions = []) {
		return implode(PHP_EOL, [
			$this->link($title, $titleOptions),
			$this->dropdown($links, $dropdownOptions, $itemOptions)
		]);
    }
}