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

use Cake\View\View;
use MeTools\TestSuite\TestCase;
use MeTools\View\Helper\DropdownHelper;

/**
 * DropdownHelperTest class
 */
class DropdownHelperTest extends TestCase
{
    /**
     * @var \MeTools\View\Helper\DropdownHelper
     */
    protected DropdownHelper $Helper;

    /**
     * @inheritDoc
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->Helper ??= new DropdownHelper(new View());
    }

    /**
     * @test
     * @uses \MeTools\View\Helper\DropdownHelper::end()
     * @uses \MeTools\View\Helper\DropdownHelper::link()
     * @uses \MeTools\View\Helper\DropdownHelper::start()
     */
    public function testStartLinkEndMethods(): void
    {
        $expected = [
            'a' => [
                'href' => '#',
                'class' => 'my-start-class dropdown-toggle text-decoration-none',
                'aria-expanded' => 'false',
                'data-bs-toggle' => 'dropdown',
                'id' => 'preg:/dropdown_[a-z0-9]+/',
                'title' => 'My title',
            ],
            'i' => ['class' => 'fa fa-home'],
            '/i',
            'My title',
            '/a',
            'ul' => [
                'class' => 'my-div-class dropdown-menu',
                'style' => 'background:red',
                'aria-labelledby' => 'preg:/dropdown_[a-z0-9]+/',
            ],
            ['li' => []],
            ['a' => [
                'href' => '/first',
                'class' => 'my-first-link-class dropdown-item',
                'title' => 'First link',
            ]],
            'First link',
            '/a',
            '/li',
            ['li' => []],
            ['a' => [
                'href' => '/second',
                'class' => 'my-second-link-class dropdown-item',
                'title' => 'Second link',
            ]],
            'Second link',
            '/a',
            '/li',
            '/ul',
        ];
        $this->Helper->start('My title', ['class' => 'my-start-class', 'icon' => 'fa fa-home']);
        $this->Helper->link('First link', '/first', ['class' => 'my-first-link-class']);
        $this->Helper->link('Second link', '/second', ['class' => 'my-second-link-class']);
        $result = $this->Helper->end(['class' => 'my-div-class', 'style' => 'background:red']);
        $this->assertHtml($expected, $result);

        $this->expectExceptionMessage('The dropdown has no content. Perhaps the `link()` method was never called');
        $this->Helper->start('My title');
        $this->Helper->end();
    }

    /**
     * @test
     * @uses \MeTools\View\Helper\DropdownHelper::end()
     */
    public function testEndWithNoStart(): void
    {
        $this->expectExceptionMessage('The `start()` method was not called before `end()`');
        $this->Helper->end();
    }
}
