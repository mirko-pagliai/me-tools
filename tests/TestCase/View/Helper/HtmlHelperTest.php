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
}
