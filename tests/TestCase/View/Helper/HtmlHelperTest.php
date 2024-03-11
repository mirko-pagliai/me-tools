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
    protected HtmlHelper $Helper;

    /**
     * @inheritDoc
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->Helper ??= new HtmlHelper(new View());
    }

    /**
     * @test
     * @uses \MeTools\View\Helper\HtmlHelper::__call()
     */
    public function testCall(): void
    {
        //The `h3()` method should not exist, otherwise the `__call()` method will not be called
        $this->assertFalse(method_exists($this->Helper, 'h3'));

        $expected = '<h3 class="my-class">my h3 text</h3>';
        $result = $this->Helper->h3('my h3 text', ['class' => 'my-class']);
        $this->assertSame($expected, $result);

        //With named params
        $result = $this->Helper->h3(text: 'my h3 text', options: ['class' => 'my-class']);
        $this->assertSame($expected, $result);

        $expected = '<h3 class="my-class"><i class="fa fa-home"> </i> my h3 text</h3>';
        $result = $this->Helper->h3('my h3 text', ['class' => 'my-class', 'icon' => 'fa fa-home']);
        $this->assertSame($expected, $result);

        $expected = '<h3 class="my-class">my h3 text <i class="fa fa-home"> </i></h3>';
        $result = $this->Helper->h3('my h3 text', ['class' => 'my-class', 'icon' => 'fa fa-home', 'icon-align' => 'right']);
        $this->assertSame($expected, $result);

        //With a no existing method
        $this->expectExceptionMessage('Method `' . get_class($this->Helper) . '::noExistingMethod()` does not exist');
        /** @noinspection PhpUndefinedMethodInspection */
        $this->Helper->noExistingMethod(null, null, null);
    }

    /**
     * @test
     * @uses \MeTools\View\Helper\HtmlHelper::button()
     */
    public function testButton(): void
    {
        $expected = '<a href="https://link" title="my-title" role="button" class="btn btn-primary">My title</a>';
        $result = $this->Helper->button('My title', 'https://link', ['title' => 'my-title']);
        $this->assertSame($expected, $result);

        $expected = '<a href="#" role="button" class="btn btn-primary" title="My title">My title <i class="fa fa-home"> </i></a>';
        $result = $this->Helper->button('My title', '#', ['icon' => 'fa fa-home', 'icon-align' => 'right']);
        $this->assertSame($expected, $result);

        //Code on text
        $expected = '<a href="#" role="button" class="btn btn-primary" title="Code"><u>Code</u> </a>';
        $result = $this->Helper->button('<u>Code</u> ', '#');
        $this->assertSame($expected, $result);

        //Code on custom title
        $expected = '<a href="#" title="Code" role="button" class="btn btn-primary">My title</a>';
        $result = $this->Helper->button('My title', '#', ['title' => '<u>Code</u>']);
        $this->assertSame($expected, $result);

        $expected = '<a href="/" role="button" class="btn btn-primary" title="/">/</a>';
        $result = $this->Helper->button('/');
        $this->assertSame($expected, $result);

        //With a button class
        $expected = '<a href="https://link" class="btn btn-danger" title="my-title" role="button">My title</a>';
        $result = $this->Helper->button('My title', 'https://link', ['class' => 'btn-danger', 'title' => 'my-title']);
        $this->assertSame($expected, $result);

        $this->loadPlugins(['AnotherTestPlugin' => []]);
        $expected = '<a href="/pages" role="button" class="btn btn-primary"></a>';
        $result = $this->Helper->button(['controller' => 'Pages', 'plugin' => 'AnotherTestPlugin']);
        $this->assertSame($expected, $result);
    }

    /**
     * @test
     * @uses \MeTools\View\Helper\HtmlHelper::badge()
     */
    public function testBadge(): void
    {
        $expected = '<span class="my-class badge"><i class="fa fa-home"> </i> 1</span>';
        $result = $this->Helper->badge('1', ['class' => 'my-class', 'icon' => 'fa fa-home']);
        $this->assertSame($expected, $result);
    }

    /**
     * @uses \MeTools\View\Helper\HtmlHelper::iframe()
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

        $this->loadPlugins(['AnotherTestPlugin' => []]);
        $expected = '<iframe src="/pages"></iframe>';
        $result = $this->Helper->iframe(['controller' => 'Pages', 'plugin' => 'AnotherTestPlugin']);
        $this->assertSame($expected, $result);
    }

    /**
     * @test
     * @uses \MeTools\View\Helper\HtmlHelper::image()
     */
    public function testImage(): void
    {
        $expected = '<img src="/img/image.gif" class="my-class img-fluid" alt="image.gif">';
        $result = $this->Helper->image('image.gif', ['class' => 'my-class']);
        $this->assertSame($expected, $result);

        $expected = '<img src="/img/image.gif" alt="my-alt" class="img-fluid">';
        $result = $this->Helper->image('image.gif', ['alt' => 'my-alt']);
        $this->assertSame($expected, $result);

        $expected = '<img src="http://url/image.gif" alt="image.gif" class="img-fluid">';
        $result = $this->Helper->image('http://url/image.gif');
        $this->assertSame($expected, $result);

        $this->loadPlugins(['AnotherTestPlugin' => []]);
        $expected = '<img src="/pages" alt="pages" class="img-fluid">';
        $result = $this->Helper->image(['controller' => 'Pages', 'plugin' => 'AnotherTestPlugin']);
        $this->assertSame($expected, $result);

        $expected = '<a href="/pages"><img src="/img/image.gif" alt="image.gif" class="img-fluid"></a>';
        $result = $this->Helper->image('image.gif', ['url' => ['controller' => 'Pages', 'plugin' => 'AnotherTestPlugin']]);
        $this->assertSame($expected, $result);
    }

    /**
     * @test
     * @uses \MeTools\View\Helper\HtmlHelper::li()
     */
    public function testLi(): void
    {
        $expected = '<li><i class="fa fa-home"> </i> My li</li>';
        $result = $this->Helper->li('My li', ['icon' => 'fa fa-home']);
        $this->assertSame($expected, $result);

        $expected = '<li class="my-class"><i class="fa fa-home"> </i> first-value</li>' . PHP_EOL .
            '<li class="my-class"><i class="fa fa-home"> </i> second-value</li>';
        $result = $this->Helper->li(['first-value', 'second-value'], ['class' => 'my-class', 'icon' => 'fa fa-home']);
        $this->assertSame($expected, $result);
    }

    /**
     * @test
     * @uses \MeTools\View\Helper\HtmlHelper::link()
     */
    public function testLink(): void
    {
        $expected = '<a href="https://link" title="my-title">My title</a>';
        $result = $this->Helper->link('My title', 'https://link', ['title' => 'my-title']);
        $this->assertSame($expected, $result);

        $expected = '<a href="#" class="text-decoration-none" title="My title">My title <i class="fa fa-home"> </i></a>';
        $result = $this->Helper->link('My title', '#', ['icon' => 'fa fa-home', 'icon-align' => 'right']);
        $this->assertSame($expected, $result);

        //Code on text
        $expected = '<a href="#" title="Code"><u>Code</u> </a>';
        $result = $this->Helper->link('<u>Code</u> ', '#');
        $this->assertSame($expected, $result);

        //Code on custom title
        $expected = '<a href="#" title="Code">My title</a>';
        $result = $this->Helper->link('My title', '#', ['title' => '<u>Code</u>']);
        $this->assertSame($expected, $result);

        //Icon and `text-decoration-underline` (so class `text-decoration-none` will not be applied)
        $expected = '<a href="#" class="text-decoration-underline" title="My title"><i class="fa fa-home"> </i> My title</a>';
        $result = $this->Helper->link('My title', '#', ['class' => 'text-decoration-underline', 'icon' => 'fa fa-home']);
        $this->assertSame($expected, $result);

        $this->assertSame('<a href="/" title="/">/</a>', $this->Helper->link('/'));

        $this->loadPlugins(['AnotherTestPlugin' => []]);
        $expected = '<a href="/pages"></a>';
        $result = $this->Helper->link(['controller' => 'Pages', 'plugin' => 'AnotherTestPlugin']);
        $this->assertSame($expected, $result);
    }

    /**
     * @test
     * @uses \MeTools\View\Helper\HtmlHelper::meta()
     */
    public function testMeta(): void
    {
        $this->assertNull($this->Helper->meta('viewport', 'width=device-width'));

        $expected = '<meta name="viewport" content="width=device-width">';
        $result = $this->Helper->meta('viewport', 'width=device-width', ['block' => false]);
        $this->assertSame($expected, $result);
    }

    /**
     * @test
     * @uses \MeTools\View\Helper\HtmlHelper::nestedList()
     * @uses \MeTools\View\Helper\HtmlHelper::ol()
     * @uses \MeTools\View\Helper\HtmlHelper::ul()
     */
    public function testOlAndUl(): void
    {
        $expected = '<ul class="parent-class fa-ul">' .
            '<li class="li-class"><i class="fa fa-home fa-li"> </i> First</li>' .
            '<li class="li-class"><i class="fa fa-home fa-li"> </i> Second</li>' .
            '</ul>';
        $result = $this->Helper->ul(['First', 'Second'], ['class' => 'parent-class'], ['class' => 'li-class', 'icon' => 'fa fa-home']);
        $this->assertSame($expected, $result);

        $expected = str_replace(['<ul', '</ul'], ['<ol', '</ol'], $expected);
        $result = $this->Helper->ol(['First', 'Second'], ['class' => 'parent-class'], ['class' => 'li-class', 'icon' => 'fa fa-home']);
        $this->assertSame($expected, $result);

        $expected = '<ul class="fa-ul">' .
            '<li><i class="fa fa-home fa-li"> </i> First</li>' .
            '<li><i class="fa fa-home fa-li"> </i> Second</li>' .
            '</ul>';
        $result = $this->Helper->ul(['First', 'Second'], ['icon' => 'fa fa-home']);
        $this->assertSame($expected, $result);
    }

    /**
     * @test
     * @uses \MeTools\View\Helper\HtmlHelper::para()
     */
    public function testPara(): void
    {
        $expected = '<p><i class="fa fa-home"> </i> Test</p>';
        $this->assertSame($expected, $this->Helper->para('', 'Test', ['icon' => 'fa fa-home']));
    }

    /**
     * @test
     * @uses \MeTools\View\Helper\HtmlHelper::tag()
     */
    public function testTag(): void
    {
        $this->assertSame('<h3>My header</h3>', $this->Helper->tag('h3', 'My header'));

        $expected = '<h3 class="my-class"><i class="fa fa-home"> </i> My text</h3>';
        $result = $this->Helper->tag('h3', 'My text', ['class' => 'my-class', 'icon' => 'fa fa-home']);
        $this->assertSame($expected, $result);

        $expected = '<h3 class="my-class">My text <i class="fa fa-home"> </i></h3>';
        $result = $this->Helper->tag('h3', 'My text', ['class' => 'my-class', 'icon' => 'fa fa-home', 'icon-align' => 'right']);
        $this->assertSame($expected, $result);
    }

    /**
     * @test
     * @uses \MeTools\View\Helper\HtmlHelper::viewport()
     */
    public function testViewport(): void
    {
        $expected = '<meta name="viewport" content="initial-scale=1, width=device-width">';
        $result = $this->Helper->viewport([], ['block' => false]);
        $this->assertSame($expected, $result);

        $expected = '<meta title="my title" name="viewport" content="width=500, initial-scale=1">';
        $result = $this->Helper->viewport(['width' => 500], ['block' => false, 'title' => 'my title']);
        $this->assertSame($expected, $result);
    }

    /**
     * @test
     * @uses \MeTools\View\Helper\HtmlHelper::youtube()
     */
    public function testYoutube(): void
    {
        $expected = '<div class="ratio ratio-16x9"><iframe allowfullscreen="allowfullscreen" height="480" width="640" src="https://www.youtube.com/embed/-YcwR89cfao"></iframe></div>';
        $result = $this->Helper->youtube('-YcwR89cfao');
        $this->assertSame($expected, $result);

        $expected = '<div class="ratio ratio-4x3"><iframe allowfullscreen="allowfullscreen" height="480" width="640" src="https://www.youtube.com/embed/-YcwR89cfao"></iframe></div>';
        $result = $this->Helper->youtube('-YcwR89cfao', ['ratio' => '4x3']);
        $this->assertSame($expected, $result);

        $expected = '<iframe allowfullscreen="allowfullscreen" height="480" width="640" src="https://www.youtube.com/embed/-YcwR89cfao"></iframe>';
        $result = $this->Helper->youtube('-YcwR89cfao', ['ratio' => false]);
        $this->assertSame($expected, $result);

        $expected = '<div class="ratio ratio-16x9"><iframe class="my-class" height="100" width="200" allowfullscreen="allowfullscreen" src="https://www.youtube.com/embed/-YcwR89cfao"></iframe></div>';
        $result = $this->Helper->youtube('-YcwR89cfao', ['class' => 'my-class', 'height' => 100, 'width' => 200]);
        $this->assertSame($expected, $result);

        $expected = '<div class="ratio ratio-16x9"><iframe allowfullscreen="allowfullscreen" height="480" width="640" src="https://www.youtube.com/embed/-YcwR89cfao?start=80"></iframe></div>';
        $result = $this->Helper->youtube('-YcwR89cfao?t=80');
        $this->assertSame($expected, $result);
    }
}
