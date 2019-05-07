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
namespace MeTools\Test\TestCase\View\Helper;

use Cake\Core\Exception\Exception;
use MeTools\TestSuite\HelperTestCase;
use MeTools\View\OptionsParser;

/**
 * HtmlHelperTest class
 */
class HtmlHelperTest extends HelperTestCase
{
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
        $this->assertFalse(method_exists($this->Helper, 'h3'));

        $expected = $this->Helper->tag('h3', $text, compact('class'));
        $this->assertEquals($expected, $this->Helper->h3($text, compact('class')));

        $expected = $this->Helper->tag('h3', $text, compact('class') + ['icon' => 'home']);
        $this->assertEquals($expected, $this->Helper->h3($text, ['class' => $class, 'icon' => 'home']));

        $expected = $this->Helper->tag('h3', $text, compact('class') + ['icon' => 'home', 'icon-align' => 'right']);
        $result = $this->Helper->h3($text, compact('class') + ['icon' => 'home', 'icon-align' => 'right']);
        $this->assertEquals($expected, $result);

        //With a no existing method
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Method `' . get_parent_class($this->Helper) . '::noExistingMethod()` does not exist');
        $this->Helper->noExistingMethod(null, null, null);
    }

    /**
     * Tests for `addIconToText()` method
     * @test
     */
    public function testAddIconToText()
    {
        $text = 'My text';

        $options = optionsParser(['icon' => 'home']);
        list($result, $options) = $this->Helper->addIconToText($text, $options);
        $this->assertEquals('<i class="fas fa-home"> </i> ' . $text, $result);
        $this->assertInstanceOf(OptionsParser::class, $options);
        $this->assertFalse($options->exists('icon'));
        $this->assertFalse($options->exists('icon-align'));

        //Missing `icon` option
        $options = optionsParser(['class' => 'my-class', 'icon-align' => 'right']);
        list($result, $options) = $this->Helper->addIconToText($text, $options);
        $this->assertEquals($text, $result);
        $this->assertInstanceOf(OptionsParser::class, $options);
        $this->assertFalse($options->exists('icon'));
        $this->assertFalse($options->exists('icon-align'));
        $this->assertEquals('my-class', $options->get('class'));

        //Empty text
        $options = optionsParser(['icon' => 'home']);
        list($result, $options) = $this->Helper->addIconToText(null, $options);
        $this->assertEquals('<i class="fas fa-home"> </i>', $result);
        $this->assertInstanceOf(OptionsParser::class, $options);
        $this->assertFalse($options->exists('icon'));
        $this->assertFalse($options->exists('icon-align'));

        //Using `icon-align` option
        $options = optionsParser(['icon' => 'home', 'icon-align' => 'right']);
        list($result, $options) = $this->Helper->addIconToText($text, $options);
        $this->assertEquals($text . ' <i class="fas fa-home"> </i>', $result);
        $this->assertInstanceOf(OptionsParser::class, $options);
        $this->assertFalse($options->exists('icon'));
        $this->assertFalse($options->exists('icon-align'));

        //Invalid `icon-align` option
        $options = optionsParser(['icon' => 'home', 'icon-align' => 'left']);
        list($result, $options) = $this->Helper->addIconToText($text, $options);
        $this->assertEquals('<i class="fas fa-home"> </i> ' . $text, $result);
        $this->assertInstanceOf(OptionsParser::class, $options);
        $this->assertFalse($options->exists('icon'));
        $this->assertFalse($options->exists('icon-align'));
    }

    /**
     * Tests for `badge()` method
     * @test
     */
    public function testBadge()
    {
        $expected = ['span' => ['class' => 'badge my-class'], 'My text', '/span'];
        $this->assertHtml($expected, $this->Helper->badge('My text', ['class' => 'my-class']));
    }

    /**
     * Test for `button()` method
     * @test
     */
    public function testButton()
    {
        $text = 'My text';

        $expected = ['button' => ['class' => 'btn btn-light', 'role' => 'button', 'title' => $text], $text, '/button'];
        $this->assertHtml($expected, $this->Helper->button($text));

        $expected = ['button' => ['class' => 'btn btn-light', 'role' => 'button', 'title' => 'my-custom-title'], $text, '/button'];
        $this->assertHtml($expected, $this->Helper->button($text, null, ['title' => 'my-custom-title']));

        $expected = ['button' => ['class' => 'btn btn-primary my-class', 'role' => 'button', 'title' => $text], $text, '/button'];
        $this->assertHtml($expected, $this->Helper->button($text, null, ['class' => 'btn-primary my-class']));

        // `tooltip` value rewrites `title` value
        $expected = [
            'button' => ['role' => 'button', 'class' => 'btn btn-light', 'data-toggle' => 'tooltip', 'title' => 'my tooltip'],
            $text,
            '/button'
        ];
        $result = $this->Helper->button($text, null, ['title' => 'my custom title', 'tooltip' => 'my tooltip']);
        $this->assertHtml($expected, $result);

        $expected = [
            'button' => ['role' => 'button', 'class' => 'btn btn-light', 'title' => $text],
            'i' => ['class' => 'fas fa-home'],
            ' ',
            '/i',
            ' ',
            $text,
            '/button',
        ];
        $this->assertHtml($expected, $this->Helper->button($text, null, ['icon' => 'home']));

        //Quotes on text
        $expected = [
            'button' => ['role' => 'button', 'class' => 'btn btn-light', 'title' => '&quot; &#039;'],
            '" \'',
            '/button',
        ];
        $this->assertHtml($expected, $this->Helper->button('" \'', null));

        //Quotes on custom title
        $expected = ['button' => ['role' => 'button', 'class' => 'btn btn-light', 'title' => '&quot; &#039;'], $text, '/button'];
        $this->assertHtml($expected, $this->Helper->button($text, null, ['title' => '" \'']));

        //Code on text
        $expected = [
            'button' => ['role' => 'button', 'class' => 'btn btn-light', 'title' => 'Code'],
            'u' => true,
            'Code',
            '/u',
            '/button',
        ];
        $this->assertHtml($expected, $this->Helper->button('<u>Code</u>', null));

        //Code on custom title
        $expected = ['button' => ['class' => 'btn btn-light', 'role' => 'button', 'title' => 'Code'], $text, '/button'];
        $this->assertHtml($expected, $this->Helper->button($text, null, ['title' => '<u>Code</u>']));
    }

    /**
     * Test for `button()` method, with buttons as links
     * @test
     */
    public function testButtonAsLink()
    {
        $text = 'My text';

        $expected = ['a' => ['class' => 'btn btn-primary my-class', 'href' => '#', 'role' => 'button', 'title' => $text], $text, '/a'];
        $this->assertHtml($expected, $this->Helper->button($text, '#', ['class' => 'btn-primary my-class']));

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
        $result = $this->Helper->button($text, '#', ['title' => 'my custom title', 'tooltip' => 'my tooltip']);
        $this->assertHtml($expected, $result);

        $expected = [
            'a' => ['class' => 'btn btn-light', 'href' => '#', 'role' => 'button', 'title' => $text],
            'i' => ['class' => 'fas fa-home'],
            ' ',
            '/i',
            ' ',
            $text,
            '/a',
        ];
        $this->assertHtml($expected, $this->Helper->button($text, '#', ['icon' => 'home']));
    }

    /**
     * Test for `css()` method
     * @test
     */
    public function testCss()
    {
        //By default, `block` is `true`
        $this->assertNull($this->Helper->css('my-file'));
        $this->assertNull($this->Helper->css('my-file2', ['block' => true]));

        $expected = ['link' => ['rel' => 'stylesheet', 'href' => '/css/my-file3.css']];
        $this->assertHtml($expected, $this->Helper->css('my-file3', ['block' => false]));

        $expected = ['link' => ['rel' => 'alternate', 'href' => '/css/my-file4.css']];
        $result = $this->Helper->css('my-file4', ['block' => false, 'rel' => 'alternate']);
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
        $this->assertNull($this->Helper->cssBlock($css));
        $this->assertNull($this->Helper->cssBlock($css, ['block' => true]));

        $expected = ['style' => true, $css, '/style'];
        $this->assertHtml($expected, $this->Helper->cssBlock($css, ['block' => false]));
    }

    /**
     * Test for `cssStart()` and `cssEnd()` methods
     * @test
     */
    public function testCssStartAndCssEnd()
    {
        $css = 'body { color: red; }';

        //By default, `block` is `true`
        $this->Helper->cssStart();
        echo $css;
        $result = $this->Helper->cssEnd();
        $this->assertNull($result);
        $this->Helper->cssStart(['block' => true]);
        echo $css;
        $result = $this->Helper->cssEnd();
        $this->assertNull($result);

        $expected = ['<style', $css, '/style'];
        $this->Helper->cssStart(['block' => false]);
        echo $css;
        $result = $this->Helper->cssEnd();
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
        $this->assertHtml($expected, $this->Helper->heading($text));

        //It still creates a h2 tag
        $this->assertHtml($expected, $this->Helper->heading($text, ['type' => 'strong']));

        $expected = ['h4' => true, $text, '/h4'];
        $this->assertHtml($expected, $this->Helper->heading($text, ['type' => 'h4']));

        $expected = [
            'h4' => ['class' => 'header-class'],
            $text,
            ' ',
            'small' => ['class' => 'small-class'],
            $smallText,
            '/small',
            '/h4',
        ];
        $result = $this->Helper->heading($text, ['class' => 'header-class', 'type' => 'h4'], $smallText, ['class' => 'small-class']);
        $this->assertHtml($expected, $result);
    }

    /**
     * Test for `hr()` method
     * @test
     */
    public function testHr()
    {
        $expected = $this->Helper->tag('hr', null, ['class' => 'my-hr-class']);
        $this->assertEquals($expected, $this->Helper->hr(['class' => 'my-hr-class']));
    }

    /**
     * Test for `icon()` method
     * @test
     */
    public function testIcons()
    {
        $expected = ['i' => ['class' => 'preg:/(fa|fab|fal|far|fas) fa\-home/'], ' ', '/i'];
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
            $this->assertHtml($expected, $this->Helper->icon($icons));
        }
        $this->assertHtml($expected, $this->Helper->icon('fa', 'fa-home'));

        $expected = ['i' => ['class' => 'fas fa-hand-o-right fa-2x'], ' ', '/i'];
        foreach (['hand-o-right 2x', ['hand-o-right', '2x']] as $icons) {
            $this->assertHtml($expected, $this->Helper->icon($icons));
        }
        $this->assertHtml($expected, $this->Helper->icon('hand-o-right', '2x'));
    }

    /**
     * Test for `iframe()` method
     * @test
     */
    public function testIframe()
    {
        $url = 'http://frame';

        $expected = ['iframe' => ['src' => $url]];

        //No existing ratio
        $this->assertHtml($expected, $this->Helper->iframe($url, ['ratio' => 'noExisting']));

        $expected = ['iframe' => ['class' => 'my-class', 'src' => $url]];
        $this->assertHtml($expected, $this->Helper->iframe($url, ['class' => 'my-class']));

        //The `src` option doesn't overwrite
        $expected = ['iframe' => ['src' => $url]];
        $this->assertHtml($expected, $this->Helper->iframe($url, ['src' => 'http://anotherframe']));

        $expected = [
            'div' => [],
            'iframe' => ['class' => 'embed-responsive-item', 'src' => $url],
            '/iframe',
            '/div',
        ];
        foreach (['16by9', '4by3'] as $ratio) {
            $expected['div']['class'] = 'embed-responsive embed-responsive-' . $ratio;
            $this->assertHtml($expected, $this->Helper->iframe($url, compact('ratio')));
        }

        $expected = [
            'div' => ['class' => 'embed-responsive embed-responsive-16by9'],
            'iframe' => ['class' => 'embed-responsive-item my-class', 'src' => $url],
            '/iframe',
            '/div',
        ];
        $this->assertHtml($expected, $this->Helper->iframe($url, ['class' => 'my-class', 'ratio' => '16by9']));
    }

    /**
     * Test for `image()` and `img()` methods
     * @test
     */
    public function testImage()
    {
        $image = 'image.gif';

        $expected = ['img' => ['src' => '/img/image.gif', 'alt' => $image, 'class' => 'img-fluid my-class']];
        $this->assertHtml($expected, $this->Helper->image($image, ['class' => 'my-class']));
        $this->assertHtml($expected, $this->Helper->img($image, ['class' => 'my-class']));

        $expected = ['img' => ['src' => '/img/image.gif', 'alt' => 'my-alt', 'class' => 'img-fluid']];
        $this->assertHtml($expected, $this->Helper->image($image, ['alt' => 'my-alt']));

        $expected = [
            'img' => [
                'src' => '/img/image.gif',
                'alt' => $image,
                'class' => 'img-fluid',
                'data-toggle' => 'tooltip',
                'title' => 'my tooltip',
            ],
        ];
        $this->assertHtml($expected, $this->Helper->image($image, ['tooltip' => 'my tooltip']));

        $expected = ['img' => ['src' => 'http://fullurl/image.gif', 'alt' => $image, 'class' => 'img-fluid']];
        $this->assertHtml($expected, $this->Helper->image('http://fullurl/image.gif'));
    }

    /**
     * Tests for `label()` method
     * @test
     */
    public function testLabel()
    {
        $text = 'My text';

        $expected = ['span' => ['class' => 'label label-default my-class'], $text, '/span'];
        $this->assertHtml($expected, $this->Helper->label($text, ['class' => 'my-class']));

        $expected = ['span' => ['class' => 'another-class label label-success'], $text, '/span'];
        $this->assertHtml($expected, $this->Helper->label($text, ['class' => 'another-class', 'type' => 'success']));
    }

    /**
     * Test for `li()` method
     * @test
     */
    public function testLi()
    {
        $expected = [
            'li' => true,
            'i' => ['class' => 'fas fa-home'],
            ' ',
            '/i',
            ' ',
            'My text',
            '/li'
        ];
        $this->assertHtml($expected, $this->Helper->li('My text', ['icon' => 'home']));

        $expected = [
            ['li' => ['class' => 'my-class']],
            ['i' => ['class' => 'fas fa-home']],
            ' ',
            '/i',
            ' ',
            'first-value',
            '/li',
            ['li' => ['class' => 'my-class']],
            ['i' => ['class' => 'fas fa-home']],
            ' ',
            '/i',
            ' ',
            'second-value',
            '/li',
        ];
        $this->assertHtml($expected, $this->Helper->li(['first-value', 'second-value'], ['class' => 'my-class', 'icon' => 'home']));
    }

    /**
     * Test for `link()` method
     * @test
     */
    public function testLink()
    {
        $title = 'My title';

        $expected = ['a' => ['href' => 'http://link', 'title' => 'my-title'], $title, '/a'];
        $this->assertHtml($expected, $this->Helper->link($title, 'http://link', ['title' => 'my-title']));

        $expected = [
            'a' => ['href' => '#', 'title' => $title],
            $title,
            ' ',
            'i' => ['class' => 'fas fa-home'],
            ' ',
            '/i',
            '/a',
        ];
        $this->assertHtml($expected, $this->Helper->link($title, '#', ['icon' => 'home', 'icon-align' => 'right']));

        //Quotes on text
        $expected = ['a' => ['href' => '#', 'title' => '&quot; &#039;'], '" \'', '/a'];
        $this->assertHtml($expected, $this->Helper->link('" \'', '#'));

        //Quotes on custom title
        $expected = ['a' => ['href' => '#', 'title' => '&quot; &#039;'], $title, '/a'];
        $this->assertHtml($expected, $this->Helper->link($title, '#', ['title' => '" \'']));

        //Code on text
        $expected = [
            'a' => ['href' => '#', 'title' => 'Code'],
            'u' => true,
            'Code',
            '/u',
            '/a',
        ];
        $this->assertHtml($expected, $this->Helper->link('<u>Code</u> ', '#'));

        //Code on custom title
        $expected = ['a' => ['href' => '#', 'title' => 'Code'], $title, '/a'];
        $this->assertHtml($expected, $this->Helper->link($title, '#', ['title' => '<u>Code</u>']));

        // `tooltip` value rewrites `title` value
        $expected = ['a' => ['href' => '#', 'data-toggle' => 'tooltip', 'title' => 'my tooltip'], $title, '/a'];
        $this->assertHtml($expected, $this->Helper->link($title, '#', ['title' => $title, 'tooltip' => 'my tooltip']));

        //Tooltip with alignment
        $expected = [
            'a' => ['href' => '#', 'data-placement' => 'bottom', 'data-toggle' => 'tooltip', 'title' => 'my tooltip'],
            $title,
            '/a'
        ];
        $this->assertHtml($expected, $this->Helper->link($title, '#', ['tooltip' => 'my tooltip', 'tooltip-align' => 'bottom']));

        $expected = [
            'a' => ['href' => '#', 'title' => '&quot; &#039;', 'data-toggle' => 'tooltip'],
            $title,
            '/a'
        ];
        $this->assertHtml($expected, $this->Helper->link($title, '#', ['tooltip' => '" \'']));

        $expected = [
            'a' => ['href' => '#', 'title' => 'Code', 'data-toggle' => 'tooltip'],
            $title,
            '/a'
        ];
        $this->assertHtml($expected, $this->Helper->link($title, '#', ['tooltip' => '<u>Code</u>']));
    }

    /**
     * Test for `meta()` method
     * @test
     */
    public function testMeta()
    {
        //By default, `block` is `true`
        $this->assertNull($this->Helper->meta('viewport', 'width=device-width'));
        $this->assertNull($this->Helper->meta('viewport', 'width=device-width', ['block' => true]));

        $expected = ['meta' => ['name' => 'viewport', 'content' => 'width=device-width']];
        $this->assertHtml($expected, $this->Helper->meta('viewport', 'width=device-width', ['block' => false]));
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
        $this->assertHtml($expected, $this->Helper->ul($list, [], ['icon' => 'home']));
        $this->assertEquals($this->Helper->ul($list, [], ['icon' => 'home']), $this->Helper->ul($list, ['icon' => 'home']));

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
        $result = $this->Helper->ul($list, ['class' => 'list-class'], ['class' => 'item-class', 'icon' => 'home']);
        $this->assertHtml($expected, $result);

        $expected = $this->Helper->nestedList($list, ['class' => 'my-class', 'tag' => 'ul']);
        $this->assertEquals($expected, $this->Helper->ul($list, ['class' => 'my-class']));

        $expected = $this->Helper->nestedList($list, ['class' => 'my-class', 'tag' => 'ol']);
        $this->assertEquals($expected, $this->Helper->ol($list, ['class' => 'my-class']));
    }

    /**
     * Test for `para()` method
     * @test
     */
    public function testPara()
    {
        $expected = ['p' => true, '/p'];
        $this->assertHtml($expected, $this->Helper->para());
        $this->assertHtml($expected, $this->Helper->para(null));
        $this->assertHtml($expected, $this->Helper->para(null, null));
        $this->assertHtml($expected, $this->Helper->para(null, ''));

        $expected = ['p' => ['class' => 'my-class']];
        $this->assertHtml($expected, $this->Helper->para('my-class'));
        $this->assertHtml($expected, $this->Helper->para('my-class', null));

        $expected = ['p' => true, ' ', '/p'];
        $this->assertHtml($expected, $this->Helper->para(null, ' '));

        $expected = ['p' => ['data-toggle' => 'tooltip', 'title' => 'my tooltip'], 'my text', '/p'];
        $result = $this->Helper->para(null, 'my text', ['tooltip' => 'my tooltip']);
        $this->assertHtml($expected, $result);

        $expected = [
            'p' => ['class' => 'my-class', 'id' => 'my-id'],
            'i' => ['class' => 'fas fa-home'],
            ' ',
            '/i',
            ' ',
            'text',
            '/p'
        ];
        $this->assertHtml($expected, $this->Helper->para('my-class', 'text', ['id' => 'my-id', 'icon' => 'home']));
    }

    /**
     * Test for `script()` and `js()` methods
     * @test
     */
    public function testScript()
    {
        //By default, `block` is `true`
        $this->assertNull($this->Helper->script('my-file'));
        $this->assertNull($this->Helper->script('my-file2', ['block' => true]));
        $this->assertNull($this->Helper->js('my-file4'));
        $this->assertNull($this->Helper->js('my-file5', ['block' => true]));

        $expected = ['script' => ['src' => '/js/my-file3.js']];
        $this->assertHtml($expected, $this->Helper->script('my-file3', ['block' => false]));

        $expected = ['script' => ['src' => '/js/my-file6.js']];
        $this->assertHtml($expected, $this->Helper->js('my-file6', ['block' => false]));
    }

    /**
     * Test for `scriptBlock()` method
     * @test
     */
    public function testScriptBlock()
    {
        $code = 'window.foo = 2;';

        //By default, `block` is `true`
        $this->assertNull($this->Helper->scriptBlock($code, ['safe' => false]));
        $this->assertNull($this->Helper->scriptBlock($code, ['block' => true, 'safe' => false]));

        $expected = ['<script', $code, '/script'];
        $this->assertHtml($expected, $this->Helper->scriptBlock($code, ['block' => false, 'safe' => false]));
    }

    /**
     * Test for `scriptStart()` and `scriptEnd()` methods
     * @test
     */
    public function testScriptStartAndScriptEnd()
    {
        //By default, `block` is `true`
        $this->assertNull($this->Helper->scriptStart(['safe' => false]));
        echo 'this is some javascript';
        $this->assertNull($this->Helper->scriptEnd());
        $this->assertNull($this->Helper->scriptStart(['block' => true, 'safe' => false]));

        echo 'this is some javascript';
        $this->assertNull($this->Helper->scriptEnd());
        $this->assertNull($this->Helper->scriptStart(['block' => false, 'safe' => false]));

        echo 'this is some javascript';
        $expected = ['<script', 'this is some javascript', '/script'];
        $this->assertHtml($expected, $this->Helper->scriptEnd());
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
        $this->assertHtml($expected, $this->Helper->shareaholic('my-app-id'));
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
        $this->assertHtml($expected, $this->Helper->tag('h3'));
        $this->assertHtml($expected, $this->Helper->tag('h3', null));
        $this->assertHtml($expected, $this->Helper->tag('h3', ''));

        $expected = [
            'h3' => ['class' => $class],
            'i' => ['class' => 'fas fa-home'],
            ' ',
            '/i',
            ' ',
            $text,
            '/h3',
        ];
        $this->assertHtml($expected, $this->Helper->tag('h3', $text, ['class' => $class, 'icon' => 'home']));

        // `tooltip` value rewrites `title` value
        $expected = ['h3' => ['data-toggle' => 'tooltip', 'title' => 'my tooltip'], $text, '/h3'];
        $this->assertHtml($expected, $this->Helper->tag('h3', $text, ['title' => 'my title', 'tooltip' => 'my tooltip']));

        $expected = [
            'h3' => ['class' => $class],
            $text,
            ' ',
            'i' => ['class' => 'fas fa-home'],
            ' ',
            '/i',
            '/h3',
        ];
        $this->assertHtml($expected, $this->Helper->tag('h3', $text, compact('class') + ['icon' => 'home', 'icon-align' => 'right']));
    }

    /**
     * Tests for `viewport()` method
     * @test
     */
    public function testViewport()
    {
        //By default, `block` is `true`
        $this->assertNull($this->Helper->viewport());
        $this->assertNull($this->Helper->viewport(['block' => true]));

        $expected = ['meta' => [
            'option' => 'value',
            'name' => 'viewport',
            'content' => 'initial-scale=1, shrink-to-fit=no, width=device-width',
        ]];
        $this->assertHtml($expected, $this->Helper->viewport(['block' => false, 'option' => 'value']));
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
        $this->assertHtml($expected, $this->Helper->youtube($id));

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
        $this->assertHtml($expected, $this->Helper->youtube($id, ['ratio' => '4by3']));

        $expected = ['iframe' => ['allowfullscreen' => 'allowfullscreen', 'height' => '480', 'width' => '640', 'src' => $url]];
        $this->assertHtml($expected, $this->Helper->youtube($id, ['ratio' => false]));

        $expected = [
            'div' => ['class' => 'embed-responsive embed-responsive-16by9'],
            'iframe' => [
                'allowfullscreen' => 'allowfullscreen',
                'height' => '100',
                'width' => '200',
                'class' => 'embed-responsive-item my-class',
                'src' => $url,
            ],
            '/iframe',
            '/div',
        ];
        $this->assertHtml($expected, $this->Helper->youtube($id, ['class' => 'my-class', 'height' => 100, 'width' => 200]));
    }
}
