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

use ErrorException;
use MeTools\TestSuite\HelperTestCase;
use MeTools\View\Helper\HtmlHelper;
use PHPUnit\Framework\Error\Deprecated;

/**
 * HtmlHelperTest class
 * @property \MeTools\View\Helper\HtmlHelper $Helper
 */
class HtmlHelperTest extends HelperTestCase
{
    /**
     * Tests for `__call()` method
     * @deprecated
     * @test
     */
    public function testCall(): void
    {
        $text = 'my h3 text';
        $class = 'my-class';

        //The `h3()` method should not exist, otherwise the `__call()` method
        //  will not be called
        $this->assertFalse(method_exists($this->Helper, 'h3'));

        $expected = $this->Helper->tag('h3', $text, compact('class'));
        /** @phpstan-ignore-next-line */
        $this->assertEquals($expected, $this->Helper->h3($text, compact('class')));

        $expected = $this->Helper->tag('h3', $text, compact('class') + ['icon' => 'home']);
        /** @phpstan-ignore-next-line */
        $this->assertEquals($expected, $this->Helper->h3($text, ['class' => $class, 'icon' => 'home']));

        $expected = $this->Helper->tag('h3', $text, compact('class') + ['icon' => 'home', 'icon-align' => 'right']);
        /** @phpstan-ignore-next-line */
        $result = $this->Helper->h3($text, compact('class') + ['icon' => 'home', 'icon-align' => 'right']);
        $this->assertEquals($expected, $result);

        //With a no existing method
        $this->expectException(ErrorException::class);
        $this->expectExceptionMessage('Method `' . HtmlHelper::class . '::noExistingMethod()` does not exist');
        /** @phpstan-ignore-next-line */
        $this->Helper->noExistingMethod(null, null, null);
    }

    /**
     * Tests for `badge()` method
     * @test
     */
    public function testBadge(): void
    {
        $expected = ['span' => ['class' => 'badge my-class'], 'My text', '/span'];
        $this->assertHtml($expected, $this->Helper->badge('My text', ['class' => 'my-class']));
    }

    /**
     * Test for `button()` method
     * @deprecated
     * @test
     */
    public function testButton(): void
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
            '/button',
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

        $this->assertSame('<button class="btn btn-light" role="button" title=""></button>', $this->Helper->button());
    }

    /**
     * Test for `button()` method, with buttons as links
     * @deprecated
     * @test
     */
    public function testButtonAsLink(): void
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
            '/a',
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
    public function testCss(): void
    {
        //By default, `block` is `true`
        $this->assertNull($this->Helper->css('my-file'));
        $this->assertNull($this->Helper->css('my-file2', ['block' => true]));

        $expected = ['link' => ['rel' => 'stylesheet', 'href' => '/css/my-file3.css']];
        $this->assertHtml($expected, $this->Helper->css('my-file3', ['block' => false]) ?: '');

        $expected = ['link' => ['rel' => 'alternate', 'href' => '/css/my-file4.css']];
        $result = $this->Helper->css('my-file4', ['block' => false, 'rel' => 'alternate']);
        $this->assertHtml($expected, $result ?: '');
    }

    /**
     * Test for `cssBlock()` method
     * @deprecated
     * @test
     */
    public function testCssBlock(): void
    {
        $current = error_reporting(E_ALL & ~E_USER_DEPRECATED);

        $css = 'body { color: red; }';

        //By default, `block` is `true`
        $this->assertNull($this->Helper->cssBlock($css));
        $this->assertNull($this->Helper->cssBlock($css, ['block' => true]));

        $expected = ['style' => true, $css, '/style'];
        $this->assertHtml($expected, $this->Helper->cssBlock($css, ['block' => false]) ?: '');

        error_reporting($current);

        $this->expectDeprecation();
        $this->Helper->cssBlock($css);
    }

    /**
     * Test for `cssStart()` and `cssEnd()` methods
     * @deprecated
     * @test
     */
    public function testCssStartAndCssEnd(): void
    {
        $current = error_reporting(E_ALL & ~E_USER_DEPRECATED);

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
        $this->assertHtml($expected, $result ?: '');

        error_reporting($current);

        $this->assertException(fn() => $this->Helper->cssStart(), Deprecated::class);
        $this->assertException(fn() => $this->Helper->cssEnd(), Deprecated::class);
    }

    /**
     * Tests for `heading()` method
     * @deprecated
     * @test
     */
    public function testHeading(): void
    {
        $current = error_reporting(E_ALL & ~E_USER_DEPRECATED);

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

        error_reporting($current);

        $this->expectDeprecation();
        $this->Helper->heading($text);
    }

    /**
     * Test for `hr()` method
     * @deprecated
     * @test
     */
    public function testHr(): void
    {
        $current = error_reporting(E_ALL & ~E_USER_DEPRECATED);

        $expected = $this->Helper->tag('hr', null, ['class' => 'my-hr-class']);
        $this->assertEquals($expected, $this->Helper->hr(['class' => 'my-hr-class']));

        error_reporting($current);

        $this->expectDeprecation();
        $this->Helper->hr();
    }

    /**
     * Test for `iframe()` method
     * @test
     */
    public function testIframe(): void
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

        $this->loadPlugins(['TestPlugin' => []]);
        $this->assertSame('<iframe src="/pages"></iframe>', $this->Helper->iframe(['controller' => 'Pages', 'plugin' => 'TestPlugin']));
    }

    /**
     * Test for `image()` and `img()` methods
     * @deprecated
     * @test
     */
    public function testImage(): void
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

        $this->loadPlugins(['TestPlugin' => []]);
        $this->assertNotEmpty($this->Helper->image(['controller' => 'Pages', 'plugin' => 'TestPlugin']));
    }

    /**
     * Tests for `label()` method
     * @test
     */
    public function testLabel(): void
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
    public function testLi(): void
    {
        $expected = [
            'li' => true,
            'i' => ['class' => 'fas fa-home'],
            ' ',
            '/i',
            ' ',
            'My text',
            '/li',
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
     * @deprecated
     * @test
     */
    public function testLink(): void
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
            '/a',
        ];
        $this->assertHtml($expected, $this->Helper->link($title, '#', ['tooltip' => 'my tooltip', 'tooltip-align' => 'bottom']));

        $expected = [
            'a' => ['href' => '#', 'title' => '&quot; &#039;', 'data-toggle' => 'tooltip'],
            $title,
            '/a',
        ];
        $this->assertHtml($expected, $this->Helper->link($title, '#', ['tooltip' => '" \'']));

        $expected = [
            'a' => ['href' => '#', 'title' => 'Code', 'data-toggle' => 'tooltip'],
            $title,
            '/a',
        ];
        $this->assertHtml($expected, $this->Helper->link($title, '#', ['tooltip' => '<u>Code</u>']));

        $this->assertSame('<a href="/" title="/">/</a>', $this->Helper->link());

        $this->loadPlugins(['TestPlugin' => []]);
        $this->assertSame('<a href="/pages" title="/pages"></a>', $this->Helper->link(['controller' => 'Pages', 'plugin' => 'TestPlugin']));
    }

    /**
     * Test for `meta()` method
     * @test
     */
    public function testMeta(): void
    {
        //By default, `block` is `true`
        $this->assertNull($this->Helper->meta('viewport', 'width=device-width'));
        $this->assertNull($this->Helper->meta('viewport', 'width=device-width', ['block' => true]));

        $expected = ['meta' => ['name' => 'viewport', 'content' => 'width=device-width']];
        $this->assertHtml($expected, $this->Helper->meta('viewport', 'width=device-width', ['block' => false]) ?: '');
    }

    /**
     * Test for `nestedList`, `ol()` and `ul()` methods
     * @deprecated
     * @test
     */
    public function testNestedListAndOlAndUl(): void
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
    public function testPara(): void
    {
        $expected = ['p' => true, '/p'];
        $this->assertHtml($expected, $this->Helper->para());
        $this->assertHtml($expected, $this->Helper->para(''));
        $this->assertHtml($expected, $this->Helper->para('', ''));

        $expected = ['p' => ['class' => 'my-class']];
        $this->assertHtml($expected, $this->Helper->para('my-class'));
        $this->assertHtml($expected, $this->Helper->para('my-class', ''));

        $expected = ['p' => true, ' ', '/p'];
        $this->assertHtml($expected, $this->Helper->para('', ' '));

        $expected = ['p' => ['data-toggle' => 'tooltip', 'title' => 'my tooltip'], 'my text', '/p'];
        $result = $this->Helper->para('', 'my text', ['tooltip' => 'my tooltip']);
        $this->assertHtml($expected, $result);

        $expected = [
            'p' => ['class' => 'my-class', 'id' => 'my-id'],
            'i' => ['class' => 'fas fa-home'],
            ' ',
            '/i',
            ' ',
            'text',
            '/p',
        ];
        $this->assertHtml($expected, $this->Helper->para('my-class', 'text', ['id' => 'my-id', 'icon' => 'home']));
    }

    /**
     * Test for `script()` method
     * @test
     */
    public function testScript(): void
    {
        //By default, `block` is `true`
        $this->assertNull($this->Helper->script('my-file'));
        $this->assertNull($this->Helper->script('my-file2', ['block' => true]));

        $expected = ['script' => ['src' => '/js/my-file3.js']];
        $this->assertHtml($expected, $this->Helper->script('my-file3', ['block' => false]) ?: '');
    }

    /**
     * Test for `scriptBlock()` method
     * @deprecated
     * @test
     */
    public function testScriptBlock(): void
    {
        $code = 'window.foo = 2;';

        //By default, `block` is `true`
        $this->assertNull($this->Helper->scriptBlock($code, ['safe' => false]));
        $this->assertNull($this->Helper->scriptBlock($code, ['block' => true, 'safe' => false]));

        $expected = ['<script', $code, '/script'];
        $this->assertHtml($expected, $this->Helper->scriptBlock($code, ['block' => false, 'safe' => false]) ?: '');
    }

    /**
     * Test for `scriptStart()` and `scriptEnd()` methods
     * @deprecated
     * @test
     */
    public function testScriptStartAndScriptEnd(): void
    {
        //By default, `block` is `true`
        $this->Helper->scriptStart(['safe' => false]);
        echo 'this is some javascript';
        $this->assertNull($this->Helper->scriptEnd());
        $this->Helper->scriptStart(['block' => true, 'safe' => false]);

        echo 'this is some javascript';
        $this->assertNull($this->Helper->scriptEnd());
        $this->Helper->scriptStart(['block' => false, 'safe' => false]);

        echo 'this is some javascript';
        $expected = ['<script', 'this is some javascript', '/script'];
        $this->assertHtml($expected, $this->Helper->scriptEnd() ?: '');
    }

    /**
     * Tests for `shareaholic()` method
     * @test
     */
    public function testShareaholic(): void
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
     * @deprecated
     * @test
     */
    public function testTag(): void
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
     * @deprecated
     * @test
     */
    public function testViewport(): void
    {
        //By default, `block` is `true`
        $this->assertNull($this->Helper->viewport());
        $this->assertNull($this->Helper->viewport(['block' => true]));

        $expected = ['meta' => [
            'option' => 'value',
            'name' => 'viewport',
            'content' => 'initial-scale=1, shrink-to-fit=no, width=device-width',
        ]];
        $this->assertHtml($expected, $this->Helper->viewport(['block' => false, 'option' => 'value']) ?: '');
    }

    /**
     * Tests for `youtube()` method
     * @test
     */
    public function testYoutube(): void
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
