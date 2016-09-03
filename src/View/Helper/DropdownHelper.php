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
 * @author      Mirko Pagliai <mirko.pagliai@gmail.com>
 * @copyright   Copyright (c) 2016, Mirko Pagliai for Nova Atlantis Ltd
 * @license     http://www.gnu.org/licenses/agpl.txt AGPL License
 * @link        http://git.novatlantis.it Nova Atlantis Ltd
 * @see         http://getbootstrap.com/components/#dropdowns Bootstrap documentation
 */
namespace MeTools\View\Helper;

use Cake\View\Helper;

/**
 * Provides functionalities for creating dropdown menus, according to Bootstrap.
 *
 * Example:
 * <code>
 * $this->Dropdown->start('My dropdown');
 *
 * echo $this->Html->link('First link', '/first');
 * echo $this->Html->link('Second link', '/second');
 *
 * echo $this->Dropdown->end();
 * </code>
 *
 * Or using the `menu()` method:
 * <code>
 * echo $this->Dropdown->menu('My dropdown', [
 *      $this->Html->link('First link', '/first'),
 *      $this->Html->link('Second link', '/second'),
 * ]);
 * </code>
 *
 * You can also use it as a callback.
 * For example, this creates a dropdown menu as an element of a navbar:
 * <code>
 * $this->Html->ul([
 *      $this->Html->link('Home', '/'),
 *      //This is the dropdown menu
 *      call_user_func(function() {
 *          $this->Dropdown->start('My dropdown');
 *
 *          echo $this->Html->link('First link', '/first');
 *          echo $this->Html->link('Second link', '/second');
 *
 *          echo $this->Dropdown->end();
 *      }),
 *      $this->Html->link('Other main link', '#')
 * ], ['class' => 'nav navbar-nav']);
 * </code>
 */
class DropdownHelper extends Helper
{
    /**
     * Helpers
     * @var array
     */
    public $helpers = ['Html' => ['className' => 'MeTools.Html']];

    /**
     * Start link. This link allows the opening of the dropdown menu
     * @var string
     */
    protected $_start;

    /**
     * Wrap method about `start()` and `end()` methods, which are called
     *  consecutively
     * @param string $title Title for the opening link
     * @param array $menu Content for the dropdown menu, for example an array
     *  of links
     * @param array $titleOptions HTML attributes and options for the opening
     *  link
     * @param array $listOptions HTML attributes and options for the list
     * @param array $itemOptions HTML attributes and options for each list item
     * @return string|void
     */
    public function menu(
        $title,
        array $menu,
        array $titleOptions = [],
        array $listOptions = [],
        array $itemOptions = []
    ) {
        $this->start($title, $titleOptions);
        
        array_walk($menu, function ($menu) {
            echo $menu;
        });
        
        return $this->end($listOptions, $itemOptions);
    }
    
    /**
     * Starts a dropdown. It captures links for the dropdown menu output until
     *  `DropdownHelper::end()` is called.
     *
     * Arguments and options regarding the link that allows the opening of the
     *  dropdown menu.
     * @param string $title Title for the opening link
     * @param array $titleOptions HTML attributes and options for the opening
     *  link
     * @return void
     * @uses $_start
     */
    public function start($title, array $titleOptions = [])
    {
        $title = sprintf('%s %s', $title, $this->Html->icon('caret-down'));

        $titleOptions = optionValues([
            'aria-expanded' => 'false',
            'aria-haspopup' => 'true',
            'class' => 'dropdown-toggle',
            'data-toggle' => 'dropdown',
        ], $titleOptions);
        
        $this->_start = $this->Html->link($title, '#', $titleOptions);
        
        ob_start();
    }
    
    /**
     * End a buffered section of dropdown menu capturing.
     *
     * Arguments and options regarding the list of the dropdown menu.
     * @param array $listOptions HTML attributes and options for the list
     * @param array $itemOptions HTML attributes and options for each list item
     * @return string|void
     * @uses $_start
     */
    public function end(array $listOptions = [], array $itemOptions = [])
    {
        $buffer = ob_get_contents();
        
        if (empty($buffer)) {
            return;
        }
        
        ob_end_clean();
         
        //Split all links
        preg_match_all('/(<a[^>]*>.*?<\/a[^>]*>)/', $buffer, $matches);
        
        if (empty($matches[0])) {
            return;
        }
        
        $listOptions = optionValues(['class' => 'dropdown-menu'], $listOptions);
        
        return $this->_start .
            PHP_EOL .
            $this->Html->ul($matches[0], $listOptions, $itemOptions);
    }
}
