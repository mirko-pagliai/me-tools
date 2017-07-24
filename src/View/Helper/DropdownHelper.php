<?php
/**
 * This file is part of me-tools.
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright   Copyright (c) Mirko Pagliai
 * @link        https://github.com/mirko-pagliai/me-tools
 * @license     https://opensource.org/licenses/mit-license.php MIT License
 * @see         http://getbootstrap.com/components/#dropdowns Bootstrap documentation
 */
namespace MeTools\View\Helper;

use Cake\View\Helper;
use MeTools\Utility\OptionsParserTrait;

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
    use OptionsParserTrait;

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

        $titleOptions = $this->optionsValues([
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

        $listOptions = $this->optionsValues(['class' => 'dropdown-menu'], $listOptions);

        return $this->_start . PHP_EOL . $this->Html->ul($matches[0], $listOptions, $itemOptions);
    }
}
