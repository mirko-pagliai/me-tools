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

use Assets\View\Helper\AssetHelper;
use Cake\Core\Plugin;
use Cake\Event\Event;
use Cake\Http\ServerRequest;
use Cake\View\View;
use MeTools\TestSuite\HelperTestCase;
use MeTools\View\Helper\HtmlHelper;

/**
 * LibraryHelperTest class
 */
class LibraryHelperTest extends HelperTestCase
{
    /**
     * @var array
     */
    protected $expectedDatepickerIcons = [
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
     * Called after every test method
     * @return void
     */
    public function tearDown()
    {
        parent::tearDown();

        foreach ([
            'ckeditor' . DS . 'adapters' . DS . 'jquery.js',
            'ckeditor' . DS . 'ckeditor.js',
            'js' . DS . 'ckeditor_init.js',
            'js' . DS . 'ckeditor_init.php',
            'js' . DS . 'fancybox_init.js',
            'vendor' . DS . 'fancybox',
        ] as $file) {
            safe_unlink(WWW_ROOT . $file);
        }
    }

    /**
     * Tests for `initialize()` method
     * @test
     */
    public function testInitialize()
    {
        //Checks that, when the `Assets` plugin is not present, the
        //  `AssetHelper` matches the `HtmlHelper`
        if (Plugin::getCollection()->has('Assets')) {
            $this->Helper->initialize([]);
            $this->assertInstanceof(AssetHelper::class, $this->Helper->Asset);

            $this->removePlugins(['Assets']);
        }

        $this->Helper->initialize([]);
        $this->assertInstanceof(HtmlHelper::class, $this->Helper->Asset);
    }

    /**
     * Tests for `beforeLayout()` method
     * @test
     */
    public function testBeforeLayout()
    {
        $this->Helper->beforeLayout(new Event(null), null);
        $this->assertEmpty($this->getProperty($this->Helper, 'output'));
        $this->assertEmpty($this->Helper->getView()->fetch('script_bottom'));

        $expected = [
            '<script>$(function() {',
            '    //first',
            '    //second',
            '});</script>',
        ];
        $this->setProperty($this->Helper, 'output', ['//first', '//second']);
        $this->Helper->beforeLayout(new Event(null), null);
        $this->assertEmpty($this->getProperty($this->Helper, 'output'));
        $this->assertEquals($expected, preg_split('/' . PHP_EOL . '/', $this->Helper->getView()->fetch('script_bottom')));
    }

    /**
     * Tests for `analytics()` method
     * @test
     */
    public function testAnalytics()
    {
        $expected = '<script>!function(e,a,t,n,c,o,s){e.GoogleAnalyticsObject=c,e[c]=e[c]||function(){(e[c].q=e[c].q||[]).push(arguments)},e[c].l=1*new Date,o=a.createElement(t),s=a.getElementsByTagName(t)[0],o.async=1,o.src=n,s.parentNode.insertBefore(o,s)}(window,document,"script","//www.google-analytics.com/analytics.js","ga"),ga("create","my-id","auto"),ga("send","pageview");</script>';
        $this->Helper->analytics('my-id');
        $this->assertEquals($expected, $this->Helper->getView()->fetch('script_bottom'));
    }

    /**
     * Tests for `analytics()` method, on localhost
     * @test
     */
    public function testAnalyticsOnLocalhost()
    {
        $request = $this->getMockBuilder(ServerRequest::class)
            ->setMethods(['is'])
            ->getMock();

        $request->expects($this->any())->method('is')->willReturn(true);

        $helper = $this->getMockBuilder(get_class($this->Helper))
            ->setMethods(null)
            ->setConstructorArgs([new View($request)])
            ->getMock();

        $helper->analytics('my-id');
        $this->assertEmpty($helper->getView()->fetch('script_bottom'));
    }

    /**
     * Tests for `ckeditor()` method
     * @test
     */
    public function testCkeditor()
    {
        safe_create_file(WWW_ROOT . 'ckeditor' . DS . 'ckeditor.js');

        $expected = [
            ['script' => ['src' => '/ckeditor/ckeditor.js']],
            '/script',
            ['script' => ['src' => '/me_tools/js/ckeditor_init.php?']],
            '/script',
        ];
        $this->Helper->ckeditor();
        $this->assertHtml($expected, $this->Helper->getView()->fetch('script_bottom'));
    }

    /**
     * Tests for `ckeditor()` method, with the jQuery adapter
     * @test
     */
    public function testCkeditorWithJqueryAdapter()
    {
        safe_create_file(WWW_ROOT . 'ckeditor' . DS . 'ckeditor.js');
        safe_create_file(WWW_ROOT . 'ckeditor' . DS . 'adapters' . DS . 'jquery.js');

        $expected = [
            ['script' => ['src' => '/ckeditor/ckeditor.js']],
            '/script',
            ['script' => ['src' => '/ckeditor/adapters/jquery.js']],
            '/script',
            ['script' => ['src' => '/me_tools/js/ckeditor_init.php?']],
            '/script',
        ];
        $this->Helper->ckeditor(true);
        $this->assertHtml($expected, $this->Helper->getView()->fetch('script_bottom'));
    }

    /**
     * Tests for `ckeditor()` method, with a js config file from app
     * @test
     */
    public function testCkeditorWithJsFromApp()
    {
        safe_create_file(WWW_ROOT . 'ckeditor' . DS . 'ckeditor.js');
        safe_create_file(WWW_ROOT . 'js' . DS . 'ckeditor_init.js');

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
     */
    public function testCkeditorWithPhpFromApp()
    {
        safe_create_file(WWW_ROOT . 'ckeditor' . DS . 'ckeditor.js');
        safe_create_file(WWW_ROOT . 'js' . DS . 'ckeditor_init.php');

        $expected = [
            ['script' => ['src' => '/ckeditor/ckeditor.js']],
            '/script',
            ['script' => ['src' => '/js/ckeditor_init.php?']],
            '/script',
        ];
        $this->Helper->ckeditor();
        $this->assertHtml($expected, $this->Helper->getView()->fetch('script_bottom'));
    }

    /**
     * Tests for `datepicker()` method
     * @test
     */
    public function testDatepicker()
    {
        $expected = [
            'format' => 'YYYY/MM/DD',
            'icons' => $this->expectedDatepickerIcons,
            'locale' => 'en',
            'showTodayButton' => true,
            'showClear' => true,
        ];
        $this->Helper->datepicker('#my-id');
        $output = $this->getProperty($this->Helper, 'output');
        $this->assertEquals(1, preg_match('/\$\("#my-id"\)\.datetimepicker\(({\n(\s+.+\n)+})\);/', $output[0], $matches));
        $this->assertNotEmpty($matches[1]);
        $this->assertEquals($expected, json_decode($matches[1], true));

        $expected = [
            ['script' => ['src' => '/vendor/moment/moment-with-locales.min.js']],
            '/script',
            ['script' => ['src' => '/me_tools/js/bootstrap-datetimepicker.min.js']],
            '/script',
        ];
        $this->assertHtml($expected, $this->Helper->getView()->fetch('script_bottom'));

        $expected = ['link' => [
            'rel' => 'stylesheet',
            'href' => '/vendor/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css',
        ]];
        $this->assertHtml($expected, $this->Helper->getView()->fetch('css_bottom'));
    }

    /**
     * Tests for `datetimepicker()` method.
     *
     * Note: assets have already been tested in the `testDatepicker()` method.
     * @test
     */
    public function testDatetimepicker()
    {
        $expected = [
            'icons' => $this->expectedDatepickerIcons,
            'locale' => 'en',
            'showTodayButton' => true,
            'showClear' => true,
        ];
        $this->Helper->datetimepicker('#my-id');
        $output = $this->getProperty($this->Helper, 'output');
        $this->assertEquals(1, preg_match('/\$\("#my-id"\)\.datetimepicker\(({\n(\s+.+\n)+})\);/', $output[0], $matches));
        $this->assertNotEmpty($matches[1]);
        $this->assertEquals($expected, json_decode($matches[1], true));
    }

    /**
     * Tests for `fancybox()` method
     * @test
     */
    public function testFancybox()
    {
        safe_symlink(VENDOR . 'newerton' . DS . 'fancy-box' . DS . 'source', WWW_ROOT . 'vendor' . DS . 'fancybox');

        $expected = [
            ['link' => ['rel' => 'stylesheet', 'href' => '/vendor/fancybox/jquery.fancybox.css']],
            ['link' => ['rel' => 'stylesheet', 'href' => '/vendor/fancybox/helpers/jquery.fancybox-buttons.css']],
            ['link' => ['rel' => 'stylesheet', 'href' => '/vendor/fancybox/helpers/jquery.fancybox-thumbs.css']],
        ];
        $this->Helper->fancybox();
        $this->assertHtml($expected, $this->Helper->getView()->fetch('css_bottom'));

        $expected = [
            ['script' => ['src' => '/vendor/fancybox/jquery.fancybox.pack.js']],
            '/script',
            ['script' => ['src' => '/vendor/fancybox/helpers/jquery.fancybox-buttons.js']],
            '/script',
            ['script' => ['src' => '/vendor/fancybox/helpers/jquery.fancybox-thumbs.js']],
            '/script',
            ['script' => ['src' => '/me_tools/fancybox/fancybox_init.js']],
            '/script',
        ];
        $this->assertHtml($expected, $this->Helper->getView()->fetch('script_bottom'));
    }

    /**
     * Tests for `fancybox()` method, with a js config file from app
     * @test
     */
    public function testFancyboxWithJsFromApp()
    {
        safe_symlink(VENDOR . 'newerton' . DS . 'fancy-box' . DS . 'source', WWW_ROOT . 'vendor' . DS . 'fancybox');
        safe_create_file(WWW_ROOT . 'js' . DS . 'fancybox_init.js');

        $expected = [
            ['script' => ['src' => '/vendor/fancybox/jquery.fancybox.pack.js']],
            '/script',
            ['script' => ['src' => '/vendor/fancybox/helpers/jquery.fancybox-buttons.js']],
            '/script',
            ['script' => ['src' => '/vendor/fancybox/helpers/jquery.fancybox-thumbs.js']],
            '/script',
            ['script' => ['src' => '/js/fancybox_init.js']],
            '/script',
        ];
        $this->Helper->fancybox();
        $this->assertHtml($expected, $this->Helper->getView()->fetch('script_bottom'));
    }

    /**
     * Tests for `shareaholic()` method
     * @test
     */
    public function testShareaholic()
    {
        $expected = [
            'script' => [
                'src' => '//dsms0mj1bbhn4.cloudfront.net/assets/pub/shareaholic.js',
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
     * @test
     */
    public function testSlugify()
    {
        $expected = [
            'script' => ['src' => '/me_tools/js/slugify.js'],
            '/script',
        ];
        $this->Helper->slugify();
        $this->assertHtml($expected, $this->Helper->getView()->fetch('script_bottom'));

        $expected = ['$().slugify("form #title", "form #slug");'];
        $this->assertEquals($expected, $this->getProperty($this->Helper, 'output'));
    }

    /**
     * Tests for `timepicker()` method.
     *
     * Note: assets have already been tested in the `testDatepicker()` method.
     * @test
     */
    public function testTimepicker()
    {
        $expected = [
            'icons' => $this->expectedDatepickerIcons,
            'locale' => 'en',
            'pickTime' => false,
            'showTodayButton' => true,
            'showClear' => true,
        ];
        $this->Helper->timepicker('#my-id');
        $output = $this->getProperty($this->Helper, 'output');
        $this->assertEquals(1, preg_match('/\$\("#my-id"\)\.datetimepicker\(({\n(\s+.+\n)+})\);/', $output[0], $matches));
        $this->assertNotEmpty($matches[1]);
        $this->assertEquals($expected, json_decode($matches[1], true));
    }
}
