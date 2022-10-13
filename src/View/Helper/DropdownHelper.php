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
 */
namespace MeTools\View\Helper;

use Cake\View\Helper;
use Tools\Exceptionist;

/**
 * Provides functionalities for creating dropdown menus, according to Bootstrap.
 *
 * Dropdowns are built on a third party library, Popper, which provides dynamic
 *  positioning and viewport detection. Be sure to include popper.min.js before
 *  Bootstrap’s JavaScript or use `bootstrap.bundle.min.js`/`bootstrap.bundle.js`
 *  which contains Popper.
 *
 * You need to call in order: the `start()` method; the `link()`method, for each
 *  link in the menu; the `end()` method, which also returns the HTML code.
 *
 * Example:
 * <code>
 * $this->Dropdown->start('My dropdown');
 * $this->Dropdown->link('First link', '/first');
 * $this->Dropdown->link('Second link', '/second');
 * echo $this->Dropdown->end();
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
     * @var string
     */
    protected string $_start;

    /**
     * @var string
     */
    protected string $_id;

    /**
     * @var array
     */
    protected array $_links = [];

    /**
     * Creates an HTML link for the dropdown.
     *
     * See the parent method for all available options.
     * @param array|string $title The content to be wrapped by `<a>` tags
     *   Can be an array if $url is null. If $url is null, $title will be used as both the URL and title.
     * @param array|string|null $url Cake-relative URL or array of URL parameters, or
     *   external URL (starts with http://)
     * @param array<string, mixed> $options Array of options and HTML attributes
     * @return void
     */
    public function link($title, $url = null, array $options = []): void
    {
        $options = optionsParser($options)->append('class', 'dropdown-item');
        $this->_links[] = $this->Html->link($title, $url, $options->toArray());
    }

    /**
     * Starts a dropdown.
     *
     * It will subsequently capture links created with the `link()` method until
     *  the `end()` method is called.
     *
     * `$title` and `$titleOptions` arguments are about the link that allows the
     *  opening of the dropdown menu.
     * @param string $title Title for the opening link
     * @param array $titleOptions HTML attributes and options for the opening
     *  link
     * @return void
     */
    public function start(string $title, array $titleOptions = []): void
    {
        $titleOptions = optionsParser($titleOptions)->append([
            'class' => 'dropdown-toggle',
            'data-bs-toggle' => 'dropdown',
            'aria-expanded' => 'false',
        ]);

        if (!$titleOptions->get('id')) {
            $titleOptions->add('id', uniqid('dropdown_'));
        }
        $this->_id = $titleOptions->get('id');

        $this->_start = $this->Html->link($title, '#', $titleOptions->toArray());
    }

    /**
     * Closes the dropdown and returns its entire code.
     *
     * `$ulOptions` argument is about the list of the dropdown menu.
     * @param array $ulOptions HTML attributes and options for the wrapper `<ul>`
     *  element
     * @return string
     * @throws \Throwable
     */
    public function end(array $ulOptions = []): string
    {
        Exceptionist::isTrue(isset($this->_start), sprintf('The `%s()` method was not called before `%s()`', 'start', 'end'));
        Exceptionist::isTrue($this->_links, sprintf('The dropdown has no content. Perhaps the `%s()` method was never called', 'link'));

        $ulOptions = optionsParser($ulOptions, ['aria-labelledby' => $this->_id])->append('class', 'dropdown-menu');

        $list = $this->Html->ul($this->_links, $ulOptions->toArray());
        $this->_id = '';
        $this->_links = [];

        return $this->_start . $list;
    }
}
