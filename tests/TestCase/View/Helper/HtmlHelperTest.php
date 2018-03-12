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
        $this->assertEquals('<i class="fa fa-home"> </i> ' . $text, $result);
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
        $this->assertEquals('<i class="fa fa-home"> </i>', $result);
        $this->assertInstanceOf('MeTools\View\OptionsParser', $options);
        $this->assertFalse($options->exists('icon'));
        $this->assertFalse($options->exists('icon-align'));

        //Using `icon-align` option
        $options = optionsParser(['icon' => 'home', 'icon-align' => 'right']);
        list($result, $options) = $this->Html->addIconToText($text, $options);
        $this->assertEquals($text . ' <i class="fa fa-home"> </i>', $result);
        $this->assertInstanceOf('MeTools\View\OptionsParser', $options);
        $this->assertFalse($options->exists('icon'));
        $this->assertFalse($options->exists('icon-align'));

        //Invalid `icon-align` option
        $options = optionsParser(['icon' => 'home', 'icon-align' => 'left']);
        list($result, $options) = $this->Html->addIconToText($text, $options);
        $this->assertEquals('<i class="fa fa-home"> </i> ' . $text, $result);
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

        $result = $this->Html->badge($text);
        $expected = [
            'span' => ['class' => 'badge'],
            $text,
            '/span',
        ];
        $this->assertHtml($expected, $result);

        $result = $this->Html->badge($text, ['class' => 'my-class']);
        $expected = [
            'span' => ['class' => 'badge my-class'],
            $text,
            '/span',
        ];
        $this->assertHtml($expected, $result);
    }

    /**
     * Test for `button()` method
     * @test
     */
    public function testButton()
    {
        $text = 'My text';

        $result = $this->Html->button($text);
        $expected = [
            'button' => [
                'class' => 'btn btn-secondary',
                'role' => 'button',
                'title' => $text,
            ],
            $text,
            '/button',
        ];
        $this->assertHtml($expected, $result);

        $result = $this->Html->button($text, null, [
            'title' => 'my-custom-title',
        ]);
        $expected = [
            'button' => [
                'class' => 'btn btn-secondary',
                'role' => 'button',
                'title' => 'my-custom-title',
            ],
            $text,
            '/button',
        ];
        $this->assertHtml($expected, $result);

        $result = $this->Html->button($text, null, ['class' => 'my-class']);
        $expected = [
            'button' => [
                'class' => 'btn btn-secondary my-class',
                'role' => 'button',
                'title' => $text,
             ],
            $text,
            '/button',
        ];
        $this->assertHtml($expected, $result);

        $result = $this->Html->button($text, null, ['class' => 'btn-primary']);
        $expected = [
            'button' => [
                'class' => 'btn btn-primary',
                'role' => 'button',
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
                'class' => 'btn btn-secondary',
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
                'class' => 'btn btn-secondary',
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
                'class' => 'btn btn-secondary',
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
        $result = $this->Html->button('Single quote \'', null);
        $expected = [
            'button' => [
                'role' => 'button',
                'class' => 'btn btn-secondary',
                'title' => 'Single quote &#039;',
            ],
            'Single quote \'',
            '/button',
        ];
        $this->assertHtml($expected, $result);

        //Double quote on text
        $result = $this->Html->button('Double quote "', null);
        $expected = [
            'button' => [
                'role' => 'button',
                'class' => 'btn btn-secondary',
                'title' => 'Double quote &quot;',
             ],
            'Double quote "',
            '/button',
        ];
        $this->assertHtml($expected, $result);

        //Single quote on custom title
        $result = $this->Html->button(
            $text,
            null,
            ['title' => 'Single quote \'']
        );
        $expected = [
            'button' => [
                'role' => 'button',
                'class' => 'btn btn-secondary',
                'title' => 'Single quote &#039;',
            ],
            $text,
            '/button',
        ];
        $this->assertHtml($expected, $result);

        //Double quote on custom title
        $result = $this->Html->button(
            $text,
            null,
            ['title' => 'Double quote "']
        );
        $expected = [
            'button' => [
                'title' => 'Double quote &quot;',
                'role' => 'button',
                'class' => 'btn btn-secondary',
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
                'class' => 'btn btn-secondary',
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
                'class' => 'btn btn-secondary',
                'role' => 'button',
                'title' => 'Code and text'
            ],
            $text,
            '/button',
        ];
        $this->assertHtml($expected, $result);
    }

    /**
     * Test for `button()` method, with buttons as links
     * @test
     */
    public function testButtonAsLink()
    {
        $text = 'My text';

        $result = $this->Html->button($text, '#');
        $expected = [
            'a' => [
                'class' => 'btn btn-secondary',
                'href' => '#',
                'role' => 'button',
                'title' => $text,
            ],
            $text,
            '/a',
        ];
        $this->assertHtml($expected, $result);

        $result = $this->Html->button($text, '#', ['class' => 'my-class']);
        $expected = [
            'a' => [
                'class' => 'btn btn-secondary my-class',
                'href' => '#',
                'role' => 'button',
                'title' => $text,
            ],
            $text,
            '/a',
        ];
        $this->assertHtml($expected, $result);

        $result = $this->Html->button($text, '#', ['class' => 'btn-primary']);
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
        $this->assertHtml($expected, $result);

        $result = $this->Html->button($text, '#', ['tooltip' => 'my tooltip']);
        $expected = [
            'a' => [
                'class' => 'btn btn-secondary',
                'href' => '#',
                'role' => 'button',
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
                'class' => 'btn btn-secondary',
                'href' => '#',
                'role' => 'button',
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
                'class' => 'btn btn-secondary',
                'href' => '#',
                'role' => 'button',
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
        $expected = ['link' => ['rel' => 'stylesheet', 'href' => '/css/my-file3.css']];
        $this->assertHtml($expected, $result);

        $result = $this->Html->css('my-file4', ['block' => false, 'rel' => 'alternate']);
        $expected = ['link' => ['rel' => 'alternate', 'href' => '/css/my-file4.css']];
        $this->assertHtml($expected, $result);
    }

    /**
     * Test for `cssBlock()` method
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

        $this->Html->cssStart(['block' => false]);
        echo $css;
        $result = $this->Html->cssEnd();
        $expected = ['<style', $css, '/style'];
        $this->assertHtml($expected, $result);
    }

    /**
     * Test for `div()` method
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
     * Tests for `heading()` method

     * @test
     */
    public function testHeading()
    {
        $text = 'My header';
        $smallText = 'My small text';

        $expected = ['h2' => true, $text, '/h2'];

        $result = $this->Html->heading($text);
        $this->assertHtml($expected, $result);

        //It still creates a h2 tag
        $result = $this->Html->heading($text, ['type' => 'strong']);
        $this->assertHtml($expected, $result);

        $result = $this->Html->heading($text, ['type' => 'h4']);
        $expected = ['h4' => true, $text, '/h4'];
        $this->assertHtml($expected, $result);

        $result = $this->Html->heading($text, [], $smallText);
        $expected = [
            'h2' => true,
            $text,
            ' ',
            'small' => true,
            $smallText,
            '/small',
            '/h2',
        ];
        $this->assertHtml($expected, $result);

        $result = $this->Html->heading($text, ['type' => 'h4'], $smallText);
        $expected = [
            'h4' => true,
            $text,
            ' ',
            'small' => true,
            $smallText,
            '/small',
            '/h4',
        ];
        $this->assertHtml($expected, $result);

        $result = $this->Html->heading(
            $text,
            ['class' => 'header-class'],
            $smallText,
            ['class' => 'small-class']
        );
        $expected = [
            'h2' => ['class' => 'header-class'],
            $text,
            ' ',
            'small' => ['class' => 'small-class'],
            $smallText,
            '/small',
            '/h2',
        ];
        $this->assertHtml($expected, $result);
    }

    /**
     * Test for `hr()` method
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
     * @test
     */
    public function testIcons()
    {
        $expected = [
            'i' => ['class' => 'fa fa-home'],
            ' ',
            '/i',
        ];

        foreach ([
            'home',
            'fa-home',
            'fa home',
            'fa fa-home',
            ['home'],
            ['fa', 'home'],
        ] as $icons) {
            $this->assertHtml($expected, $this->Html->icon($icons));
        }

        $this->assertHtml($expected, $this->Html->icon('fa', 'fa-home'));

        $expected = [
            'i' => ['class' => 'fa fa-2x fa-hand-o-right'],
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

        $result = $this->Html->iframe($url);
        $this->assertHtml($expected, $result);

        //No existing ratio
        $result = $this->Html->iframe($url, ['ratio' => 'noExisting']);
        $this->assertHtml($expected, $result);

        $result = $this->Html->iframe($url, ['class' => 'my-class']);
        $expected = ['iframe' => ['class' => 'my-class', 'src' => $url]];
        $this->assertHtml($expected, $result);

        //The `src` option doesn't overwrite
        $result = $this->Html->iframe($url, ['src' => 'http://anotherframe']);
        $expected = ['iframe' => ['src' => $url]];
        $this->assertHtml($expected, $result);

        $result = $this->Html->iframe($url, ['ratio' => '16by9']);
        $expected = [
            'div' => ['class' => 'embed-responsive embed-responsive-16by9'],
            'iframe' => ['class' => 'embed-responsive-item', 'src' => $url],
            '/iframe',
            '/div',
        ];
        $this->assertHtml($expected, $result);

        $result = $this->Html->iframe($url, ['ratio' => '4by3']);
        $expected = [
            'div' => ['class' => 'embed-responsive embed-responsive-4by3'],
            'iframe' => ['class' => 'embed-responsive-item', 'src' => $url],
            '/iframe',
            '/div',
        ];
        $this->assertHtml($expected, $result);

        $result = $this->Html->iframe($url, ['class' => 'my-class', 'ratio' => '16by9']);
        $expected = [
            'div' => ['class' => 'embed-responsive embed-responsive-16by9'],
            'iframe' => [
                'class' => 'embed-responsive-item my-class',
                'src' => $url,
            ],
            '/iframe',
            '/div',
        ];
        $this->assertHtml($expected, $result);
    }

    /**
     * Test for `image()` and `img()` methods
     * @test
     */
    public function testImage()
    {
        $image = 'image.gif';

        $result = $this->Html->image($image);
        $expected = [
            'img' => [
                'src' => '/img/image.gif',
                'alt' => $image,
                'class' => 'img-fluid',
            ],
        ];
        $this->assertHtml($expected, $result);

        $result = $this->Html->image($image, ['class' => 'my-class']);
        $expected = [
            'img' => [
                'src' => '/img/image.gif',
                'alt' => $image,
                'class' => 'img-fluid my-class',
            ],
        ];
        $this->assertHtml($expected, $result);

        //Tests `img()` alias
        $result = $this->Html->img($image, ['class' => 'my-class']);
        $expected = [
            'img' => [
                'src' => '/img/image.gif',
                'alt' => $image,
                'class' => 'img-fluid my-class',
            ],
        ];
        $this->assertHtml($expected, $result);

        $result = $this->Html->image($image, ['alt' => 'my-alt']);
        $expected = [
            'img' => [
                'src' => '/img/image.gif',
                'alt' => 'my-alt',
                'class' => 'img-fluid',
            ],
        ];
        $this->assertHtml($expected, $result);

        $result = $this->Html->image($image, ['tooltip' => 'my tooltip']);
        $expected = [
            'img' => [
                'src' => '/img/image.gif',
                'alt' => $image,
                'class' => 'img-fluid',
                'data-toggle' => 'tooltip',
                'title' => 'my tooltip',
            ],
        ];
        $this->assertHtml($expected, $result);

        $result = $this->Html->image('http://fullurl/image.gif');
        $expected = [
            'img' => [
                'src' => 'http://fullurl/image.gif',
                'alt' => $image,
                'class' => 'img-fluid',
            ],
        ];
        $this->assertHtml($expected, $result);
    }

    /**
     * Tests for `label()` method
     * @test
     */
    public function testLabel()
    {
        $text = 'My text';

        $result = $this->Html->label($text);
        $expected = [
            'span' => ['class' => 'label label-default'],
            $text,
            '/span',
        ];
        $this->assertHtml($expected, $result);

        $result = $this->Html->label($text, ['class' => 'my-class']);
        $expected = [
            'span' => ['class' => 'label label-default my-class'],
            $text,
            '/span',
        ];
        $this->assertHtml($expected, $result);

        $result = $this->Html->label($text, ['type' => 'success']);
        $expected = [
            'span' => ['class' => 'label label-success'],
            $text,
            '/span',
        ];
        $this->assertHtml($expected, $result);

        $result = $this->Html->label(
            $text,
            ['class' => 'my-class', 'type' => 'success']
        );
        $expected = [
            'span' => ['class' => 'label label-success my-class'],
            $text,
            '/span',
        ];
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
     * @test
     */
    public function testLink()
    {
        $title = 'My title';

        $result = $this->Html->link($title, 'http://link', ['title' => 'my-custom-title']);
        $expected = [
            'a' => ['href' => 'http://link', 'title' => 'my-custom-title'],
            $title,
            '/a',
        ];
        $this->assertHtml($expected, $result);

        $result = $this->Html->link($title, 'http://link', ['icon' => 'home']);
        $expected = [
            'a' => ['href' => 'http://link', 'title' => $title],
            'i' => ['class' => 'fa fa-home'],
            ' ',
            '/i',
            ' ',
            $title,
            '/a',
        ];
        $this->assertHtml($expected, $result);

        $result = $this->Html->link($title, '#', ['icon' => 'home', 'icon-align' => 'right']);
        $expected = [
            'a' => ['href' => '#', 'title' => $title],
            $title,
            ' ',
            'i' => ['class' => 'fa fa-home'],
            ' ',
            '/i',
            '/a',
        ];
        $this->assertHtml($expected, $result);

        //Single quote on text
        $result = $this->Html->link('Single quote \'', '#');
        $expected = [
            'a' => ['href' => '#', 'title' => 'Single quote &#039;'],
            'Single quote \'',
            '/a',
        ];
        $this->assertHtml($expected, $result);

        //Double quote on text
        $result = $this->Html->link('Double quote "', '#');
        $expected = [
            'a' => ['href' => '#', 'title' => 'Double quote &quot;'],
            'Double quote "',
            '/a',
        ];
        $this->assertHtml($expected, $result);

        //Single quote on custom title
        $result = $this->Html->link($title, '#', ['title' => 'Single quote \'']);
        $expected = [
            'a' => ['href' => '#', 'title' => 'Single quote &#039;'],
            $title,
            '/a',
        ];
        $this->assertHtml($expected, $result);

        //Double quote on custom title
        $result = $this->Html->link($title, '#', ['title' => 'Double quote "']);
        $expected = [
            'a' => ['href' => '#', 'title' => 'Double quote &quot;'],
            $title,
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
        $result = $this->Html->link($title, '#', ['title' => '<u>Code</u> and text']);
        $expected = [
            'a' => ['href' => '#', 'title' => 'Code and text'],
            $title,
            '/a',
        ];
        $this->assertHtml($expected, $result);

        $result = $this->Html->link($title, '#', ['tooltip' => 'my tooltip']);
        $expected = [
            'a' => [
                'href' => '#',
                'data-toggle' => 'tooltip',
                'title' => 'my tooltip',
            ],
            $title,
            '/a'
        ];
        $this->assertHtml($expected, $result);

        // `tooltip` value rewrites `title` value
        $result = $this->Html->link($title, '#', ['title' => 'my custom title', 'tooltip' => 'my tooltip']);
        $expected = [
            'a' => [
                'href' => '#',
                'data-toggle' => 'tooltip',
                'title' => 'my tooltip',
            ],
            $title,
            '/a'
        ];
        $this->assertHtml($expected, $result);

        //Tooltip with alignment
        $result = $this->Html->link($title, '#', ['tooltip' => 'my tooltip', 'tooltip-align' => 'bottom']);
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
        $this->assertHtml($expected, $result);

        $result = $this->Html->link($title, '#', ['tooltip' => 'Single quote \'']);
        $expected = [
            'a' => [
                'href' => '#',
                'title' => 'Single quote &#039;',
                'data-toggle' => 'tooltip',
            ],
            $title,
            '/a'
        ];
        $this->assertHtml($expected, $result);

        $result = $this->Html->link($title, '#', ['tooltip' => 'Double quote "']);
        $expected = [
            'a' => [
                'href' => '#',
                'title' => 'Double quote &quot;',
                'data-toggle' => 'tooltip',
            ],
            $title,
            '/a'
        ];
        $this->assertHtml($expected, $result);

        $result = $this->Html->link($title, '#', ['tooltip' => '<u>Code</u> and text']);
        $expected = [
            'a' => [
                'href' => '#',
                'title' => 'Code and text',
                'data-toggle' => 'tooltip',
            ],
            $title,
            '/a'
        ];
        $this->assertHtml($expected, $result);
    }

    /**
     * Test for `meta()` method
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
            'ul' => ['class' => 'fa-ul list-class'],
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
        $result = $this->Html->ul($list);
        $expected = $this->Html->nestedList($list);
        $this->assertEquals($expected, $result);

        $result = $this->Html->ul($list);
        $expected = $this->Html->nestedList($list, ['tag' => 'ul']);
        $this->assertEquals($expected, $result);

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
        $expected = ['script' => ['src' => '/js/my-file3.js']];
        $this->assertHtml($expected, $result);

        //By default, `block` is `true`
        $result = $this->Html->js('my-file4');
        $this->assertNull($result);

        $result = $this->Html->js('my-file5', ['block' => true]);
        $this->assertNull($result);

        $result = $this->Html->js('my-file6', ['block' => false]);
        $expected = ['script' => ['src' => '/js/my-file6.js']];
        $this->assertHtml($expected, $result);
    }

    /**
     * Test for `scriptBlock()` method
     * @test
     */
    public function testScriptBlock()
    {
        $code = 'window.foo = 2;';

        //By default, `block` is `true`
        $result = $this->Html->scriptBlock($code, ['safe' => false]);
        $this->assertNull($result);

        $result = $this->Html->scriptBlock($code, ['block' => true, 'safe' => false]);
        $this->assertNull($result);

        $result = $this->Html->scriptBlock($code, ['block' => false, 'safe' => false]);
        $expected = ['<script', $code, '/script'];
        $this->assertHtml($expected, $result);
    }

    /**
     * Test for `scriptStart()` and `scriptEnd()` methods
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
     * Tests for `shareaholic()` method
     * @test
     */
    public function testShareaholic()
    {
        $result = $this->Html->shareaholic('my-app-id');
        $expected = ['div' => [
            'data-app' => 'share_buttons',
            'data-app-id' => 'my-app-id',
            'class' => 'shareaholic-canvas',
        ]];
        $this->assertHtml($expected, $result);
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

        $result = $this->Html->tag('h3');
        $this->assertHtml($expected, $result);

        $result = $this->Html->tag('h3', null);
        $this->assertHtml($expected, $result);

        $result = $this->Html->tag('h3', '');
        $this->assertHtml($expected, $result);

        $result = $this->Html->tag('h3', $text, ['class' => $class]);
        $expected = [
            'h3' => ['class' => $class],
            $text,
            '/h3',
        ];
        $this->assertHtml($expected, $result);

        $result = $this->Html->tag('h3', $text, ['tooltip' => 'my tooltip']);
        $expected = [
            'h3' => ['data-toggle' => 'tooltip', 'title' => 'my tooltip'],
            $text,
            '/h3',
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
            '/h3',
        ];
        $this->assertHtml($expected, $result);

        $result = $this->Html->tag('h3', $text, ['class' => '$class', 'icon' => 'home']);
        $expected = [
            'h3' => ['class' => '$class'],
            'i' => ['class' => 'fa fa-home'],
            ' ',
            '/i',
            ' ',
            $text,
            '/h3',
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
            '/h3',
        ];
        $this->assertHtml($expected, $result);
    }

    /**
     * Tests for `viewport()` method
     * @test
     */
    public function testViewport()
    {
        //By default, `block` is `true`
        $result = $this->Html->viewport();
        $this->assertNull($result);

        $result = $this->Html->viewport(['block' => true]);
        $this->assertNull($result);

        $result = $this->Html->viewport(['block' => false]);
        $expected = ['meta' => [
            'name' => 'viewport',
            'content' => 'initial-scale=1, shrink-to-fit=no, width=device-width',
        ]];
        $this->assertHtml($expected, $result);

        $result = $this->Html->viewport(['block' => false, 'custom-option' => 'custom-value']);
        $expected = ['meta' => [
            'custom-option' => 'custom-value',
            'name' => 'viewport',
            'content' => 'initial-scale=1, shrink-to-fit=no, width=device-width',
        ]];
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

        $result = $this->Html->youtube($id);
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
        $this->assertHtml($expected, $result);

        $result = $this->Html->youtube($id, ['ratio' => '4by3']);
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
        $this->assertHtml($expected, $result);

        $result = $this->Html->youtube($id, ['ratio' => false]);
        $expected = [
            'iframe' => [
                'allowfullscreen' => 'allowfullscreen',
                'height' => '480',
                'width' => '640',
                'src' => $url,
            ],
        ];
        $this->assertHtml($expected, $result);

        $result = $this->Html->youtube($id, ['height' => 100, 'width' => 200]);
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
        $this->assertHtml($expected, $result);

        $result = $this->Html->youtube($id, ['class' => 'my-class']);
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
        $this->assertHtml($expected, $result);
    }
}
