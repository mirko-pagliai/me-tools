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

use Cake\View\View;
use MeTools\TestSuite\TestCase;
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

        $this->Dropdown = new DropdownHelper(new View);
        $this->Html = new HtmlHelper(new View);
    }

    /**
     * Tests for `menu()`, `start()` and `end()` methods
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
            '/a',
            'div' => ['class' => 'dropdown-menu'],
            ['a' => ['href' => '/first', 'class' => 'dropdown-item', 'title' => 'First link']],
            'First link',
            '/a',
            ['a' => ['href' => '/second', 'class' => 'dropdown-item', 'title' => 'Second link']],
            'Second link',
            '/a',
            '/div',
        ];

        //No dropdown menu again...
        $this->assertNull($this->Dropdown->end());

        //Empty dropdown
        $this->Dropdown->start($text);
        echo 'hello!';
        $result = $this->Dropdown->end();
        $this->assertNull($result);

        $this->Dropdown->start($text);
        echo $this->Html->link('First link', '/first', ['class' => 'dropdown-item']);
        echo $this->Html->link('Second link', '/second', ['class' => 'dropdown-item']);
        $result = $this->Dropdown->end();
        $this->assertHtml($expected, $result);

        //With `menu()` method
        $result = $this->Dropdown->menu($text, [
            $this->Html->link('First link', '/first', ['class' => 'dropdown-item']),
            $this->Html->link('Second link', '/second', ['class' => 'dropdown-item']),
        ]);
        $this->assertHtml($expected, $result);

        //With callback
        $result = call_user_func(function () use ($text) {
            $this->Dropdown->start($text);
            echo $this->Html->link('First link', '/first', ['class' => 'dropdown-item']);
            echo $this->Html->link('Second link', '/second', ['class' => 'dropdown-item']);

            return $this->Dropdown->end();
        });
        $this->assertHtml($expected, $result);

        $expected = [
            ['a' => [
                'href' => '#',
                'class' => 'dropdown-toggle my-start-class',
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
            '/a',
            'div' => ['class' => 'div-custom-class dropdown-menu', 'attr' => 'value'],
            ['a' => ['href' => '/first', 'class' => 'dropdown-item', 'title' => 'First link']],
            'First link',
            '/a',
            ['a' => ['href' => '/second', 'class' => 'dropdown-item', 'title' => 'Second link']],
            'Second link',
            '/a',
            '/div',
        ];

        //Start link with custom class
        $this->Dropdown->start($text, ['class' => 'my-start-class', 'icon' => 'home']);
        echo $this->Html->link('First link', '/first', ['class' => 'dropdown-item']);
        echo $this->Html->link('Second link', '/second', ['class' => 'dropdown-item']);
        //Div wrapper with custom class and attribute
        $result = $this->Dropdown->end(['class' => 'div-custom-class', 'attr' => 'value']);
        $this->assertHtml($expected, $result);

        //With `menu()` method
        $result = $this->Dropdown->menu(
            $text,
            [
                $this->Html->link('First link', '/first', ['class' => 'dropdown-item']),
                $this->Html->link('Second link', '/second', ['class' => 'dropdown-item']),
            ],
            ['class' => 'my-start-class', 'icon' => 'home'],
            ['class' => 'div-custom-class', 'attr' => 'value']
        );
        $this->assertHtml($expected, $result);

        //Dropdown inside a list, with other links
        $expected = [
            ['ul' => true],
            ['li' => true],
            ['a' => ['href' => '/', 'title' => 'Home']],
            'Home',
            '/a',
            '/li',
            ['li' => true],
            ['a' => [
                'href' => '#',
                'aria-expanded' => 'false',
                'aria-haspopup' => 'true',
                'class' => 'dropdown-toggle',
                'data-toggle' => 'dropdown',
                'title' => $text,
            ]],
            $text,
            '/a',
            'div' => ['class' => 'dropdown-menu'],
            ['a' => ['href' => '/first', 'class' => 'dropdown-item', 'title' => 'First link']],
            'First link',
            '/a',
            ['a' => ['href' => '/second', 'class' => 'dropdown-item', 'title' => 'Second link']],
            'Second link',
            '/a',
            '/div',
            '/li',
            ['li' => true],
            ['a' => ['href' => '#', 'title' => 'Other main link']],
            'Other main link',
            '/a',
            '/li',
            '/ul',
        ];

        $result = $this->Html->ul([
            $this->Html->link('Home', '/'),
            //This is the dropdown menu
            call_user_func(function () {
                $this->Dropdown->start('My dropdown');
                echo $this->Html->link('First link', '/first', ['class' => 'dropdown-item']);
                echo $this->Html->link('Second link', '/second', ['class' => 'dropdown-item']);

                return $this->Dropdown->end();
            }),
            $this->Html->link('Other main link', '#'),
        ]);
        $this->assertHtml($expected, $result);
    }
}
