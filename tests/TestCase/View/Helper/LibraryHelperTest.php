<?php
/** @noinspection PhpDocMissingThrowsInspection */
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
use MeTools\TestSuite\HelperTestCase;
use Tools\Filesystem;

/**
 * LibraryHelperTest class
 * @property \MeTools\View\Helper\LibraryHelper $Helper
 */
class LibraryHelperTest extends HelperTestCase
{
    /**
     * Called before every test method
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        Filesystem::createFile(WWW_ROOT . 'ckeditor' . DS . 'ckeditor.js');
    }

    /**
     * Called after every test method
     * @return void
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        array_map(fn($dir) => Filesystem::unlinkRecursive(WWW_ROOT . $dir, '.gitkeep'), ['ckeditor', 'js', 'vendor']);
    }

    /**
     * @test
     * @uses \MeTools\View\Helper\LibraryHelper::initialize()
     */
    public function testInitialize(): void
    {
        //Checks that, when the `Assets` plugin is not present, the `AssetHelper` matches the `HtmlHelper`
        if (Plugin::getCollection()->has('Assets')) {
            $this->Helper->initialize([]);
            $this->assertInstanceOf(AssetHelper::class, $this->Helper->Asset);
            $this->removePlugins(['Assets']);
        }

        $this->Helper->initialize([]);
        $this->assertNotSame(AssetHelper::class, get_class($this->Helper->Asset));
    }

    /**
     * @test
     * @uses \MeTools\View\Helper\LibraryHelper::beforeLayout()
     */
    public function testBeforeLayout(): void
    {
        $this->Helper->getView()->dispatchEvent('View.beforeLayout');
        $this->assertEmpty($this->Helper->getOutput());
        $this->assertEmpty($this->Helper->getView()->fetch('script_bottom'));

        $expected = '<script src="/me_tools/js/slugify.js"></script>';
        $this->Helper->slugify();
        $this->Helper->getView()->dispatchEvent('View.beforeLayout');
        $this->assertEquals($expected, $this->Helper->getView()->fetch('script_bottom'));
    }

    /**
     * @test
     * @uses \MeTools\View\Helper\LibraryHelper::ckeditor()
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
        Filesystem::createFile(WWW_ROOT . 'ckeditor' . DS . 'adapters' . DS . 'jquery.js');
        $expected = [...$expected, ['script' => ['src' => '/ckeditor/adapters/jquery.js']], '/script'];
        $this->Helper->ckeditor(true);
        $this->assertHtml($expected, $this->Helper->getView()->fetch('script_bottom'));
    }

    /**
     * Tests for `ckeditor()` method, with a js config file from app
     * @test
     * @uses \MeTools\View\Helper\LibraryHelper::ckeditor()
     */
    public function testCkeditorWithJsFromApp(): void
    {
        Filesystem::createFile(WWW_ROOT . 'js' . DS . 'ckeditor_init.js');

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
     * @test
     * @uses \MeTools\View\Helper\LibraryHelper::ckeditor()
     */
    public function testCkeditorWithPhpFromApp(): void
    {
        Filesystem::createFile(WWW_ROOT . 'js' . DS . 'ckeditor_init.php');

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
     * @test
     * @uses \MeTools\View\Helper\LibraryHelper::fancybox()
     */
    public function testFancybox(): void
    {
        $expectedCss = '<link rel="stylesheet" href="/vendor/fancyapps-fancybox/jquery.fancybox.min.css"/>';
        $expectedJs = '<script src="/vendor/fancyapps-fancybox/jquery.fancybox.min.js"></script>';
        $this->Helper->fancybox();
        $this->assertSame($expectedCss, $this->Helper->getView()->fetch('css_bottom'));
        $this->assertSame($expectedJs, $this->Helper->getView()->fetch('script_bottom'));

        //With che init file
        Filesystem::createFile(WWW_ROOT . 'js' . DS . 'fancybox_init.js');
        $expectedJs .= '<script src="/js/fancybox_init.js"></script>';
        $this->Helper->fancybox();
        $this->assertSame($expectedJs, $this->Helper->getView()->fetch('script_bottom'));
    }

    /**
     * @test
     * @uses \MeTools\View\Helper\LibraryHelper::shareaholic()
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
     * @test
     * @uses \MeTools\View\Helper\LibraryHelper::slugify()
     */
    public function testSlugify(): void
    {
        $expected = ['script' => ['src' => '/me_tools/js/slugify.js'], '/script'];
        $this->Helper->slugify();
        $this->assertHtml($expected, $this->Helper->getView()->fetch('script_bottom'));

        $expected = ['$().slugify("form #title", "form #slug");'];
        $this->assertEquals($expected, $this->Helper->getOutput());
    }
}
