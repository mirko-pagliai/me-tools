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
     * Tests for `start()` and `end()` methods
     * @return void
     * @test
     */
    public function testStartAndEnd()
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
        
        $result = $this->Dropdown->start($text);
        $this->assertNull($result);
        
        echo $this->Html->link('First link', '/first');
        echo $this->Html->link('Second link', '/second');
        
        $result = $this->Dropdown->end();
        $this->assertHtml($expected, $result);
        
        //With callback
        $result = call_user_func(function () use ($text) {
            $result = $this->Dropdown->start($text);
            $this->assertNull($result);

            echo $this->Html->link('First link', '/first');
            echo $this->Html->link('Second link', '/second');

            return $this->Dropdown->end();
        });
        $this->assertHtml($expected, $result);
        
        //Start link with custom class
        $result = $this->Dropdown->start(
            $text,
            ['class' => 'my-start-class', 'icon' => 'home']
        );
        $this->assertNull($result);
        
        echo $this->Html->link('First link', '/first');
        echo $this->Html->link('Second link', '/second');
        
        //Ul and list elements with custom classes
        $result = $this->Dropdown->end(
            ['class' => 'ul-class'],
            ['class' => 'li-class']
        );
        
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
        $this->assertHtml($expected, $result);
    }
}
