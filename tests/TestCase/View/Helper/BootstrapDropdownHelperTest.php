<?php
/** @noinspection PhpUnhandledExceptionInspection */
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
namespace MeTools\Test\TestCase\View\Helper;

use MeTools\TestSuite\HelperTestCase;

/**
 * BootstrapDropdownHelperTest class
 * @property \MeTools\View\Helper\BootstrapDropdownHelper $Helper
 */
class BootstrapDropdownHelperTest extends HelperTestCase
{
    /**
     * Test for `start()`, `link()` and `end()` methods
     * @return void
     * @uses \MeTools\View\Helper\BootstrapDropdownHelper::end()
     * @uses \MeTools\View\Helper\BootstrapDropdownHelper::link()
     * @uses \MeTools\View\Helper\BootstrapDropdownHelper::start()
     */
    public function testStartLinkEndMethods(): void
    {
        $expected = [
            'a' => [
                'href' => '#',
                'aria-expanded' => 'false',
                'class' => 'dropdown-toggle my-start-class',
                'data-bs-toggle' => 'dropdown',
                'id' => 'preg:/dropdown_[a-z0-9]+/',
                'title' => 'My title',
            ],
            'i' => ['class' => 'fas fa-home'],
            '/i',
            'My title',
            '/a',
            'ul' => [
                'aria-labelledby' => 'preg:/dropdown_[a-z0-9]+/',
                'class' => 'dropdown-menu my-div-class',
                'style' => 'background:red',
            ],
            ['li' => []],
            ['a' => [
                'href' => '/first',
                'class' => 'dropdown-item my-first-link-class',
                'title' => 'First link',
            ]],
            'First link',
            '/a',
            '/li',
            ['li' => []],
            ['a' => [
                'href' => '/second',
                'class' => 'dropdown-item my-second-link-class',
                'title' => 'Second link',
            ]],
            'Second link',
            '/a',
            '/li',
            '/ul',
        ];
        $this->Helper->start('My title', ['class' => 'my-start-class', 'icon' => 'home']);
        $this->Helper->link('First link', '/first', ['class' => 'my-first-link-class']);
        $this->Helper->link('Second link', '/second', ['class' => 'my-second-link-class']);
        $result = $this->Helper->end(['class' => 'my-div-class', 'style' => 'background:red']);
        $this->assertHtml($expected, $result);

        $this->expectErrorMessage('The dropdown has no content. Perhaps the `link()` method was never called');
        $this->Helper->start('My title');
        $this->Helper->end();
    }

    /**
     * Test for `end()` method, with the `start()` method not called
     * @return void
     * @uses \MeTools\View\Helper\BootstrapDropdownHelper::end()
     */
    public function testEndWithNoStart(): void
    {
        $this->expectErrorMessage('The `start()` method was not called before `end()`');
        $this->Helper->end();
    }
}
