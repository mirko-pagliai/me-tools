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

/**
 * HtmlHelperTest class
 * @property \MeTools\View\Helper\BootstrapHtmlHelper $Helper
 */
class BootstrapHtmlHelperTest extends HelperTestCase
{
    /**
     * Tests for `__call()` method
     * @test
     * @uses \MeTools\View\Helper\BootstrapHtmlHelper::__call()
     */
    public function testCall(): void
    {
        //The `h3()` method should not exist, otherwise the `__call()` method
        //  will not be called
        $this->assertFalse(method_exists($this->Helper, 'h3'));

        $expected = '<h3 class="my-class">my h3 text</h3>';
        $result = $this->Helper->h3('my h3 text', ['class' => 'my-class']);
        $this->assertSame($expected, $result);

        $expected = '<h3 class="my-class"><i class="fas fa-home"> </i> my h3 text</h3>';
        $result = $this->Helper->h3('my h3 text', ['class' => 'my-class', 'icon' => 'home']);
        $this->assertSame($expected, $result);

        $expected = '<h3 class="my-class">my h3 text <i class="fas fa-home"> </i></h3>';
        $result = $this->Helper->h3('my h3 text', ['class' => 'my-class', 'icon' => 'home', 'icon-align' => 'right']);
        $this->assertSame($expected, $result);

        //With a no existing method
        $this->expectException(ErrorException::class);
        $this->expectExceptionMessage('Method `' . get_class($this->Helper) . '::noExistingMethod()` does not exist');
        /** @noinspection PhpUndefinedMethodInspection */
        $this->Helper->noExistingMethod(null, null, null);
    }

    /**
     * Test for `button()` method
     * @test
     * @uses \MeTools\View\Helper\BootstrapHtmlHelper::button()
     */
    public function testButton(): void
    {
        $expected = '<a href="https://link" class="btn btn-light" role="button" title="my-title">My title</a>';
        $result = $this->Helper->button('My title', 'https://link', ['title' => 'my-title']);
        $this->assertSame($expected, $result);

        $expected = '<a href="#" class="btn btn-light" role="button" title="My title">My title <i class="fas fa-home"> </i></a>';
        $result = $this->Helper->button('My title', '#', ['icon' => 'home', 'icon-align' => 'right']);
        $this->assertSame($expected, $result);

        //Code on text
        $expected = '<a href="#" class="btn btn-light" role="button" title="Code"><u>Code</u> </a>';
        $result = $this->Helper->button('<u>Code</u> ', '#');
        $this->assertSame($expected, $result);

        //Code on custom title
        $expected = '<a href="#" class="btn btn-light" role="button" title="Code">My title</a>';
        $result = $this->Helper->button('My title', '#', ['title' => '<u>Code</u>']);
        $this->assertSame($expected, $result);

        $expected = '<a href="/" class="btn btn-light" role="button" title="/">/</a>';
        $result = $this->Helper->button('/');
        $this->assertSame($expected, $result);

        //With a button class
        $expected = '<a href="https://link" class="btn btn-success" role="button" title="my-title">My title</a>';
        $result = $this->Helper->button('My title', 'https://link', ['class' => 'btn-success', 'title' => 'my-title']);
        $this->assertSame($expected, $result);

        $this->loadPlugins(['TestPlugin' => []]);
        $expected = '<a href="/pages" class="btn btn-light" role="button"></a>';
        $result = $this->Helper->button(['controller' => 'Pages', 'plugin' => 'TestPlugin']);
        $this->assertSame($expected, $result);
    }

    /**
     * Test for `badge()` method
     * @test
     * @uses \MeTools\View\Helper\BootstrapHtmlHelper::badge()
     */
    public function testBadge(): void
    {
        $expected = '<span class="badge my-class"><i class="fas fa-home"> </i> 1</span>';
        $result = $this->Helper->badge('1', ['class' => 'my-class', 'icon' => 'home']);
        $this->assertSame($expected, $result);
    }

    /**
     * Test for `iframe()` method
     * @uses \MeTools\View\Helper\BootstrapHtmlHelper::iframe()
     * @test
     */
    public function testIframe(): void
    {
        $url = 'https://frame';

        $expected = '<iframe src="https://frame"></iframe>';
        $result = $this->Helper->iframe($url);
        $this->assertSame($expected, $result);

        //No existing ratio
        $result = $this->Helper->iframe($url, ['ratio' => 'noExisting']);
        $this->assertSame($expected, $result);

        //The `src` option doesn't overwrite
        $result = $this->Helper->iframe($url, ['src' => 'https://anotherframe']);
        $this->assertSame($expected, $result);

        $expected = '<iframe class="my-class" src="https://frame"></iframe>';
        $result = $this->Helper->iframe($url, ['class' => 'my-class']);
        $this->assertSame($expected, $result);

        foreach (['1x1', '4x3', '16x9', '21x9'] as $ratio) {
            $expected = '<div class="ratio ratio-' . $ratio . '"><iframe src="https://frame"></iframe></div>';
            $result = $this->Helper->iframe($url, compact('ratio'));
            $this->assertSame($expected, $result);
        }

        $this->loadPlugins(['TestPlugin' => []]);
        $expected = '<iframe src="/pages"></iframe>';
        $result = $this->Helper->iframe(['controller' => 'Pages', 'plugin' => 'TestPlugin']);
        $this->assertSame($expected, $result);
    }

    /**
     * Test for `image()` and `img()` methods
     * @test
     * @uses \MeTools\View\Helper\BootstrapHtmlHelper::image()
     * @uses \MeTools\View\Helper\BootstrapHtmlHelper::img()
     */
    public function testImage(): void
    {
        $expected = '<img src="/img/image.gif" alt="image.gif" class="img-fluid my-class"/>';
        $result = $this->Helper->image('image.gif', ['class' => 'my-class']);
        $this->assertSame($expected, $result);

        //With `img()` method
        $result = $this->Helper->img('image.gif', ['class' => 'my-class']);
        $this->assertSame($expected, $result);

        $expected = '<img src="/img/image.gif" alt="my-alt" class="img-fluid"/>';
        $result = $this->Helper->image('image.gif', ['alt' => 'my-alt']);
        $this->assertSame($expected, $result);

        $expected = '<img src="http://url/image.gif" alt="image.gif" class="img-fluid"/>';
        $result = $this->Helper->image('http://url/image.gif');
        $this->assertSame($expected, $result);

        $this->loadPlugins(['TestPlugin' => []]);
        $expected = '<img src="/pages" alt="pages" class="img-fluid"/>';
        $result = $this->Helper->image(['controller' => 'Pages', 'plugin' => 'TestPlugin']);
        $this->assertSame($expected, $result);

        $expected = '<a href="/pages"><img src="/img/image.gif" alt="image.gif" class="img-fluid"/></a>';
        $result = $this->Helper->image('image.gif', ['url' => ['controller' => 'Pages', 'plugin' => 'TestPlugin']]);
        $this->assertSame($expected, $result);
    }

    /**
     * Test for `li()` method
     * @test
     * @uses \MeTools\View\Helper\BootstrapHtmlHelper::li()
     */
    public function testLi(): void
    {
        $expected = '<li><i class="fas fa-home"> </i> My li</li>';
        $result = $this->Helper->li('My li', ['icon' => 'home']);
        $this->assertSame($expected, $result);

        $expected = '<li class="my-class"><i class="fas fa-home"> </i> first-value</li>' . PHP_EOL .
            '<li class="my-class"><i class="fas fa-home"> </i> second-value</li>';
        $result = $this->Helper->li(['first-value', 'second-value'], ['class' => 'my-class', 'icon' => 'home']);
        $this->assertSame($expected, $result);
    }

    /**
     * Test for `link()` method
     * @test
     * @uses \MeTools\View\Helper\BootstrapHtmlHelper::link()
     */
    public function testLink(): void
    {
        $expected = '<a href="https://link" title="my-title">My title</a>';
        $result = $this->Helper->link('My title', 'https://link', ['title' => 'my-title']);
        $this->assertSame($expected, $result);

        $expected = '<a href="#" title="My title">My title <i class="fas fa-home"> </i></a>';
        $result = $this->Helper->link('My title', '#', ['icon' => 'home', 'icon-align' => 'right']);
        $this->assertSame($expected, $result);

        //Code on text
        $expected = '<a href="#" title="Code"><u>Code</u> </a>';
        $result = $this->Helper->link('<u>Code</u> ', '#');
        $this->assertSame($expected, $result);

        //Code on custom title
        $expected = '<a href="#" title="Code">My title</a>';
        $result = $this->Helper->link('My title', '#', ['title' => '<u>Code</u>']);
        $this->assertSame($expected, $result);

        $this->assertSame('<a href="/" title="/">/</a>', $this->Helper->link('/'));

        $this->loadPlugins(['TestPlugin' => []]);
        $expected = '<a href="/pages"></a>';
        $result = $this->Helper->link(['controller' => 'Pages', 'plugin' => 'TestPlugin']);
        $this->assertSame($expected, $result);
    }

    /**
     * Test for `meta()` method
     * @test
     * @uses \MeTools\View\Helper\BootstrapHtmlHelper::meta()
     */
    public function testMeta(): void
    {
        $this->assertNull($this->Helper->meta('viewport', 'width=device-width'));

        $expected = '<meta name="viewport" content="width=device-width"/>';
        $result = $this->Helper->meta('viewport', 'width=device-width', ['block' => false]);
        $this->assertSame($expected, $result);
    }

    /**
     * Test for `ol()` and `ul()` methods (and consequently `nestedList ()`)
     * @test
     * @uses \MeTools\View\Helper\BootstrapHtmlHelper::nestedList()
     * @uses \MeTools\View\Helper\BootstrapHtmlHelper::ol()
     * @uses \MeTools\View\Helper\BootstrapHtmlHelper::ul()
     */
    public function testOlAndUl(): void
    {
        $expected = '<ul class="fa-ul parent-class"><li class="li-class"><i class="fas fa-home fa-li"> </i> First</li><li class="li-class"><i class="fas fa-home fa-li"> </i> Second</li></ul>';
        $result = $this->Helper->ul(['First', 'Second'], ['class' => 'parent-class'], ['class' => 'li-class', 'icon' => 'home']);
        $this->assertSame($expected, $result);

        $expected = str_replace(['<ul', '</ul'], ['<ol', '</ol'], $expected);
        $result = $this->Helper->ol(['First', 'Second'], ['class' => 'parent-class'], ['class' => 'li-class', 'icon' => 'home']);
        $this->assertSame($expected, $result);

        $expected = '<ul class="fa-ul"><li><i class="fas fa-home fa-li"> </i> First</li><li><i class="fas fa-home fa-li"> </i> Second</li></ul>';
        $result = $this->Helper->ul(['First', 'Second'], ['icon' => 'home']);
        $this->assertSame($expected, $result);
    }

    /**
     * Test for `tag()` method
     * @test
     * @uses \MeTools\View\Helper\BootstrapHtmlHelper::tag()
     */
    public function testTag(): void
    {
        $this->assertSame('<h3>My header</h3>', $this->Helper->tag('h3', 'My header'));

        $expected = '<h3 class="my-class"><i class="fas fa-home"> </i> My text</h3>';
        $result = $this->Helper->tag('h3', 'My text', ['class' => 'my-class', 'icon' => 'home']);
        $this->assertSame($expected, $result);

        $expected = '<h3 class="my-class">My text <i class="fas fa-home"> </i></h3>';
        $result = $this->Helper->tag('h3', 'My text', ['class' => 'my-class', 'icon' => 'home', 'icon-align' => 'right']);
        $this->assertSame($expected, $result);
    }

    /**
     * Test for `viewport()` method
     * @test
     * @uses \MeTools\View\Helper\BootstrapHtmlHelper::viewport()
     */
    public function testViewport(): void
    {
        $expected = '<meta name="viewport" content="initial-scale=1, width=device-width"/>';
        $result = $this->Helper->viewport([], ['block' => false]);
        $this->assertSame($expected, $result);

        $expected = '<meta title="my title" name="viewport" content="width=500, initial-scale=1"/>';
        $result = $this->Helper->viewport(['width' => 500], ['block' => false, 'title' => 'my title']);
        $this->assertSame($expected, $result);
    }

    /**
     * Tests for `youtube()` method
     * @uses \MeTools\View\Helper\BootstrapHtmlHelper::youtube()
     * @test
     */
    public function testYoutube(): void
    {
        $expected = '<div class="ratio ratio-16x9"><iframe allowfullscreen="allowfullscreen" height="480" src="https://www.youtube.com/embed/my-id" width="640"></iframe></div>';
        $result = $this->Helper->youtube('my-id');
        $this->assertSame($expected, $result);

        $expected = '<div class="ratio ratio-4x3"><iframe allowfullscreen="allowfullscreen" height="480" src="https://www.youtube.com/embed/my-id" width="640"></iframe></div>';
        $result = $this->Helper->youtube('my-id', ['ratio' => '4x3']);
        $this->assertSame($expected, $result);

        $expected = '<iframe allowfullscreen="allowfullscreen" height="480" src="https://www.youtube.com/embed/my-id" width="640"></iframe>';
        $result = $this->Helper->youtube('my-id', ['ratio' => false]);
        $this->assertSame($expected, $result);

        $expected = '<div class="ratio ratio-16x9"><iframe allowfullscreen="allowfullscreen" class="my-class" height="100" src="https://www.youtube.com/embed/my-id" width="200"></iframe></div>';
        $result = $this->Helper->youtube('my-id', ['class' => 'my-class', 'height' => 100, 'width' => 200]);
        $this->assertSame($expected, $result);
    }
}
