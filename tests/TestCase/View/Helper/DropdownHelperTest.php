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
 */
namespace MeTools\Test\TestCase\View\Helper;

use Cake\TestSuite\TestCase;
use Cake\View\View;
use MeTools\View\Helper\DropdownHelper;
use MeTools\View\Helper\HtmlHelper;

/**
 * DropdownHelperTest class
 */
class DropdownHelperTest extends TestCase
{
    /**
     * @var \MeTools\View\Helper\DropdownHelper
     */
    protected $Dropdown;

    /**
     * @var \MeTools\View\Helper\HtmlHelper
     */
    protected $Html;

    /**
     * Setup the test case, backup the static object values so they can be
     * restored. Specifically backs up the contents of Configure and paths in
     *  App if they have not already been backed up
     * @return void
     */
    public function setUp()
    {
        parent::setUp();

        $this->View = new View();
        $this->Dropdown = new DropdownHelper($this->View);
        $this->Html = new HtmlHelper($this->View);
    }

    /**
     * Teardown any static object changes and restore them
     * @return void
     */
    public function tearDown()
    {
        parent::tearDown();

        unset($this->Dropdown, $this->Html, $this->View);
    }

    /**
     * Tests for `menu()`, `start()` and `end()` methods
     * @return void
     * @test
     */
    public function testMenuAndStartAndEnd()
    {
        $text = 'My dropdown';

        $expected = [
            ['a' => [
                'href' => '#',
                'aria-expanded' => 'false',
                'aria-haspopup' => 'true',
                'class' => 'dropdown-toggle',
                'data-toggle' => 'dropdown',
                'title' => $text,
            ]],
            $text,
            'i' => ['class' => 'fa fa-caret-down'],
            ' ',
            '/i',
            '/a',
            'ul' => ['class' => 'dropdown-menu'],
            ['li' => true],
            ['a' => ['href' => '/first', 'title' => 'First link']],
            'First link',
            '/a',
            '/li',
            ['li' => true],
            ['a' => ['href' => '/second', 'title' => 'Second link']],
            'Second link',
            '/a',
            '/li',
            '/ul',
        ];

        //No dropdown menu again...
        $result = $this->Dropdown->end();
        $this->assertNull($result);

        //Empty dropdown
        $this->Dropdown->start($text);
        echo 'hello!';
        $result = $this->Dropdown->end();
        $this->assertNull($result);

        $this->Dropdown->start($text);
        echo $this->Html->link('First link', '/first');
        echo $this->Html->link('Second link', '/second');
        $result = $this->Dropdown->end();
        $this->assertHtml($expected, $result);

        //With `menu()` method
        $result = $this->Dropdown->menu($text, [
            $this->Html->link('First link', '/first'),
            $this->Html->link('Second link', '/second'),
        ]);
        $this->assertHtml($expected, $result);

        //With callback
        $result = call_user_func(function () use ($text) {
            $this->Dropdown->start($text);

            echo $this->Html->link('First link', '/first');
            echo $this->Html->link('Second link', '/second');

            return $this->Dropdown->end();
        });
        $this->assertHtml($expected, $result);

        $expected = [
            ['a' => [
                'href' => '#',
                'class' => 'my-start-class dropdown-toggle',
                'aria-expanded' => 'false',
                'aria-haspopup' => 'true',
                'data-toggle' => 'dropdown',
                'title' => $text,
            ]],
            ['i' => ['class' => 'fa fa-home']],
            ' ',
            '/i',
            ' ',
            $text,
            ' ',
            ['i' => ['class' => 'fa fa-caret-down']],
            ' ',
            '/i',
            '/a',
            'ul' => ['class' => 'ul-class dropdown-menu'],
            ['li' => ['class' => 'li-class']],
            ['a' => ['href' => '/first', 'title' => 'First link']],
            'First link',
            '/a',
            '/li',
            ['li' => ['class' => 'li-class']],
            ['a' => ['href' => '/second', 'title' => 'Second link']],
            'Second link',
            '/a',
            '/li',
            '/ul',
        ];

        //Start link with custom class
        $this->Dropdown->start(
            $text,
            ['class' => 'my-start-class', 'icon' => 'home']
        );
        echo $this->Html->link('First link', '/first');
        echo $this->Html->link('Second link', '/second');
        //Ul and list elements with custom classes
        $result = $this->Dropdown->end(
            ['class' => 'ul-class'],
            ['class' => 'li-class']
        );
        $this->assertHtml($expected, $result);

        //With `menu()` method
        $result = $this->Dropdown->menu(
            $text,
            [
                $this->Html->link('First link', '/first'),
                $this->Html->link('Second link', '/second')
            ],
            ['class' => 'my-start-class', 'icon' => 'home'],
            ['class' => 'ul-class'],
            ['class' => 'li-class']
        );
        $this->assertHtml($expected, $result);
    }
}
