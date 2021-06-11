<?php
declare(strict_types=1);

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
 * @see         https://getbootstrap.com/docs/4.0/components/dropdowns
 */
namespace MeTools\View\Helper;

use Cake\View\Helper;

/**
 * Provides functionalities for creating dropdown menus, according to Bootstrap.
 *
 * Dropdowns are built on a third party library, Popper.js, which provides
 *  dynamic positioning and viewport detection. Be sure to include popper.min.js
 *  before Bootstrap’s JavaScript.
 *
 * Example:
 * <code>
 * $this->Dropdown->start('My dropdown');
 * echo $this->Html->link('First link', '/first', ['class' => 'dropdown-item']);
 * echo $this->Html->link('Second link', '/second', ['class' => 'dropdown-item']);
 * echo $this->Dropdown->end();
 * </code>
 *
 * Or using the `menu()` method:
 * <code>
 * echo $this->Dropdown->menu('My dropdown', [
 *      $this->Html->link('First link', '/first', ['class' => 'dropdown-item']),
 *      $this->Html->link('Second link', '/second', ['class' => 'dropdown-item']),
 * ]);
 * </code>
 *
 * You can also use it as a callback.
 * For example, this creates a dropdown menu as an element of a list:
 * <code>
 * $this->Html->ul([
 *      $this->Html->link('Home', '/'),
 *      //This is the dropdown menu
 *      call_user_func(function() {
 *          $this->Dropdown->start('My dropdown');
 *          echo $this->Html->link('First link', '/first', ['class' => 'dropdown-item']);
 *          echo $this->Html->link('Second link', '/second', ['class' => 'dropdown-item']);
 *
 *          return $this->Dropdown->end();
 *      }),
 *      $this->Html->link('Other main link', '#'),
 * ]);
 * </code>
 * @property \MeTools\View\Helper\HtmlHelper $Html
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
     * @param array $divOptions HTML attributes and options for the wrapper
     *  element
     * @return string|null
     */
    public function menu(string $title, array $menu, array $titleOptions = [], array $divOptions = []): ?string
    {
        $this->start($title, $titleOptions);

        array_walk($menu, function (string $item) {
            echo $item;
        });

        return $this->end($divOptions);
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
     */
    public function start(string $title, array $titleOptions = []): void
    {
        $titleOptions = optionsParser($titleOptions, ['aria-expanded' => 'false', 'aria-haspopup' => 'true'])
            ->append(['class' => 'dropdown-toggle', 'data-toggle' => 'dropdown']);

        $this->_start = $this->Html->link($title, '#', $titleOptions->toArray());

        ob_start();
    }

    /**
     * End a buffered section of dropdown menu capturing.
     *
     * Arguments and options regarding the list of the dropdown menu.
     * @param array $divOptions HTML attributes and options for the wrapper
     *  element
     * @return string|null
     */
    public function end(array $divOptions = []): ?string
    {
        $buffer = ob_get_contents();
        if (!$buffer) {
            return null;
        }

        ob_end_clean();

        //Split all links
        if (preg_match_all('/(<a[^>]*>.*?<\/a[^>]*>)/', $buffer, $matches) === 0) {
            return null;
        }

        $divOptions = optionsParser($divOptions)->append('class', 'dropdown-menu');
        $links = implode(PHP_EOL, $matches[0]);

        return $this->_start . PHP_EOL .
            $this->Html->div($divOptions->get('class'), $links, $divOptions->toArray());
    }
}
