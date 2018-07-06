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
use MeTools\View\Helper\HtmlHelper;

/**
 * HtmlHelperTest class
 */
class HtmlHelperTest extends TestCase
{
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

        $this->Html = new HtmlHelper(new View);
    }

    /**
     * Tests for `__call()` method
     * @test
     */
    public function testCall()
    {
        $text = 'my h3 text';
        $class = 'my-class';

        //The `h3()` method should not exist, otherwise the `__call()` method
        //  will not be called
        $this->assertFalse(method_exists($this->Html, 'h3'));

        $expected = $this->Html->tag('h3', $text, compact('class'));
        $this->assertEquals($expected, $this->Html->h3($text, compact('class')));

        $expected = $this->Html->tag(
            'h3',
            $text,
            ['class' => $class, 'icon' => 'home']
        );
        $this->assertEquals($expected, $this->Html->h3($text, ['class' => $class, 'icon' => 'home']));

        $expected = $this->Html->tag(
            'h3',
            $text,
            ['class' => $class, 'icon' => 'home', 'icon-align' => 'right']
        );
        $result = $this->Html->h3(
            $text,
            ['class' => $class, 'icon' => 'home', 'icon-align' => 'right']
        );
        $this->assertEquals($expected, $result);
    }

    /**
     * Tests for `__call()` method, with a no existing method
     * @expectedException Cake\Core\Exception\Exception
     * @expectedExceptionMessage Method HtmlHelper::noExistingMethod does not exist
     * @test
     */
    public function testCallNoExistingMethod()
    {
        $this->Html->noExistingMethod(null, null, null);
    }

    /**
     * Tests for `addIconToText()` method
     * @test
     */
    public function testAddIconToText()
    {
        $text = 'My text';

        $options = optionsParser(['icon' => 'home']);
        list($result, $options) = $this->Html->addIconToText($text, $options);
        $this->assertEquals('<i class="fas fa-home"> </i> ' . $text, $result);
        $this->assertInstanceOf('MeTools\View\OptionsParser', $options);
        $this->assertFalse($options->exists('icon'));
        $this->assertFalse($options->exists('icon-align'));

        //Missing `icon` option
        $options = optionsParser(['class' => 'my-class', 'icon-align' => 'right']);
        list($result, $options) = $this->Html->addIconToText($text, $options);
        $this->assertEquals($text, $result);
        $this->assertInstanceOf('MeTools\View\OptionsParser', $options);
        $this->assertFalse($options->exists('icon'));
        $this->assertFalse($options->exists('icon-align'));
        $this->assertEquals('my-class', $options->get('class'));

        //Empty text
        $options = optionsParser(['icon' => 'home']);
        list($result, $options) = $this->Html->addIconToText(null, $options);
        $this->assertEquals('<i class="fas fa-home"> </i>', $result);
        $this->assertInstanceOf('MeTools\View\OptionsParser', $options);
        $this->assertFalse($options->exists('icon'));
        $this->assertFalse($options->exists('icon-align'));

        //Using `icon-align` option
        $options = optionsParser(['icon' => 'home', 'icon-align' => 'right']);
        list($result, $options) = $this->Html->addIconToText($text, $options);
        $this->assertEquals($text . ' <i class="fas fa-home"> </i>', $result);
        $this->assertInstanceOf('MeTools\View\OptionsParser', $options);
        $this->assertFalse($options->exists('icon'));
        $this->assertFalse($options->exists('icon-align'));

        //Invalid `icon-align` option
        $options = optionsParser(['icon' => 'home', 'icon-align' => 'left']);
        list($result, $options) = $this->Html->addIconToText($text, $options);
        $this->assertEquals('<i class="fas fa-home"> </i> ' . $text, $result);
        $this->assertInstanceOf('MeTools\View\OptionsParser', $options);
        $this->assertFalse($options->exists('icon'));
        $this->assertFalse($options->exists('icon-align'));
    }

    /**
     * Tests for `badge()` method
     * @test
     */
    public function testBadge()
    {
        $text = 'My text';

        $expected = [
            'span' => ['class' => 'badge'],
            $text,
            '/span',
        ];
        $this->assertHtml($expected, $this->Html->badge($text));

        $expected = [
            'span' => ['class' => 'badge my-class'],
            $text,
            '/span',
        ];
        $this->assertHtml($expected, $this->Html->badge($text, ['class' => 'my-class']));
    }

    /**
     * Test for `button()` method
     * @test
     */
    public function testButton()
    {
        $text = 'My text';

        $expected = [
            'button' => [
                'class' => 'btn btn-light',
                'role' => 'button',
                'title' => $text,
            ],
            $text,
            '/button',
        ];
        $this->assertHtml($expected, $this->Html->button($text));

        $expected = [
            'button' => [
                'class' => 'btn btn-light',
                'role' => 'button',
                'title' => 'my-custom-title',
            ],
            $text,
            '/button',
        ];
        $this->assertHtml($expected, $this->Html->button($text, null, ['title' => 'my-custom-title']));

        $expected = [
            'button' => [
                'class' => 'btn btn-light my-class',
                'role' => 'button',
                'title' => $text,
             ],
            $text,
            '/button',
        ];
        $this->assertHtml($expected, $this->Html->button($text, null, ['class' => 'my-class']));

        $expected = [
            'button' => [
                'class' => 'btn btn-primary',
                'role' => 'button',
                'title' => $text,
            ],
            $text,
            '/button',
        ];
        $this->assertHtml($expected, $this->Html->button($text, null, ['class' => 'btn-primary']));

        $expected = [
            'button' => [
                'role' => 'button',
                'class' => 'btn btn-light',
                'data-toggle' => 'tooltip',
                'title' => 'my tooltip',
            ],
            $text,
            '/button'
        ];
        $this->assertHtml($expected, $this->Html->button($text, null, ['tooltip' => 'my tooltip']));

        // `tooltip` value rewrites `title` value
        $expected = [
            'button' => [
                'role' => 'button',
                'class' => 'btn btn-light',
                'data-toggle' => 'tooltip',
                'title' => 'my tooltip',
            ],
            $text,
            '/button'
        ];
        $result = $this->Html->button(
            $text,
            null,
            ['title' => 'my custom title', 'tooltip' => 'my tooltip']
        );
        $this->assertHtml($expected, $result);

        $expected = [
            'button' => [
                'role' => 'button',
                'class' => 'btn btn-light',
                'title' => $text,
             ],
            'i' => ['class' => 'fas fa-home'],
            ' ',
            '/i',
            ' ',
            $text,
            '/button',
        ];
        $this->assertHtml($expected, $this->Html->button($text, null, ['icon' => 'home']));

        //Single quote on text
        $expected = [
            'button' => [
                'role' => 'button',
                'class' => 'btn btn-light',
                'title' => 'Single quote &#039;',
            ],
            'Single quote \'',
            '/button',
        ];
        $this->assertHtml($expected, $this->Html->button('Single quote \'', null));

        //Double quote on text
        $expected = [
            'button' => [
                'role' => 'button',
                'class' => 'btn btn-light',
                'title' => 'Double quote &quot;',
             ],
            'Double quote "',
            '/button',
        ];
        $this->assertHtml($expected, $this->Html->button('Double quote "', null));

        //Single quote on custom title
        $expected = [
            'button' => [
                'role' => 'button',
                'class' => 'btn btn-light',
                'title' => 'Single quote &#039;',
            ],
            $text,
            '/button',
        ];
        $this->assertHtml($expected, $this->Html->button($text, null, ['title' => 'Single quote \'']));

        //Double quote on custom title
        $expected = [
            'button' => [
                'title' => 'Double quote &quot;',
                'role' => 'button',
                'class' => 'btn btn-light',
            ],
            $text,
            '/button',
        ];
        $this->assertHtml($expected, $this->Html->button($text, null, ['title' => 'Double quote "']));

        //Code on text
        $expected = [
            'button' => [
                'role' => 'button',
                'class' => 'btn btn-light',
                'title' => 'Code and text',
            ],
            'u' => true,
            'Code',
            '/u',
            ' and text',
            '/button',
        ];
        $this->assertHtml($expected, $this->Html->button('<u>Code</u> and text', null));

        //Code on custom title
        $expected = [
            'button' => [
                'class' => 'btn btn-light',
                'role' => 'button',
                'title' => 'Code and text'
            ],
            $text,
            '/button',
        ];
        $this->assertHtml($expected, $this->Html->button($text, null, ['title' => '<u>Code</u> and text']));
    }

    /**
     * Test for `button()` method, with buttons as links
     * @test
     */
    public function testButtonAsLink()
    {
        $text = 'My text';

        $expected = [
            'a' => [
                'class' => 'btn btn-light',
                'href' => '#',
                'role' => 'button',
                'title' => $text,
            ],
            $text,
            '/a',
        ];
        $this->assertHtml($expected, $this->Html->button($text, '#'));

        $expected = [
            'a' => [
                'class' => 'btn btn-light my-class',
                'href' => '#',
                'role' => 'button',
                'title' => $text,
            ],
            $text,
            '/a',
        ];
        $this->assertHtml($expected, $this->Html->button($text, '#', ['class' => 'my-class']));

        $expected = [
            'a' => [
                'class' => 'btn btn-primary',
                'href' => '#',
                'role' => 'button',
                'title' => $text,
            ],
            $text,
            '/a',
        ];
        $this->assertHtml($expected, $this->Html->button($text, '#', ['class' => 'btn-primary']));

        $expected = [
            'a' => [
                'class' => 'btn btn-light',
                'href' => '#',
                'role' => 'button',
                'data-toggle' => 'tooltip',
                'title' => 'my tooltip',
            ],
            $text,
            '/a'
        ];
        $this->assertHtml($expected, $this->Html->button($text, '#', ['tooltip' => 'my tooltip']));

        // `tooltip` value rewrites `title` value
        $expected = [
            'a' => [
                'class' => 'btn btn-light',
                'href' => '#',
                'role' => 'button',
                'data-toggle' => 'tooltip',
                'title' => 'my tooltip',
            ],
            $text,
            '/a'
        ];
        $result = $this->Html->button($text, '#', ['title' => 'my custom title', 'tooltip' => 'my tooltip']);
        $this->assertHtml($expected, $result);

        $expected = [
            'a' => [
                'class' => 'btn btn-light',
                'href' => '#',
                'role' => 'button',
                'title' => $text,
            ],
            'i' => ['class' => 'fas fa-home'],
            ' ',
            '/i',
            ' ',
            $text,
            '/a',
        ];
        $this->assertHtml($expected, $this->Html->button($text, '#', ['icon' => 'home']));
    }

    /**
     * Test for `css()` method
     * @test
     */
    public function testCss()
    {
        //By default, `block` is `true`
        $this->assertNull($this->Html->css('my-file'));

        $this->assertNull($this->Html->css('my-file2', ['block' => true]));

        $expected = ['link' => ['rel' => 'stylesheet', 'href' => '/css/my-file3.css']];
        $this->assertHtml($expected, $this->Html->css('my-file3', ['block' => false]));

        $expected = ['link' => ['rel' => 'alternate', 'href' => '/css/my-file4.css']];
        $result = $this->Html->css('my-file4', ['block' => false, 'rel' => 'alternate']);
        $this->assertHtml($expected, $result);
    }

    /**
     * Test for `cssBlock()` method
     * @test
     */
    public function testCssBlock()
    {
        $css = 'body { color: red; }';

        //By default, `block` is `true`ì
        $this->assertNull($this->Html->cssBlock($css));

        $this->assertNull($this->Html->cssBlock($css, ['block' => true]));

        $expected = ['style' => true, $css, '/style'];
        $this->assertHtml($expected, $this->Html->cssBlock($css, ['block' => false]));
    }

    /**
     * Test for `cssStart()` and `cssEnd()` methods
     * @test
     */
    public function testCssStartAndCssEnd()
    {
        $css = 'body { color: red; }';

        //By default, `block` is `true`
        $this->Html->cssStart();
        echo $css;
        $result = $this->Html->cssEnd();
        $this->assertNull($result);

        $this->Html->cssStart(['block' => true]);
        echo $css;
        $result = $this->Html->cssEnd();
        $this->assertNull($result);

        $expected = ['<style', $css, '/style'];
        $this->Html->cssStart(['block' => false]);
        echo $css;
        $result = $this->Html->cssEnd();
        $this->assertHtml($expected, $result);
    }

    /**
     * Test for `div()` method
     * @test
     */
    public function testDiv()
    {
        $expected = ['div' => true, '/div'];
        $this->assertHtml($expected, $this->Html->div());
        $this->assertHtml($expected, $this->Html->div(null));
        $this->assertHtml($expected, $this->Html->div(null, null));
        $this->assertHtml($expected, $this->Html->div(null, ''));

        $expected = ['div' => ['class' => 'my-class']];
        $this->assertHtml($expected, $this->Html->div('my-class'));
        $this->assertHtml($expected, $this->Html->div('my-class', null));

        $expected = ['div' => true, ' ', '/div'];
        $this->assertHtml($expected, $this->Html->div(null, ' '));

        $expected = [
            'div' => ['data-toggle' => 'tooltip', 'title' => 'my tooltip'],
            'my text',
            '/div',
        ];
        $this->assertHtml($expected, $this->Html->div(null, 'my text', ['tooltip' => 'my tooltip']));

        $expected = [
            'div' => ['class' => 'my-class', 'id' => 'my-id'],
            'i' => ['class' => 'fas fa-home'],
            ' ',
            '/i',
            ' ',
            'My text',
            '/div'
        ];
        $result = $this->Html->div('my-class', 'My text', ['id' => 'my-id', 'icon' => 'home']);
        $this->assertHtml($expected, $result);
    }

    /**
     * Tests for `heading()` method

     * @test
     */
    public function testHeading()
    {
        $text = 'My header';
        $smallText = 'My small text';

        $expected = ['h2' => true, $text, '/h2'];
        $this->assertHtml($expected, $this->Html->heading($text));

        //It still creates a h2 tag
        $this->assertHtml($expected, $this->Html->heading($text, ['type' => 'strong']));

        $expected = ['h4' => true, $text, '/h4'];
        $this->assertHtml($expected, $this->Html->heading($text, ['type' => 'h4']));

        $expected = [
            'h2' => true,
            $text,
            ' ',
            'small' => true,
            $smallText,
            '/small',
            '/h2',
        ];
        $this->assertHtml($expected, $this->Html->heading($text, [], $smallText));

        $expected = [
            'h4' => true,
            $text,
            ' ',
            'small' => true,
            $smallText,
            '/small',
            '/h4',
        ];
        $this->assertHtml($expected, $this->Html->heading($text, ['type' => 'h4'], $smallText));

        $expected = [
            'h2' => ['class' => 'header-class'],
            $text,
            ' ',
            'small' => ['class' => 'small-class'],
            $smallText,
            '/small',
            '/h2',
        ];
        $result = $this->Html->heading(
            $text,
            ['class' => 'header-class'],
            $smallText,
            ['class' => 'small-class']
        );
        $this->assertHtml($expected, $result);
    }

    /**
     * Test for `hr()` method
     * @test
     */
    public function testHr()
    {
        $expected = $this->Html->tag('hr');
        $this->assertEquals($expected, $this->Html->hr());

        $expected = $this->Html->tag('hr', null, ['class' => 'my-hr-class']);
        $this->assertEquals($expected, $this->Html->hr(['class' => 'my-hr-class']));
    }

    /**
     * Test for `icon()` method
     * @test
     */
    public function testIcons()
    {
        $expected = [
            'i' => ['class' => 'preg:/(fa|fab|fal|far|fas) fa\-home/'],
            ' ',
            '/i',
        ];

        foreach ([
            'home',
            'fa-home',
            'fa home',
            'fas home',
            'fab home',
            'fal home',
            'far home',
            'fas home',
            'fa fa-home',
            ['home'],
            ['fa', 'home'],
            ['fas', 'home']
        ] as $icons) {
            $this->assertHtml($expected, $this->Html->icon($icons));
        }
        $this->assertHtml($expected, $this->Html->icon('fa', 'fa-home'));

        $expected = [
            'i' => ['class' => 'fas fa-hand-o-right fa-2x'],
            ' ',
            '/i',
        ];

        foreach ([
            'hand-o-right 2x',
            ['hand-o-right', '2x'],
        ] as $icons) {
            $this->assertHtml($expected, $this->Html->icon($icons));
        }
        $this->assertHtml($expected, $this->Html->icon('hand-o-right', '2x'));
    }

    /**
     * Test for `iframe()` method
     * @test
     */
    public function testIframe()
    {
        $url = 'http://frame';

        $expected = ['iframe' => ['src' => $url]];
        $this->assertHtml($expected, $this->Html->iframe($url));

        //No existing ratio
        $this->assertHtml($expected, $this->Html->iframe($url, ['ratio' => 'noExisting']));

        $expected = ['iframe' => ['class' => 'my-class', 'src' => $url]];
        $this->assertHtml($expected, $this->Html->iframe($url, ['class' => 'my-class']));

        //The `src` option doesn't overwrite
        $expected = ['iframe' => ['src' => $url]];
        $this->assertHtml($expected, $this->Html->iframe($url, ['src' => 'http://anotherframe']));

        foreach (['16by9', '4by3'] as $ratio) {
            $expected = [
                'div' => ['class' => 'embed-responsive embed-responsive-' . $ratio],
                'iframe' => ['class' => 'embed-responsive-item', 'src' => $url],
                '/iframe',
                '/div',
            ];
            $this->assertHtml($expected, $this->Html->iframe($url, compact('ratio')));
        }

        $expected = [
            'div' => ['class' => 'embed-responsive embed-responsive-16by9'],
            'iframe' => [
                'class' => 'embed-responsive-item my-class',
                'src' => $url,
            ],
            '/iframe',
            '/div',
        ];
        $this->assertHtml($expected, $this->Html->iframe($url, ['class' => 'my-class', 'ratio' => '16by9']));
    }

    /**
     * Test for `image()` and `img()` methods
     * @test
     */
    public function testImage()
    {
        $image = 'image.gif';

        $expected = [
            'img' => [
                'src' => '/img/image.gif',
                'alt' => $image,
                'class' => 'img-fluid',
            ],
        ];
        $this->assertHtml($expected, $this->Html->image($image));

        $expected = [
            'img' => [
                'src' => '/img/image.gif',
                'alt' => $image,
                'class' => 'img-fluid my-class',
            ],
        ];
        $this->assertHtml($expected, $this->Html->image($image, ['class' => 'my-class']));

        //Tests `img()` alias
        $expected = [
            'img' => [
                'src' => '/img/image.gif',
                'alt' => $image,
                'class' => 'img-fluid my-class',
            ],
        ];
        $this->assertHtml($expected, $this->Html->img($image, ['class' => 'my-class']));

        $expected = [
            'img' => [
                'src' => '/img/image.gif',
                'alt' => 'my-alt',
                'class' => 'img-fluid',
            ],
        ];
        $this->assertHtml($expected, $this->Html->image($image, ['alt' => 'my-alt']));

        $expected = [
            'img' => [
                'src' => '/img/image.gif',
                'alt' => $image,
                'class' => 'img-fluid',
                'data-toggle' => 'tooltip',
                'title' => 'my tooltip',
            ],
        ];
        $this->assertHtml($expected, $this->Html->image($image, ['tooltip' => 'my tooltip']));

        $expected = [
            'img' => [
                'src' => 'http://fullurl/image.gif',
                'alt' => $image,
                'class' => 'img-fluid',
            ],
        ];
        $this->assertHtml($expected, $this->Html->image('http://fullurl/image.gif'));
    }

    /**
     * Tests for `label()` method
     * @test
     */
    public function testLabel()
    {
        $text = 'My text';

        $expected = [
            'span' => ['class' => 'label label-default'],
            $text,
            '/span',
        ];
//        dd($this->Html->label($text));
        $this->assertHtml($expected, $this->Html->label($text));

        $expected = [
            'span' => ['class' => 'label label-default my-class'],
            $text,
            '/span',
        ];
        $this->assertHtml($expected, $this->Html->label($text, ['class' => 'my-class']));

        $expected = [
            'span' => ['class' => 'label label-success'],
            $text,
            '/span',
        ];
        $this->assertHtml($expected, $this->Html->label($text, ['type' => 'success']));

        $expected = [
            'span' => ['class' => 'label label-success my-class'],
            $text,
            '/span',
        ];
        $result = $this->Html->label($text, ['class' => 'my-class', 'type' => 'success']);
        $this->assertHtml($expected, $result);
    }

    /**
     * Test for `li()` method
     * @test
     */
    public function testLi()
    {
        $result = $this->Html->li('My text');
        $expected = ['li' => true, 'My text', '/li'];
        $this->assertHtml($expected, $result);

        $expected = [
            'li' => true,
            'i' => ['class' => 'fas fa-home'],
            ' ',
            '/i',
            ' ',
            'My text',
            '/li'
        ];
        $this->assertHtml($expected, $this->Html->li('My text', ['icon' => 'home']));

        $list = ['first-value', 'second-value'];

        $expected = [
            ['li' => true],
            'first-value',
            '/li',
            ['li' => true],
            'second-value',
            '/li',
        ];
        $this->assertHtml($expected, $this->Html->li($list));

        $expected = [
            ['li' => ['class' => 'my-class']],
            'first-value',
            '/li',
            ['li' => ['class' => 'my-class']],
            'second-value',
            '/li',
        ];
        $this->assertHtml($expected, $this->Html->li($list, ['class' => 'my-class']));

        $expected = [
            ['li' => true],
            ['i' => ['class' => 'fas fa-home']],
            ' ',
            '/i',
            ' ',
            'first-value',
            '/li',
            ['li' => true],
            ['i' => ['class' => 'fas fa-home']],
            ' ',
            '/i',
            ' ',
            'second-value',
            '/li',
        ];
        $this->assertHtml($expected, $this->Html->li($list, ['icon' => 'home']));
    }

    /**
     * Test for `link()` method
     * @test
     */
    public function testLink()
    {
        $title = 'My title';

        $expected = [
            'a' => ['href' => 'http://link', 'title' => 'my-custom-title'],
            $title,
            '/a',
        ];
        $result = $this->Html->link($title, 'http://link', ['title' => 'my-custom-title']);
        $this->assertHtml($expected, $result);

        $expected = [
            'a' => ['href' => 'http://link', 'title' => $title],
            'i' => ['class' => 'fas fa-home'],
            ' ',
            '/i',
            ' ',
            $title,
            '/a',
        ];
        $this->assertHtml($expected, $this->Html->link($title, 'http://link', ['icon' => 'home']));

        $expected = [
            'a' => ['href' => '#', 'title' => $title],
            $title,
            ' ',
            'i' => ['class' => 'fas fa-home'],
            ' ',
            '/i',
            '/a',
        ];
        $result = $this->Html->link($title, '#', ['icon' => 'home', 'icon-align' => 'right']);
        $this->assertHtml($expected, $result);

        //Single quote on text
        $expected = [
            'a' => ['href' => '#', 'title' => 'Single quote &#039;'],
            'Single quote \'',
            '/a',
        ];
        $this->assertHtml($expected, $this->Html->link('Single quote \'', '#'));

        //Double quote on text
        $expected = [
            'a' => ['href' => '#', 'title' => 'Double quote &quot;'],
            'Double quote "',
            '/a',
        ];
        $this->assertHtml($expected, $this->Html->link('Double quote "', '#'));

        //Single quote on custom title
        $expected = [
            'a' => ['href' => '#', 'title' => 'Single quote &#039;'],
            $title,
            '/a',
        ];
        $result = $this->Html->link($title, '#', ['title' => 'Single quote \'']);
        $this->assertHtml($expected, $result);

        //Double quote on custom title
        $expected = [
            'a' => ['href' => '#', 'title' => 'Double quote &quot;'],
            $title,
            '/a',
        ];
        $result = $this->Html->link($title, '#', ['title' => 'Double quote "']);
        $this->assertHtml($expected, $result);

        //Code on text
        $expected = [
            'a' => ['href' => '#', 'title' => 'Code and text'],
            'u' => true,
            'Code',
            '/u',
            ' and text',
            '/a',
        ];
        $this->assertHtml($expected, $this->Html->link('<u>Code</u> and text', '#'));

        //Code on custom title
        $expected = [
            'a' => ['href' => '#', 'title' => 'Code and text'],
            $title,
            '/a',
        ];
        $result = $this->Html->link($title, '#', ['title' => '<u>Code</u> and text']);
        $this->assertHtml($expected, $result);

        $expected = [
            'a' => [
                'href' => '#',
                'data-toggle' => 'tooltip',
                'title' => 'my tooltip',
            ],
            $title,
            '/a'
        ];
        $this->assertHtml($expected, $this->Html->link($title, '#', ['tooltip' => 'my tooltip']));

        // `tooltip` value rewrites `title` value
        $expected = [
            'a' => [
                'href' => '#',
                'data-toggle' => 'tooltip',
                'title' => 'my tooltip',
            ],
            $title,
            '/a'
        ];
        $result = $this->Html->link($title, '#', ['title' => 'my custom title', 'tooltip' => 'my tooltip']);
        $this->assertHtml($expected, $result);

        //Tooltip with alignment
        $expected = [
            'a' => [
                'href' => '#',
                'data-placement' => 'bottom',
                'data-toggle' => 'tooltip',
                'title' => 'my tooltip',
            ],
            $title,
            '/a'
        ];
        $result = $this->Html->link($title, '#', ['tooltip' => 'my tooltip', 'tooltip-align' => 'bottom']);
        $this->assertHtml($expected, $result);

        $expected = [
            'a' => [
                'href' => '#',
                'title' => 'Single quote &#039;',
                'data-toggle' => 'tooltip',
            ],
            $title,
            '/a'
        ];
        $this->assertHtml($expected, $this->Html->link($title, '#', ['tooltip' => 'Single quote \'']));

        $expected = [
            'a' => [
                'href' => '#',
                'title' => 'Double quote &quot;',
                'data-toggle' => 'tooltip',
            ],
            $title,
            '/a'
        ];
        $this->assertHtml($expected, $this->Html->link($title, '#', ['tooltip' => 'Double quote "']));

        $expected = [
            'a' => [
                'href' => '#',
                'title' => 'Code and text',
                'data-toggle' => 'tooltip',
            ],
            $title,
            '/a'
        ];
        $this->assertHtml($expected, $this->Html->link($title, '#', ['tooltip' => '<u>Code</u> and text']));
    }

    /**
     * Test for `meta()` method
     * @test
     */
    public function testMeta()
    {
        //By default, `block` is `true`
        $this->assertNull($this->Html->meta('viewport', 'width=device-width'));
        $this->assertNull($this->Html->meta('viewport', 'width=device-width', ['block' => true]));

        $expected = ['meta' => ['name' => 'viewport', 'content' => 'width=device-width']];
        $result = $this->Html->meta('viewport', 'width=device-width', ['block' => false]);
        $this->assertHtml($expected, $result);
    }

    /**
     * Test for `nestedList`, `ol()` and `ul()` methods
     * @test
     */
    public function testNestedListAndOlAndUl()
    {
        $list = ['first', 'second'];

        $expected = [
            'ul' => ['class' => 'fa-ul'],
            ['li' => true],
            ['i' => ['class' => 'fas fa-home fa-li']],
            ' ',
            '/i',
            ' ',
            'first',
            '/li',
            ['li' => true],
            ['i' => ['class' => 'fas fa-home fa-li']],
            ' ',
            '/i',
            ' ',
            'second',
            '/li',
            '/ul',
        ];
        $this->assertHtml($expected, $this->Html->ul($list, [], ['icon' => 'home']));

        //It's the same
        $expected = $this->Html->ul($list, [], ['icon' => 'home']);
        $this->assertEquals($expected, $this->Html->ul($list, ['icon' => 'home']));

        $expected = [
            'ul' => ['class' => 'fa-ul list-class'],
            ['li' => ['class' => 'item-class']],
            ['i' => ['class' => 'fas fa-home fa-li']],
            ' ',
            '/i',
            ' ',
            'first',
            '/li',
            ['li' => ['class' => 'item-class']],
            ['i' => ['class' => 'fas fa-home fa-li']],
            ' ',
            '/i',
            ' ',
            'second',
            '/li',
            '/ul',
        ];
        $result = $this->Html->ul(
            $list,
            ['class' => 'list-class'],
            ['class' => 'item-class', 'icon' => 'home']
        );
        $this->assertHtml($expected, $result);

        //By default, `nestedList()` created `<ul>` list
        $expected = $this->Html->nestedList($list);
        $this->assertEquals($expected, $this->Html->ul($list));

        $expected = $this->Html->nestedList($list, ['tag' => 'ul']);
        $this->assertEquals($expected, $this->Html->ul($list));

        $result = $this->Html->ul($list, ['class' => 'my-class']);
        $expected = $this->Html->nestedList($list, ['class' => 'my-class', 'tag' => 'ul']);
        $this->assertEquals($expected, $result);

        $result = $this->Html->ol($list, ['class' => 'my-class']);
        $expected = $this->Html->nestedList($list, ['class' => 'my-class', 'tag' => 'ol']);
        $this->assertEquals($expected, $result);
    }

    /**
     * Test for `para()` method
     * @test
     */
    public function testPara()
    {
        $expected = ['p' => true, '/p'];
        $this->assertHtml($expected, $this->Html->para());
        $this->assertHtml($expected, $this->Html->para(null));
        $this->assertHtml($expected, $this->Html->para(null, null));
        $this->assertHtml($expected, $this->Html->para(null, ''));

        $expected = ['p' => ['class' => 'my-class']];
        $this->assertHtml($expected, $this->Html->para('my-class'));
        $this->assertHtml($expected, $this->Html->para('my-class', null));

        $expected = ['p' => true, ' ', '/p'];
        $this->assertHtml($expected, $this->Html->para(null, ' '));

        $expected = [
            'p' => ['data-toggle' => 'tooltip', 'title' => 'my tooltip'],
            'my text',
            '/p'
        ];
        $result = $this->Html->para(null, 'my text', ['tooltip' => 'my tooltip']);
        $this->assertHtml($expected, $result);

        $expected = [
            'p' => ['class' => 'my-class', 'id' => 'my-id'],
            'i' => ['class' => 'fas fa-home'],
            ' ',
            '/i',
            ' ',
            'my text',
            '/p'
        ];
        $result = $this->Html->para('my-class', 'my text', ['id' => 'my-id', 'icon' => 'home']);
        $this->assertHtml($expected, $result);
    }

    /**
     * Test for `script()` and `js()` methods
     * @test
     */
    public function testScript()
    {
        //By default, `block` is `true`
        $this->assertNull($this->Html->script('my-file'));
        $this->assertNull($this->Html->script('my-file2', ['block' => true]));
        $this->assertNull($this->Html->js('my-file4'));
        $this->assertNull($this->Html->js('my-file5', ['block' => true]));

        $expected = ['script' => ['src' => '/js/my-file3.js']];
        $this->assertHtml($expected, $this->Html->script('my-file3', ['block' => false]));

        $expected = ['script' => ['src' => '/js/my-file6.js']];
        $this->assertHtml($expected, $this->Html->js('my-file6', ['block' => false]));
    }

    /**
     * Test for `scriptBlock()` method
     * @test
     */
    public function testScriptBlock()
    {
        $code = 'window.foo = 2;';

        //By default, `block` is `true`
        $this->assertNull($this->Html->scriptBlock($code, ['safe' => false]));
        $this->assertNull($this->Html->scriptBlock($code, ['block' => true, 'safe' => false]));

        $expected = ['<script', $code, '/script'];
        $this->assertHtml($expected, $this->Html->scriptBlock($code, ['block' => false, 'safe' => false]));
    }

    /**
     * Test for `scriptStart()` and `scriptEnd()` methods
     * @test
     */
    public function testScriptStartAndScriptEnd()
    {
        //By default, `block` is `true`
        $this->assertNull($this->Html->scriptStart(['safe' => false]));

        echo 'this is some javascript';
        $this->assertNull($this->Html->scriptEnd());
        $this->assertNull($this->Html->scriptStart(['block' => true, 'safe' => false]));

        echo 'this is some javascript';
        $this->assertNull($this->Html->scriptEnd());
        $this->assertNull($this->Html->scriptStart(['block' => false, 'safe' => false]));

        echo 'this is some javascript';
        $expected = ['<script', 'this is some javascript', '/script'];
        $this->assertHtml($expected, $this->Html->scriptEnd());
    }

    /**
     * Tests for `shareaholic()` method
     * @test
     */
    public function testShareaholic()
    {
        $expected = ['div' => [
            'data-app' => 'share_buttons',
            'data-app-id' => 'my-app-id',
            'class' => 'shareaholic-canvas',
        ]];
        $this->assertHtml($expected, $this->Html->shareaholic('my-app-id'));
    }

    /**
     * Test for `tag()` method
     * @test
     */
    public function testTag()
    {
        $text = 'My text';
        $class = 'my-class';

        $expected = ['h3' => true, '/h3'];
        $this->assertHtml($expected, $this->Html->tag('h3'));
        $this->assertHtml($expected, $this->Html->tag('h3', null));
        $this->assertHtml($expected, $this->Html->tag('h3', ''));

        $expected = [
            'h3' => ['class' => $class],
            $text,
            '/h3',
        ];
        $this->assertHtml($expected, $this->Html->tag('h3', $text, ['class' => $class]));

        $expected = [
            'h3' => ['data-toggle' => 'tooltip', 'title' => 'my tooltip'],
            $text,
            '/h3',
        ];
        $this->assertHtml($expected, $this->Html->tag('h3', $text, ['tooltip' => 'my tooltip']));

        // `tooltip` value rewrites `title` value
        $expected = [
            'h3' => ['data-toggle' => 'tooltip', 'title' => 'my tooltip'],
            $text,
            '/h3',
        ];
        $result = $this->Html->tag('h3', $text, ['title' => 'my custom title', 'tooltip' => 'my tooltip']);
        $this->assertHtml($expected, $result);

        $expected = [
            'h3' => ['class' => '$class'],
            'i' => ['class' => 'fas fa-home'],
            ' ',
            '/i',
            ' ',
            $text,
            '/h3',
        ];
        $this->assertHtml($expected, $this->Html->tag('h3', $text, ['class' => '$class', 'icon' => 'home']));

        $expected = [
            'h3' => ['class' => '$class'],
            $text,
            ' ',
            'i' => ['class' => 'fas fa-home'],
            ' ',
            '/i',
            '/h3',
        ];
        $result = $this->Html->tag('h3', $text, ['class' => '$class', 'icon' => 'home', 'icon-align' => 'right']);
        $this->assertHtml($expected, $result);
    }

    /**
     * Tests for `viewport()` method
     * @test
     */
    public function testViewport()
    {
        //By default, `block` is `true`
        $this->assertNull($this->Html->viewport());
        $this->assertNull($this->Html->viewport(['block' => true]));

        $expected = ['meta' => [
            'name' => 'viewport',
            'content' => 'initial-scale=1, shrink-to-fit=no, width=device-width',
        ]];
        $this->assertHtml($expected, $this->Html->viewport(['block' => false]));

        $expected = ['meta' => [
            'custom-option' => 'custom-value',
            'name' => 'viewport',
            'content' => 'initial-scale=1, shrink-to-fit=no, width=device-width',
        ]];
        $result = $this->Html->viewport(['block' => false, 'custom-option' => 'custom-value']);
        $this->assertHtml($expected, $result);
    }

    /**
     * Tests for `youtube()` method
     * @test
     */
    public function testYoutube()
    {
        $id = 'my-id';
        $url = sprintf('https://www.youtube.com/embed/%s', $id);

        $expected = [
            'div' => ['class' => 'embed-responsive embed-responsive-16by9'],
            'iframe' => [
                'allowfullscreen' => 'allowfullscreen',
                'height' => '480',
                'width' => '640',
                'class' => 'embed-responsive-item',
                'src' => $url,
            ],
            '/iframe',
            '/div',
        ];
        $this->assertHtml($expected, $this->Html->youtube($id));

        $expected = [
            'div' => ['class' => 'embed-responsive embed-responsive-4by3'],
            'iframe' => [
                'allowfullscreen' => 'allowfullscreen',
                'height' => '480',
                'width' => '640',
                'class' => 'embed-responsive-item',
                'src' => $url,
            ],
            '/iframe',
            '/div',
        ];
        $this->assertHtml($expected, $this->Html->youtube($id, ['ratio' => '4by3']));

        $expected = [
            'iframe' => [
                'allowfullscreen' => 'allowfullscreen',
                'height' => '480',
                'width' => '640',
                'src' => $url,
            ],
        ];
        $this->assertHtml($expected, $this->Html->youtube($id, ['ratio' => false]));

        $expected = [
            'div' => ['class' => 'embed-responsive embed-responsive-16by9'],
            'iframe' => [
                'allowfullscreen' => 'allowfullscreen',
                'height' => '100',
                'width' => '200',
                'class' => 'embed-responsive-item',
                'src' => $url,
            ],
            '/iframe',
            '/div',
        ];
        $this->assertHtml($expected, $this->Html->youtube($id, ['height' => 100, 'width' => 200]));

        $expected = [
            'div' => ['class' => 'embed-responsive embed-responsive-16by9'],
            'iframe' => [
                'allowfullscreen' => 'allowfullscreen',
                'height' => '480',
                'width' => '640',
                'class' => 'embed-responsive-item my-class',
                'src' => $url,
            ],
            '/iframe',
            '/div',
        ];
        $this->assertHtml($expected, $this->Html->youtube($id, ['class' => 'my-class']));
    }
}
