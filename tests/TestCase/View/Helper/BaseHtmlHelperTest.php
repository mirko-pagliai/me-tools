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
use MeTools\View\Helper\BaseHtmlHelper;

/**
 * BaseHtmlHelperTest class
 */
class BaseHtmlHelperTest extends TestCase
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
        $View = new View();
        $this->Html = new BaseHtmlHelper($View);
    }
    
    /**
     * Tests for `__call()` method
     * @return void
     * @test
     */
    public function testMagicCall()
    {
        $text = 'my h3 text';
        $class = 'my-class';
        
        //The `h3()` method should not exist, otherwise the `__call()` method
        //  will not be called
        $this->assertFalse(method_exists($this->Html, 'h3'));
        
        $result = $this->Html->h3($text, ['class' => $class]);
        $expected = $this->Html->tag('h3', $text, ['class' => $class]);
        $this->assertEquals($expected, $result);
        
        $result = $this->Html->h3($text, ['class' => $class, 'icon' => 'home']);
        $expected = $this->Html->tag(
            'h3',
            $text,
            ['class' => $class, 'icon' => 'home']
        );
        $this->assertEquals($expected, $result);
        
        $result = $this->Html->h3(
            $text,
            ['class' => $class, 'icon' => 'home', 'icon-align' => 'right']
        );
        $expected = $this->Html->tag(
            'h3',
            $text,
            ['class' => $class, 'icon' => 'home', 'icon-align' => 'right']
        );
        $this->assertEquals($expected, $result);
    }
    
    /**
     * Test for `addIcon()` method
     * @return void
     * @test
     */
    public function testAddIcon()
    {
        $text = 'My text';
        
        $result = $this->Html->addIcon($text, ['icon' => 'home']);
        $expected = [
            'i' => ['class' => 'fa fa-home'],
            ' ',
            '/i',
            ' ',
            $text,
        ];
        $this->assertHtml($expected, array_values($result)[0]);
        
        $result = $this->Html->addIcon($text, ['icon' => 'home', 'icon-align' => 'right']);
        $expected = [
            $text,
            ' ',
            'i' => ['class' => 'fa fa-home'],
            ' ',
            '/i',
        ];
        $this->assertHtml($expected, array_values($result)[0]);
        
        //This will be only `$text`
        $result = $this->Html->addIcon($text, []);
        $this->assertEquals($text, array_values($result)[0]);
        
        //This will be only icon
        $result = $this->Html->addIcon(null, ['icon' => 'home']);
        $expected = ['i' => ['class' => 'fa fa-home'], ' ', '/i'];
        $this->assertHtml($expected, array_values($result)[0]);
    }
    
    /**
     * Test for `addTooltip()` method
     * @return void
     * @test
     */
    public function testAddTooltip()
    {
        $tooltip = 'My tooltip';
        
        $expected = ['data-toggle' => 'tooltip', 'title' => $tooltip];
        
        $result = $this->Html->addTooltip(['tooltip' => $tooltip]);
        $this->assertEquals($expected, $result);
        
        // `tooltip` rewrites `title`
        $result = $this->Html->addTooltip([
            'title' => 'my title',
            'tooltip' => $tooltip,
        ]);
        $this->assertEquals($expected, $result);
        
        $result = $this->Html->addTooltip([
            'data-toggle' => 'some-data-here',
            'tooltip' => $tooltip,
        ]);
        $expected = [
            'data-toggle' => 'some-data-here tooltip',
            'title' => $tooltip,
        ];
        $this->assertEquals($expected, $result);
    }
    
    /**
     * Test for `button()` method
     * @return void
     * @test
     */
    public function testButton()
    {
        $text = 'My text';
        
        $result = $this->Html->button($text);
        $expected = [
            'button' => [
                'role' => 'button',
                'class' => 'btn btn-default',
                'title' => $text,
            ],
            $text,
            '/button',
        ];
        $this->assertHtml($expected, $result);
        
        $result = $this->Html->button($text, null, ['title' => 'my-custom-title']);
        $expected = [
            'button' => [
                'role' => 'button',
                'class' => 'btn btn-default',
                'title' => 'my-custom-title',
            ],
            $text,
            '/button',
        ];
        $this->assertHtml($expected, $result);
        
        $result = $this->Html->button($text, null, ['class' => 'my-class']);
        $expected = [
            'button' => [
                'role' => 'button',
                'class' => 'my-class btn btn-default',
                'title' => $text,
             ],
            $text,
            '/button',
        ];
        $this->assertHtml($expected, $result);
        
        $result = $this->Html->button($text, null, ['class' => 'btn-primary']);
        $expected = [
            'button' => [
                'role' => 'button',
                'class' => 'btn-primary btn',
                'title' => $text,
            ],
            $text,
            '/button',
        ];
        $this->assertHtml($expected, $result);
        
        $result = $this->Html->button($text, null, ['tooltip' => 'my tooltip']);
        $expected = [
            'button' => [
                'role' => 'button',
                'class' => 'btn btn-default',
                'data-toggle' => 'tooltip',
                'title' => 'my tooltip',
            ],
            $text,
            '/button'
        ];
        $this->assertHtml($expected, $result);
        
        // `tooltip` value rewrites `title` value
        $result = $this->Html->button(
            $text,
            null,
            ['title' => 'my custom title', 'tooltip' => 'my tooltip']
        );
        $expected = [
            'button' => [
                'role' => 'button',
                'class' => 'btn btn-default',
                'data-toggle' => 'tooltip',
                'title' => 'my tooltip',
            ],
            $text,
            '/button'
        ];
        $this->assertHtml($expected, $result);
        
        $result = $this->Html->button($text, null, ['icon' => 'home']);
        $expected = [
            'button' => [
                'role' => 'button',
                'class' => 'btn btn-default',
                'title' => $text,
             ],
            'i' => ['class' => 'fa fa-home'],
            ' ',
            '/i',
            ' ',
            $text,
            '/button',
        ];
        $this->assertHtml($expected, $result);
        
        //Single quote on text
        $result = $this->Html->button('single quote \'', null);
        $expected = [
            'button' => [
                'role' => 'button',
                'class' => 'btn btn-default',
                'title' => h('single quote \''),
            ],
            'single quote \'',
            '/button',
        ];
        $this->assertHtml($expected, $result);
        
        //Double quote on text
        $result = $this->Html->button('double quote "', null);
        $expected = [
            'button' => [
                'role' => 'button',
                'class' => 'btn btn-default',
                'title' => h('double quote "'),
             ],
            'double quote "',
            '/button',
        ];
        $this->assertHtml($expected, $result);
        
        //Single quote on custom title
        $result = $this->Html->button($text, null, ['title' => 'single quote \'']);
        $expected = [
            'button' => [
                'role' => 'button',
                'class' => 'btn btn-default',
                'title' => h('single quote \''),
            ],
            $text,
            '/button',
        ];
        $this->assertHtml($expected, $result);
        
        //Double quote on custom title
        $result = $this->Html->button($text, null, ['title' => 'double quote "']);
        $expected = [
            'button' => [
                'title' => h('double quote "'),
                'role' => 'button',
                'class' => 'btn btn-default',
            ],
            $text,
            '/button',
        ];
        $this->assertHtml($expected, $result);
        
        //Code on text
        $result = $this->Html->button('<u>Code</u> and text', null);
        $expected = [
            'button' => [
                'role' => 'button',
                'class' => 'btn btn-default',
                'title' => 'Code and text',
            ],
            'u' => true,
            'Code',
            '/u',
            ' and text',
            '/button',
        ];
        $this->assertHtml($expected, $result);
        
        //Code on custom title
        $result = $this->Html->button(
            $text,
            null,
            ['title' => '<u>Code</u> and text']
        );
        $expected = [
            'button' => [
                'role' => 'button',
                'class' => 'btn btn-default',
                'title' => 'Code and text'
            ],
            $text,
            '/button',
        ];
        $this->assertHtml($expected, $result);
    }
    
    /**
     * Test for `button()` method, with buttons as links
     * @return void
     * @test
     */
    public function testButtonAsLink()
    {
        $text = 'My text';
        
        $result = $this->Html->button($text, '#');
        $expected = [
            'a' => [
                'href' => '#',
                'role' => 'button',
                'class' => 'btn btn-default',
                'title' => $text,
            ],
            $text,
            '/a',
        ];
        $this->assertHtml($expected, $result);
        
        $result = $this->Html->button($text, '#', ['class' => 'my-class']);
        $expected = [
            'a' => [
                'href' => '#',
                'role' => 'button',
                'class' => 'my-class btn btn-default',
                'title' => $text,
            ],
            $text,
            '/a',
        ];
        $this->assertHtml($expected, $result);
        
        $result = $this->Html->button($text, '#', ['class' => 'btn-primary']);
        $expected = [
            'a' => [
                'href' => '#',
                'role' => 'button',
                'class' => 'btn-primary btn',
                'title' => $text,
            ],
            $text,
            '/a',
        ];
        $this->assertHtml($expected, $result);
        
        $result = $this->Html->button($text, '#', ['tooltip' => 'my tooltip']);
        $expected = [
            'a' => [
                'href' => '#',
                'role' => 'button',
                'class' => 'btn btn-default',
                'data-toggle' => 'tooltip',
                'title' => 'my tooltip',
            ],
            $text,
            '/a'
        ];
        $this->assertHtml($expected, $result);
        
        // `tooltip` value rewrites `title` value
        $result = $this->Html->button(
            $text,
            '#',
            ['title' => 'my custom title', 'tooltip' => 'my tooltip']
        );
        $expected = [
            'a' => [
                'href' => '#',
                'role' => 'button',
                'class' => 'btn btn-default',
                'data-toggle' => 'tooltip',
                'title' => 'my tooltip',
            ],
            $text,
            '/a'
        ];
        $this->assertHtml($expected, $result);
        
        $result = $this->Html->button($text, '#', ['icon' => 'home']);
        $expected = [
            'a' => [
                'href' => '#',
                'role' => 'button',
                'class' => 'btn btn-default',
                'title' => $text,
            ],
            'i' => ['class' => 'fa fa-home'],
            ' ',
            '/i',
            ' ',
            $text,
            '/a',
        ];
        $this->assertHtml($expected, $result);
    }
    
    /**
     * Test for `css()` method
     * @return void
     * @test
     */
    public function testCss()
    {
        //By default, `block` is `true`
        $result = $this->Html->css('my-file');
        $this->assertNull($result);
        
        $result = $this->Html->css('my-file2', ['block' => true]);
        $this->assertNull($result);
        
        $result = $this->Html->css('my-file3', ['block' => false]);
        $expected = [
            'link' => ['rel' => 'stylesheet', 'href' => '/css/my-file3.css']
        ];
        $this->assertHtml($expected, $result);
        
        $result = $this->Html->css('my-file4', ['block' => false, 'rel' => 'alternate']);
        $expected = [
            'link' => ['rel' => 'alternate', 'href' => '/css/my-file4.css']
        ];
        $this->assertHtml($expected, $result);
    }
    
    /**
     * Test for `cssBlock()` method
     * @return void
     * @test
     */
    public function testCssBlock()
    {
        $css = 'body { color: red; }';
        
        //By default, `block` is `true`
        $result = $this->Html->cssBlock($css);
        $this->assertNull($result);
        
        $result = $this->Html->cssBlock($css, ['block' => true]);
        $this->assertNull($result);
        
        $result = $this->Html->cssBlock($css, ['block' => false]);
        $expected = ['style' => true, $css, '/style'];
        $this->assertHtml($expected, $result);
    }
    
    /**
     * Test for `cssStart()` and `cssEnd()` methods
     * @return void
     * @test
     */
    public function testCssStartAndCssEnd()
    {
        $css = 'body { color: red; }';
        
        //By default, `block` is `true`
        $result = $this->Html->cssStart();
        $this->assertNull($result);
        
        echo $css;

        $result = $this->Html->cssEnd();
        $this->assertNull($result);
        
        $result = $this->Html->cssStart(['block' => true]);
        $this->assertNull($result);
        
        echo $css;

        $result = $this->Html->cssEnd();
        $this->assertNull($result);
        
        $result = $this->Html->cssStart(['block' => false]);
        $this->assertNull($result);
        
        echo $css;

        $result = $this->Html->cssEnd();
        $expected = ['<style', $css, '/style'];
        $this->assertHtml($expected, $result);
    }
    
    /**
     * Test for `div()` method
     * @return void
     * @test
     */
    public function testDiv()
    {
        $expected = ['div' => true, '/div'];
        
        $result = $this->Html->div();
        $this->assertHtml($expected, $result);
        
        $result = $this->Html->div(null);
        $this->assertHtml($expected, $result);
        
        $result = $this->Html->div(null, null);
        $this->assertHtml($expected, $result);
        
        $result = $this->Html->div(null, '');
        $this->assertHtml($expected, $result);
        
        $expected = ['div' => ['class' => 'my-class']];
        
        $result = $this->Html->div('my-class');
        $this->assertHtml($expected, $result);
        
        $result = $this->Html->div('my-class', null);
        $this->assertHtml($expected, $result);
        
        $result = $this->Html->div(null, ' ');
        $expected = ['div' => true, ' ', '/div'];
        $this->assertHtml($expected, $result);
        
        $result = $this->Html->div(null, 'my text', ['tooltip' => 'my tooltip']);
        $expected = [
            'div' => ['data-toggle' => 'tooltip', 'title' => 'my tooltip'],
            'my text',
            '/div',
        ];
        $this->assertHtml($expected, $result);
        
        $result = $this->Html->div('my-class', 'My text', ['id' => 'my-id', 'icon' => 'home']);
        $expected = [
            'div' => ['class' => 'my-class', 'id' => 'my-id'],
            'i' => ['class' => 'fa fa-home'],
            ' ',
            '/i',
            ' ',
            'My text',
            '/div'
        ];
        $this->assertHtml($expected, $result);
    }
    
    /**
     * Test for `hr()` method
     * @return void
     * @test
     */
    public function testHr()
    {
        $result = $this->Html->hr();
        $expected = $this->Html->tag('hr');
        $this->assertEquals($expected, $result);
        
        $result = $this->Html->hr(['class' => 'my-hr-class']);
        $expected = $this->Html->tag('hr', null, ['class' => 'my-hr-class']);
        $this->assertEquals($expected, $result);
    }
    
    /**
     * Test for `icon()` method
     * @return void
     * @test
     */
    public function testIcons()
    {
        $expected = [
            'i' => ['class' => 'fa fa-home'],
            ' ',
            '/i'
        ];
        
        $result = $this->Html->icon('home');
        $this->assertHtml($expected, $result);
        
        $result = $this->Html->icon('fa-home');
        $this->assertHtml($expected, $result);
        
        $result = $this->Html->icon('fa home');
        $this->assertHtml($expected, $result);
        
        $result = $this->Html->icon('fa fa-home');
        $this->assertHtml($expected, $result);
        
        $expected = [
            'i' => ['class' => 'fa fa-hand-o-right fa-2x'],
            ' ',
            '/i'
        ];
        
        $result = $this->Html->icon('hand-o-right 2x');
        $this->assertHtml($expected, $result);
        
        $result = $this->Html->icon('hand-o-right', '2x');
        $this->assertHtml($expected, $result);
        
        $result = $this->Html->icon(['hand-o-right', '2x']);
        $this->assertHtml($expected, $result);
    }
    
    /**
     * Test for `iframe()` method
     * @return void
     * @test
     */
    public function testIframe()
    {
        $url = 'http://frame';
        
        $result = $this->Html->iframe($url);
        $expected = ['iframe' => ['src' => $url]];
        $this->assertHtml($expected, $result);
        
        $result = $this->Html->iframe($url, ['class' => 'my-class']);
        $expected = ['iframe' => ['class' => 'my-class', 'src' => $url]];
        $this->assertHtml($expected, $result);
        
        //The `src` option doesn't overwrite
        $result = $this->Html->iframe($url, ['src' => 'http://anotherframe']);
        $expected = ['iframe' => ['src' => $url]];
        $this->assertHtml($expected, $result);
    }
    
    /**
     * Test for `image()` and `img()` methods
     * @return void
     * @test
     */
    public function testImage()
    {
        $image = 'image.gif';
        
        $result = $this->Html->image($image);
        $expected = ['img' => [
            'src' => '/img/image.gif',
            'alt' => $image,
            'class' => 'img-responsive',
        ]];
        $this->assertHtml($expected, $result);
        
        $result = $this->Html->image($image, ['class' => 'my-class']);
        $expected = ['img' => [
            'src' => '/img/image.gif',
            'alt' => $image,
            'class' => 'my-class img-responsive',
        ]];
        $this->assertHtml($expected, $result);
        
        //Tests `img()` alias
        $result = $this->Html->img($image, ['class' => 'my-class']);
        $expected = ['img' => [
            'src' => '/img/image.gif',
            'alt' => $image,
            'class' => 'my-class img-responsive',
        ]];
        $this->assertHtml($expected, $result);
        
        $result = $this->Html->image($image, ['alt' => 'my-alt']);
        $expected = ['img' => [
            'src' => '/img/image.gif',
            'alt' => 'my-alt',
            'class' => 'img-responsive',
        ]];
        $this->assertHtml($expected, $result);
        
        $result = $this->Html->image($image, ['tooltip' => 'my tooltip']);
        $expected = ['img' => [
            'src' => '/img/image.gif',
            'alt' => $image,
            'class' => 'img-responsive',
            'data-toggle' => 'tooltip',
            'title' => 'my tooltip',
        ]];
        $this->assertHtml($expected, $result);
        
        $result = $this->Html->image('http://fullurl/image.gif');
        $expected = ['img' => [
            'src' => 'http://fullurl/image.gif',
            'alt' => $image,
            'class' => 'img-responsive',
        ]];
        $this->assertHtml($expected, $result);
    }
    
    /**
     * Test for `li()` method
     * @return void
     * @test
     */
    public function testLi()
    {
        $result = $this->Html->li('My text');
        $expected = ['li' => true, 'My text', '/li'];
        $this->assertHtml($expected, $result);
        
        $result = $this->Html->li('My text', ['icon' => 'home']);
        $expected = [
            'li' => true,
            'i' => ['class' => 'fa fa-home'],
            ' ',
            '/i',
            ' ',
            'My text',
            '/li'
        ];
        $this->assertHtml($expected, $result);
        
        $list = ['first-value', 'second-value'];
        
        $result = $this->Html->li($list);
        $expected = [
            ['li' => true],
            'first-value',
            '/li',
            ['li' => true],
            'second-value',
            '/li',
        ];
        $this->assertHtml($expected, $result);
        
        $result = $this->Html->li($list, ['class' => 'my-class']);
        $expected = [
            ['li' => ['class' => 'my-class']],
            'first-value',
            '/li',
            ['li' => ['class' => 'my-class']],
            'second-value',
            '/li',
        ];
        $this->assertHtml($expected, $result);
        
        $result = $this->Html->li($list, ['icon' => 'home']);
        $expected = [
            ['li' => true],
            ['i' => ['class' => 'fa fa-home']],
            ' ',
            '/i',
            ' ',
            'first-value',
            '/li',
            ['li' => true],
            ['i' => ['class' => 'fa fa-home']],
            ' ',
            '/i',
            ' ',
            'second-value',
            '/li',
        ];
        $this->assertHtml($expected, $result);
    }
    
    /**
     * Test for `link()` method
     * @return void
     * @test
     */
    public function testLink()
    {
        $result = $this->Html->link(
            'My text',
            'http://link',
            ['title' => 'my-custom-title']
        );
        $expected = [
            'a' => ['href' => 'http://link', 'title' => 'my-custom-title'],
            'My text',
            '/a',
        ];
        $this->assertHtml($expected, $result);
        
        $result = $this->Html->link('My text', 'http://link', ['icon' => 'home']);
        $expected = [
            'a' => ['href' => 'http://link', 'title' => 'My text'],
            'i' => ['class' => 'fa fa-home'],
            ' ',
            '/i',
            ' ',
            'My text',
            '/a',
        ];
        $this->assertHtml($expected, $result);
        
        $result = $this->Html->link(
            'My text',
            '#',
            ['icon' => 'home', 'icon-align' => 'right']
        );
        $expected = [
            'a' => ['href' => '#', 'title' => 'My text'],
            'My text',
            ' ',
            'i' => ['class' => 'fa fa-home'],
            ' ',
            '/i',
            '/a',
        ];
        $this->assertHtml($expected, $result);
        
        $result = $this->Html->link('my text', '#', ['tooltip' => 'my tooltip']);
        $expected = [
            'a' => [
                'href' => '#',
                'data-toggle' => 'tooltip',
                'title' => 'my tooltip',
            ],
            'my text',
            '/a'
        ];
        $this->assertHtml($expected, $result);
        
        // `tooltip` value rewrites `title` value
        $result = $this->Html->link(
            'my text',
            '#',
            ['title' => 'my custom title', 'tooltip' => 'my tooltip']
        );
        $expected = [
            'a' => [
                'href' => '#',
                'data-toggle' => 'tooltip',
                'title' => 'my tooltip',
            ],
            'my text',
            '/a'
        ];
        $this->assertHtml($expected, $result);
        
        //Single quote on text
        $result = $this->Html->link('single quote \'', '#');
        $expected = [
            'a' => ['href' => '#', 'title' => h('single quote \'')],
            'single quote \'',
            '/a',
        ];
        $this->assertHtml($expected, $result);
        
        //Double quote on text
        $result = $this->Html->link('double quote "', '#');
        $expected = [
            'a' => ['href' => '#', 'title' => h('double quote "')],
            'double quote "',
            '/a',
        ];
        $this->assertHtml($expected, $result);
        
        //Single quote on custom title
        $result = $this->Html->link('My text', '#', ['title' => 'single quote \'']);
        $expected = [
            'a' => ['href' => '#', 'title' => h('single quote \'')],
            'My text',
            '/a',
        ];
        $this->assertHtml($expected, $result);
        
        //Double quote on custom title
        $result = $this->Html->link('My text', '#', ['title' => 'double quote "']);
        $expected = [
            'a' => ['href' => '#', 'title' => h('double quote "')],
            'My text',
            '/a',
        ];
        $this->assertHtml($expected, $result);
        
        //Code on text
        $result = $this->Html->link('<u>Code</u> and text', '#');
        $expected = [
            'a' => ['href' => '#', 'title' => 'Code and text'],
            'u' => true,
            'Code',
            '/u',
            ' and text',
            '/a',
        ];
        $this->assertHtml($expected, $result);
        
        //Code on custom title
        $result = $this->Html->link('My text', '#', ['title' => '<u>Code</u> and text']);
        $expected = [
            'a' => ['href' => '#', 'title' => 'Code and text'],
            'My text',
            '/a',
        ];
        $this->assertHtml($expected, $result);
    }
    
    /**
     * Test for `meta()` method
     * @return void
     * @test
     */
    public function testMeta()
    {
        //By default, `block` is `true`
        $result = $this->Html->meta('viewport', 'width=device-width');
        $this->assertNull($result);
        
        $result = $this->Html->meta('viewport', 'width=device-width', ['block' => true]);
        $this->assertNull($result);
        
        $result = $this->Html->meta('viewport', 'width=device-width', ['block' => false]);
        $expected = [
            'meta' => ['name' => 'viewport', 'content' => 'width=device-width']
        ];
        $this->assertHtml($expected, $result);
    }
    
    /**
     * Test for `nestedList`, `ol()` and `ul()` methods
     * @return void
     * @test
     */
    public function testNestedListAndOlAndUl()
    {
        $list = ['first', 'second'];
    
        $result = $this->Html->ul($list, [], ['icon' => 'home']);
        $expected = [
            'ul' => ['class' => 'fa-ul'],
            ['li' => true],
            ['i' => ['class' => 'fa fa-home fa-li']],
            ' ',
            '/i',
            ' ',
            'first',
            '/li',
            ['li' => true],
            ['i' => ['class' => 'fa fa-home fa-li']],
            ' ',
            '/i',
            ' ',
            'second',
            '/li',
            '/ul',
        ];
        $this->assertHtml($expected, $result);
        
        //It's the same
        $result = $this->Html->ul($list, ['icon' => 'home']);
        $expected = $this->Html->ul($list, [], ['icon' => 'home']);
        $this->assertEquals($expected, $result);
        
        $result = $this->Html->ul(
            $list,
            ['class' => 'list-class'],
            ['class' => 'item-class', 'icon' => 'home']
        );
        $expected = [
            'ul' => ['class' => 'list-class fa-ul'],
            ['li' => ['class' => 'item-class']],
            ['i' => ['class' => 'fa fa-home fa-li']],
            ' ',
            '/i',
            ' ',
            'first',
            '/li',
            ['li' => ['class' => 'item-class']],
            ['i' => ['class' => 'fa fa-home fa-li']],
            ' ',
            '/i',
            ' ',
            'second',
            '/li',
            '/ul',
        ];
        $this->assertHtml($expected, $result);
        
        //By default, `nestedList()` created `<ul>` list
        $result = $this->Html->ul(['first', 'second']);
        $expected = $this->Html->nestedList(['first', 'second']);
        $this->assertEquals($expected, $result);
        
        $result = $this->Html->ul(['first', 'second']);
        $expected = $this->Html->nestedList(['first', 'second'], ['tag' => 'ul']);
        $this->assertEquals($expected, $result);
        
        $result = $this->Html->ul(['first', 'second'], ['class' => 'my-class']);
        $expected = $this->Html->nestedList(
            ['first', 'second'],
            ['class' => 'my-class', 'tag' => 'ul']
        );
        $this->assertEquals($expected, $result);
        
        $result = $this->Html->ol(['first', 'second'], ['class' => 'my-class']);
        $expected = $this->Html->nestedList(
            ['first', 'second'],
            ['class' => 'my-class', 'tag' => 'ol']
        );
        $this->assertEquals($expected, $result);
    }
    
    /**
     * Test for `para()` method
     * @return void
     * @test
     */
    public function testPara()
    {
        $expected = ['p' => true, '/p'];
        
        $result = $this->Html->para();
        $this->assertHtml($expected, $result);
        
        $result = $this->Html->para(null);
        $this->assertHtml($expected, $result);
        
        $result = $this->Html->para(null, null);
        $this->assertHtml($expected, $result);
        
        $result = $this->Html->para(null, '');
        $this->assertHtml($expected, $result);
        
        $expected = ['p' => ['class' => 'my-class']];
        
        $result = $this->Html->para('my-class');
        $this->assertHtml($expected, $result);
        
        $result = $this->Html->para('my-class', null);
        $this->assertHtml($expected, $result);
        
        $result = $this->Html->para(null, ' ');
        $expected = ['p' => true, ' ', '/p'];
        $this->assertHtml($expected, $result);
        
        $result = $this->Html->para(null, 'my text', ['tooltip' => 'my tooltip']);
        $expected = [
            'p' => ['data-toggle' => 'tooltip', 'title' => 'my tooltip'],
            'my text',
            '/p'
        ];
        $this->assertHtml($expected, $result);
        
        $result = $this->Html->para('my-class', 'my text', ['id' => 'my-id', 'icon' => 'home']);
        $expected = [
            'p' => ['class' => 'my-class', 'id' => 'my-id'],
            'i' => ['class' => 'fa fa-home'],
            ' ',
            '/i',
            ' ',
            'my text',
            '/p'
        ];
        $this->assertHtml($expected, $result);
    }
    
    /**
     * Test for `script()` and `js()` methods
     * @return void
     * @test
     */
    public function testScript()
    {
        //By default, `block` is `true`
        $result = $this->Html->script('my-file');
        $this->assertNull($result);
        
        $result = $this->Html->script('my-file2', ['block' => true]);
        $this->assertNull($result);
        
        $result = $this->Html->script('my-file3', ['block' => false]);
        $expected = [
            'script' => ['src' => '/js/my-file3.js']
        ];
        $this->assertHtml($expected, $result);
        
        //By default, `block` is `true`
        $result = $this->Html->js('my-file4');
        $this->assertNull($result);
        
        $result = $this->Html->js('my-file5', ['block' => true]);
        $this->assertNull($result);
        
        $result = $this->Html->js('my-file6', ['block' => false]);
        $expected = [
            'script' => ['src' => '/js/my-file6.js']
        ];
        $this->assertHtml($expected, $result);
    }
    
    /**
     * Test for `scriptBlock()` method
     * @return void
     * @test
     */
    public function testScriptBlock()
    {
        $code = 'window.foo = 2;';
        
        //By default, `block` is `true`
        $result = $this->Html->scriptBlock($code, ['safe' => false]);
        $this->assertNull($result);
        
        $result = $this->Html->scriptBlock(
            $code,
            ['block' => true, 'safe' => false]
        );
        $this->assertNull($result);
        
        $result = $this->Html->scriptBlock(
            $code,
            ['block' => false, 'safe' => false]
        );
        $expected = [
            '<script',
            $code,
            '/script',
        ];
        $this->assertHtml($expected, $result);
    }
    
    /**
     * Test for `scriptStart()` and `scriptEnd()` methods
     * @return void
     * @test
     */
    public function testScriptStartAndScriptEnd()
    {
        //By default, `block` is `true`
        $result = $this->Html->scriptStart(['safe' => false]);
        $this->assertNull($result);
        
        echo 'this is some javascript';

        $result = $this->Html->scriptEnd();
        $this->assertNull($result);
        
        $result = $this->Html->scriptStart(['block' => true, 'safe' => false]);
        $this->assertNull($result);
        
        echo 'this is some javascript';

        $result = $this->Html->scriptEnd();
        $this->assertNull($result);
        
        $result = $this->Html->scriptStart(['block' => false, 'safe' => false]);
        $this->assertNull($result);
        
        echo 'this is some javascript';

        $result = $this->Html->scriptEnd();
        $expected = ['<script', 'this is some javascript', '/script'];
        $this->assertHtml($expected, $result);
    }

    /**
     * Test for `tag()` method
     * @return void
     * @test
     */
    public function testTag()
    {
        $text = 'My text';
        $class = 'my-class';
        
        $expected = ['h3' => true, '/h3'];
        
        $result = $this->Html->tag('h3');
        $this->assertHtml($expected, $result);
        
        $result = $this->Html->tag('h3', null);
        $this->assertHtml($expected, $result);
        
        $result = $this->Html->tag('h3', '');
        $this->assertHtml($expected, $result);
        
        $result = $this->Html->tag('h3', $text, ['class' => '$class']);
        $expected = [
            'h3' => ['class' => '$class'],
            $text,
            '/h3'
        ];
        $this->assertHtml($expected, $result);
        
        $result = $this->Html->tag('h3', $text, ['tooltip' => 'my tooltip']);
        $expected = [
            'h3' => ['data-toggle' => 'tooltip', 'title' => 'my tooltip'],
            $text,
            '/h3'
        ];
        $this->assertHtml($expected, $result);
        
        // `tooltip` value rewrites `title` value
        $result = $this->Html->tag(
            'h3',
            $text,
            ['title' => 'my custom title', 'tooltip' => 'my tooltip']
        );
        $expected = [
            'h3' => ['data-toggle' => 'tooltip', 'title' => 'my tooltip'],
            $text,
            '/h3'
        ];
        $this->assertHtml($expected, $result);
        
        $result = $this->Html->tag(
            'h3',
            $text,
            ['class' => '$class', 'icon' => 'home']
        );
        $expected = [
            'h3' => ['class' => '$class'],
            'i' => ['class' => 'fa fa-home'],
            ' ',
            '/i',
            ' ',
            $text,
            '/h3'
        ];
        $this->assertHtml($expected, $result);
        
        $result = $this->Html->tag(
            'h3',
            $text,
            ['class' => '$class', 'icon' => 'home', 'icon-align' => 'right']
        );
        $expected = [
            'h3' => ['class' => '$class'],
            $text,
            ' ',
            'i' => ['class' => 'fa fa-home'],
            ' ',
            '/i',
            '/h3'
        ];
        $this->assertHtml($expected, $result);
    }
}
