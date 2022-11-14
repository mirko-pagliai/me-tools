<?php
/** @noinspection PhpUnhandledExceptionInspection */
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

use Assets\View\Helper\AssetHelper;
use Cake\Core\Plugin;
use Cake\Http\ServerRequest;
use Cake\View\View;
use MeTools\TestSuite\HelperTestCase;
use MeTools\View\Helper\LibraryHelper;
use Tools\Filesystem;

/**
 * LibraryHelperTest class
 * @property \MeTools\View\Helper\LibraryHelper $Helper
 */
class LibraryHelperTest extends HelperTestCase
{
    protected const EXPECTED_DATEPICKER_ICONS = [
        'time' => 'fas fa-clock',
        'date' => 'fas fa-calendar',
        'up' => 'fas fa-chevron-up',
        'down' => 'fas fa-chevron-down',
        'previous' => 'fas fa-chevron-left',
        'next' => 'fas fa-chevron-right',
        'today' => 'fas fa-dot-circle',
        'clear' => 'fas fa-trash',
        'close' => 'fas fa-times',
    ];

    /**
     * Called before every test method
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        Filesystem::instance()->createFile(WWW_ROOT . 'ckeditor' . DS . 'ckeditor.js');
    }

    /**
     * Called after every test method
     * @return void
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        array_map(fn($dir) => Filesystem::instance()->unlinkRecursive(WWW_ROOT . $dir, '.gitkeep'), ['ckeditor', 'js', 'vendor']);
    }

    /**
     * Tests for `initialize()` method
     * @uses \MeTools\View\Helper\LibraryHelper::initialize()
     * @test
     */
    public function testInitialize(): void
    {
        //Checks that, when the `Assets` plugin is not present, the
        //  `AssetHelper` matches the `HtmlHelper`
        if (Plugin::getCollection()->has('Assets')) {
            $this->Helper->initialize([]);
            $this->assertInstanceOf(AssetHelper::class, $this->Helper->Asset);
            $this->removePlugins(['Assets']);
        }

        $this->Helper->initialize([]);
        $this->assertNotSame(AssetHelper::class, get_class($this->Helper->Asset));
    }

    /**
     * Tests for `beforeLayout()` method
     * @uses \MeTools\View\Helper\LibraryHelper::beforeLayout()
     * @test
     */
    public function testBeforeLayout(): void
    {
        $this->Helper->beforeLayout();
        $this->assertEmpty($this->getProperty($this->Helper, 'output'));
        $this->assertEmpty($this->Helper->getView()->fetch('script_bottom'));

        $expected = [
            '<script>$(function() {',
            '    //first',
            '    //second',
            '});</script>',
        ];
        $this->setProperty($this->Helper, 'output', ['//first', '//second']);
        $this->Helper->beforeLayout();
        $this->assertEmpty($this->getProperty($this->Helper, 'output'));
        $this->assertEquals($expected, preg_split('/' . PHP_EOL . '/', $this->Helper->getView()->fetch('script_bottom')));
    }

    /**
     * Tests for `analytics()` method
     * @uses \MeTools\View\Helper\LibraryHelper::analytics()
     * @test
     */
    public function testAnalytics(): void
    {
        $expected = '<script>!function(e,a,t,n,c,o,s){e.GoogleAnalyticsObject=c,e[c]=e[c]||function(){(e[c].q=e[c].q||[]).push(arguments)},e[c].l=1*new Date,o=a.createElement(t),s=a.getElementsByTagName(t)[0],o.async=1,o.src=n,s.parentNode.insertBefore(o,s)}(window,document,"script","//www.google-analytics.com/analytics.js","ga"),ga("create","my-id","auto"),ga("send","pageview");</script>';
        $this->Helper->analytics('my-id');
        $this->assertEquals($expected, $this->Helper->getView()->fetch('script_bottom'));
    }

    /**
     * Tests for `analytics()` method, on localhost
     * @uses \MeTools\View\Helper\LibraryHelper::analytics()
     * @test
     */
    public function testAnalyticsOnLocalhost(): void
    {
        $request = $this->createMock(ServerRequest::class);
        $request->expects($this->any())->method('is')->willReturn(true);
        $Helper = new LibraryHelper(new View($request));
        $Helper->analytics('my-id');
        $this->assertEmpty($Helper->getView()->fetch('script_bottom'));
    }

    /**
     * Tests for `ckeditor()` method
     * @uses \MeTools\View\Helper\LibraryHelper::ckeditor()
     * @test
     */
    public function testCkeditor(): void
    {
        $expected = [
            ['script' => ['src' => '/ckeditor/ckeditor.js']],
            '/script',
            ['script' => ['src' => '/me_tools/js/ckeditor_init.php?type=js']],
            '/script',
        ];
        $this->Helper->ckeditor();
        $this->assertHtml($expected, $this->Helper->getView()->fetch('script_bottom'));

        //With the jQuery adapter
        Filesystem::instance()->createFile(WWW_ROOT . 'ckeditor' . DS . 'adapters' . DS . 'jquery.js');
        $expected = [...$expected, ['script' => ['src' => '/ckeditor/adapters/jquery.js']], '/script'];
        $this->Helper->ckeditor(true);
        $this->assertHtml($expected, $this->Helper->getView()->fetch('script_bottom'));
    }

    /**
     * Tests for `ckeditor()` method, with a js config file from app
     * @uses \MeTools\View\Helper\LibraryHelper::ckeditor()
     * @test
     */
    public function testCkeditorWithJsFromApp(): void
    {
        Filesystem::instance()->createFile(WWW_ROOT . 'js' . DS . 'ckeditor_init.js');

        $expected = [
            ['script' => ['src' => '/ckeditor/ckeditor.js']],
            '/script',
            ['script' => ['src' => '/js/ckeditor_init.js']],
            '/script',
        ];
        $this->Helper->ckeditor();
        $this->assertHtml($expected, $this->Helper->getView()->fetch('script_bottom'));
    }

    /**
     * Tests for `ckeditor()` method, with a php config file from app
     * @uses \MeTools\View\Helper\LibraryHelper::ckeditor()
     * @test
     */
    public function testCkeditorWithPhpFromApp(): void
    {
        Filesystem::instance()->createFile(WWW_ROOT . 'js' . DS . 'ckeditor_init.php');

        $expected = [
            ['script' => ['src' => '/ckeditor/ckeditor.js']],
            '/script',
            ['script' => ['src' => '/js/ckeditor_init.php?type=js']],
            '/script',
        ];
        $this->Helper->ckeditor();
        $this->assertHtml($expected, $this->Helper->getView()->fetch('script_bottom'));
    }

    /**
     * Tests for `fancybox()` method
     * @uses \MeTools\View\Helper\LibraryHelper::fancybox()
     * @test
     */
    public function testFancybox(): void
    {
        $expectedCss = '<link rel="stylesheet" href="/vendor/fancyapps-fancybox/jquery.fancybox.min.css"/>';
        $expectedJs = '<script src="/vendor/fancyapps-fancybox/jquery.fancybox.min.js"></script>';
        $this->Helper->fancybox();
        $this->assertSame($expectedCss, $this->Helper->getView()->fetch('css_bottom'));
        $this->assertSame($expectedJs, $this->Helper->getView()->fetch('script_bottom'));

        //With che init file
        Filesystem::instance()->createFile(WWW_ROOT . 'js' . DS . 'fancybox_init.js');
        $expectedJs .= '<script src="/js/fancybox_init.js"></script>';
        $this->Helper->fancybox();
        $this->assertSame($expectedJs, $this->Helper->getView()->fetch('script_bottom'));
    }

    /**
     * Tests for `shareaholic()` method
     * @uses \MeTools\View\Helper\LibraryHelper::shareaholic()
     * @test
     */
    public function testShareaholic(): void
    {
        $expected = [
            'script' => [
                'src' => 'https://dsms0mj1bbhn4.cloudfront.net/assets/pub/shareaholic.js',
                'async' => 'async',
                'data-cfasync' => 'false',
                'data-shr-siteid' => 'my-id',
            ],
            '/script',
        ];
        $this->Helper->shareaholic('my-id');
        $this->assertHtml($expected, $this->Helper->getView()->fetch('script_bottom'));
    }

    /**
     * Tests for `slugify()` method
     * @uses \MeTools\View\Helper\LibraryHelper::slugify()
     * @test
     */
    public function testSlugify(): void
    {
        $expected = ['script' => ['src' => '/me_tools/js/slugify.js'], '/script'];
        $this->Helper->slugify();
        $this->assertHtml($expected, $this->Helper->getView()->fetch('script_bottom'));

        $expected = ['$().slugify("form #title", "form #slug");'];
        $this->assertEquals($expected, $this->getProperty($this->Helper, 'output'));
    }
}
