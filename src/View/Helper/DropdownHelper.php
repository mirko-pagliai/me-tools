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
     * Starts a dropdown. It captures links for the dropdown menu output until
     *  `DropdownHelper::end()` is called.
     *
     * Arguments and options regarding the link that allows the opening of the
     *  dropdown menu.
     * @param string $title The content to be wrapped by <a> tags
     * @param array $options Array of options and HTML attributes
     * @return void
     * @uses $_start
     */
    public function start($title, array $options = [])
    {
        $title = sprintf('%s %s', $title, $this->Html->icon('caret-down'));

        $options = optionValues([
            'aria-expanded' => 'false',
            'aria-haspopup' => 'true',
            'class' => 'dropdown-toggle',
            'data-toggle' => 'dropdown',
        ], $options);
        
        $this->_start = $this->Html->link($title, '#', $options);
        
        ob_start();
    }
    
    /**
     * End a buffered section of dropdown menu capturing.
     *
     * Arguments and options regarding the list of the dropdown menu.
     * @param array $options HTML attributes of the list tag
     * @param array $itemOptions HTML attributes of the list items
     * @return string|void
     */
    public function end(array $options = [], array $itemOptions = [])
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
        
        $options = optionValues(['class' => 'dropdown-menu'], $options);
        
        return $this->_start .
            PHP_EOL .
            $this->Html->ul($matches[0], $options, $itemOptions);
    }
}
