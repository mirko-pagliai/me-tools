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

use Cake\Event\Event;
use Cake\View\View;
use MeTools\TestSuite\TestCase;
use MeTools\View\Helper\LibraryHelper;

/**
 * LibraryHelperTest class
 */
class LibraryHelperTest extends TestCase
{
    /**
     * @var \MeTools\View\Helper\LibraryHelper
     */
    protected $Library;

    /**
     * @var \Cake\View\View
     */
    protected $View;

    /**
     * Setup the test case, backup the static object values so they can be
     * restored. Specifically backs up the contents of Configure and paths in
     *  App if they have not already been backed up
     * @return void
     */
    public function setUp()
    {
        parent::setUp();

        $this->View = new View;
        $this->Library = new LibraryHelper($this->View);
    }

    /**
     * Teardown any static object changes and restore them
     * @return void
     */
    public function tearDown()
    {
        parent::tearDown();

        //@codingStandardsIgnoreStart
        @unlink(WWW_ROOT . 'ckeditor' . DS . 'adapters' . DS . 'jquery.js');
        @unlink(WWW_ROOT . 'ckeditor' . DS . 'ckeditor.js');
        @unlink(WWW_ROOT . 'js' . DS . 'ckeditor_init.js');
        @unlink(WWW_ROOT . 'js' . DS . 'ckeditor_init.php');
        @unlink(WWW_ROOT . 'vendor' . DS . 'fancybox');
        @unlink(WWW_ROOT . 'js' . DS . 'fancybox_init.js');
        //@codingStandardsIgnoreEnd
    }

    /**
     * Tests for `beforeLayout` method
     * @test
     */
    public function testBeforeLayout()
    {
        $this->Library->beforeLayout(new Event(null), null);
        $this->assertEmpty($this->getProperty($this->Library, 'output'));
        $this->assertEmpty($this->View->Blocks->get('script_bottom'));

        $this->setProperty($this->Library, 'output', ['//first', '//second']);
        $this->Library->beforeLayout(new Event(null), null);
        $this->assertEmpty($this->getProperty($this->Library, 'output'));
        $result = $this->View->Blocks->get('script_bottom');

        $result = preg_split('/' . PHP_EOL . '/', $result);
        $expected = [
            '<script>$(function() {',
            '    //first',
            '    //second',
            '});</script>',
        ];
        $this->assertEquals($expected, $result);
    }

    /**
     * Tests for `analytics` method
     * @test
     */
    public function testAnalytics()
    {
        $this->Library->analytics('my-id');
        $result = $this->View->Blocks->get('script_bottom');
        $this->assertEquals(
            '<script>!function(e,a,t,n,c,o,s){e.GoogleAnalyticsObject=c,e[c]=e[c]||function(){(e[c].q=e[c].q||[]).push(arguments)},e[c].l=1*new Date,o=a.createElement(t),s=a.getElementsByTagName(t)[0],o.async=1,o.src=n,s.parentNode.insertBefore(o,s)}(window,document,"script","//www.google-analytics.com/analytics.js","ga"),ga("create","my-id","auto"),ga("send","pageview");</script>',
            $result
        );
    }

    /**
     * Tests for `analytics` method, on localhost
     * @test
     */
    public function testAnalyticsOnLocalhost()
    {
        //On localhost
        $this->Library->request = $this->getMockBuilder(Request::class)
            ->setMethods(['is'])
            ->getMock();

        $this->Library->request->expects($this->once())
             ->method('is')
             ->with('localhost')
             ->willReturn(true);

        $this->Library->analytics('my-id');
        $result = $this->View->Blocks->get('script_bottom');
        $this->assertEmpty($result);
    }

    /**
     * Tests for `ckeditor` method
     * @test
     */
    public function testCkeditor()
    {
        $this->Library->ckeditor();
        $result = $this->View->Blocks->get('script_bottom');
        $this->assertEmpty($result);

        file_put_contents(WWW_ROOT . 'ckeditor' . DS . 'ckeditor.js', null);

        $this->Library->ckeditor();
        $result = $this->View->Blocks->get('script_bottom');

        $expected = [
            ['script' => ['src' => '/ckeditor/ckeditor.js']],
            '/script',
            ['script' => ['src' => '/me_tools/js/ckeditor_init.php?']],
            '/script',
        ];
        $this->assertHtml($expected, $result);
    }

    /**
     * Tests for `ckeditor` method, with the jQuery adapter
     * @test
     */
    public function testCkeditorWithJqueryAdapter()
    {
        file_put_contents(WWW_ROOT . 'ckeditor' . DS . 'ckeditor.js', null);
        file_put_contents(WWW_ROOT . 'ckeditor' . DS . 'adapters' . DS . 'jquery.js', null);

        $this->Library->ckeditor(true);
        $result = $this->View->Blocks->get('script_bottom');

        $expected = [
            ['script' => ['src' => '/ckeditor/ckeditor.js']],
            '/script',
            ['script' => ['src' => '/ckeditor/adapters/jquery.js']],
            '/script',
            ['script' => ['src' => '/me_tools/js/ckeditor_init.php?']],
            '/script',
        ];
        $this->assertHtml($expected, $result);
    }

    /**
     * Tests for `ckeditor` method, with a js config file from app
     * @test
     */
    public function testCkeditorWithJsFromApp()
    {
        file_put_contents(WWW_ROOT . 'ckeditor' . DS . 'ckeditor.js', null);
        file_put_contents(WWW_ROOT . 'js' . DS . 'ckeditor_init.js', null);

        $this->Library->ckeditor();
        $result = $this->View->Blocks->get('script_bottom');

        $expected = [
            ['script' => ['src' => '/ckeditor/ckeditor.js']],
            '/script',
            ['script' => ['src' => '/js/ckeditor_init.js']],
            '/script',
        ];
        $this->assertHtml($expected, $result);
    }

    /**
     * Tests for `ckeditor` method, with a php config file from app
     * @test
     */
    public function testCkeditorWithPhpFromApp()
    {
        file_put_contents(WWW_ROOT . 'ckeditor' . DS . 'ckeditor.js', null);
        file_put_contents(WWW_ROOT . 'js' . DS . 'ckeditor_init.php', null);

        $this->Library->ckeditor();
        $result = $this->View->Blocks->get('script_bottom');
        $expected = [
            ['script' => ['src' => '/ckeditor/ckeditor.js']],
            '/script',
            ['script' => ['src' => '/js/ckeditor_init.php?']],
            '/script',
        ];
        $this->assertHtml($expected, $result);
    }

    /**
     * Tests for `datepicker` method
     * @test
     */
    public function testDatepicker()
    {
        $this->Library->datepicker('#my-id');

        $output = $this->getProperty($this->Library, 'output');
        $this->assertEquals(1, preg_match('/\$\("#my-id"\)\.datetimepicker\(({\n(\s+.+\n)+})\);/', $output[0], $matches));
        $this->assertNotEmpty($matches[1]);
        $result = json_decode($matches[1], true);
        $expected = [
            'format' => 'YYYY/MM/DD',
            'showTodayButton' => true,
            'showClear' => true,
            'icons' => [
                    'time' => 'fa fa-clock-o',
                    'date' => 'fa fa-calendar',
                    'up' => 'fa fa-chevron-up',
                    'down' => 'fa fa-chevron-down',
                    'previous' => 'fa fa-chevron-left',
                    'next' => 'fa fa-chevron-right',
                    'today' => 'fa fa-dot-circle-o',
                    'clear' => 'fa fa-trash',
                    'close' => 'fa fa-times',
            ],
            'locale' => 'en',
        ];
        $this->assertEquals($expected, $result);

        $result = $this->View->Blocks->get('script_bottom');
        $expected = [
            ['script' => ['src' => '/vendor/moment/moment-with-locales.min.js']],
            '/script',
            ['script' => ['src' => '/me_tools/js/bootstrap-datetimepicker.min.js']],
            '/script',
        ];
        $this->assertHtml($expected, $result);

        $result = $this->View->Blocks->get('css_bottom');
        $expected = ['link' => [
            'rel' => 'stylesheet',
            'href' => '/vendor/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css',
        ]];
        $this->assertHtml($expected, $result);
    }

    /**
     * Tests for `datetimepicker` method
     * @test
     */
    public function testDatetimepicker()
    {
        //Note: assets have already been tested in the `testDatepicker()` method

        $this->Library->datetimepicker('#my-id');

        $output = $this->getProperty($this->Library, 'output');
        $this->assertEquals(1, preg_match('/\$\("#my-id"\)\.datetimepicker\(({\n(\s+.+\n)+})\);/', $output[0], $matches));
        $this->assertNotEmpty($matches[1]);
        $result = json_decode($matches[1], true);
        $expected = [
            'showTodayButton' => true,
            'showClear' => true,
            'icons' => [
                    'time' => 'fa fa-clock-o',
                    'date' => 'fa fa-calendar',
                    'up' => 'fa fa-chevron-up',
                    'down' => 'fa fa-chevron-down',
                    'previous' => 'fa fa-chevron-left',
                    'next' => 'fa fa-chevron-right',
                    'today' => 'fa fa-dot-circle-o',
                    'clear' => 'fa fa-trash',
                    'close' => 'fa fa-times',
            ],
            'locale' => 'en',
        ];
        $this->assertEquals($expected, $result);
    }

    /**
     * Tests for `fancybox` method
     * @test
     */
    public function testFancybox()
    {
        //@codingStandardsIgnoreLine
        @symlink(VENDOR . 'newerton' . DS . 'fancy-box' . DS . 'source', WWW_ROOT . 'vendor' . DS . 'fancybox');

        $this->Library->fancybox();

        $result = $this->View->Blocks->get('css_bottom');
        $expected = [
            ['link' => ['rel' => 'stylesheet', 'href' => '/vendor/fancybox/jquery.fancybox.css']],
            ['link' => ['rel' => 'stylesheet', 'href' => '/vendor/fancybox/helpers/jquery.fancybox-buttons.css']],
            ['link' => ['rel' => 'stylesheet', 'href' => '/vendor/fancybox/helpers/jquery.fancybox-thumbs.css']],
        ];
        $this->assertHtml($expected, $result);

        $result = $this->View->Blocks->get('script_bottom');
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
        $this->assertHtml($expected, $result);
    }

    /**
     * Tests for `fancybox` method, with a js config file from app
     * @test
     */
    public function testFancyboxWithJsFromApp()
    {
        //@codingStandardsIgnoreLine
        @symlink(VENDOR . 'newerton' . DS . 'fancy-box' . DS . 'source', WWW_ROOT . 'vendor' . DS . 'fancybox');

        file_put_contents(WWW_ROOT . 'js' . DS . 'fancybox_init.js', null);

        $this->Library->fancybox();

        $result = $this->View->Blocks->get('script_bottom');
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
        $this->assertHtml($expected, $result);
    }

    /**
     * Tests for `shareaholic` method
     * @test
     */
    public function testShareaholic()
    {
        $this->Library->shareaholic('my-id');

        $result = $this->View->Blocks->get('script_bottom');
        $expected = [
            'script' => [
                'src' => '//dsms0mj1bbhn4.cloudfront.net/assets/pub/shareaholic.js',
                'async' => 'async',
                'data-cfasync' => 'false',
                'data-shr-siteid' => 'my-id',
            ],
            '/script',
        ];

        $this->assertHtml($expected, $result);
    }

    /**
     * Tests for `slugify` method
     * @test
     */
    public function testSlugify()
    {
        $this->Library->slugify();

        $result = $this->View->Blocks->get('script_bottom');
        $expected = [
            'script' => ['src' => '/me_tools/js/slugify.js'],
            '/script',
        ];
        $this->assertHtml($expected, $result);

        $output = $this->getProperty($this->Library, 'output');
        $this->assertEquals(['$().slugify("form #title", "form #slug");'], $output);
    }

    /**
     * Tests for `timepicker` method
     * @test
     */
    public function testTimepicker()
    {
        //Note: assets have already been tested in the `testDatepicker()` method

        $this->Library->timepicker('#my-id');

        $output = $this->getProperty($this->Library, 'output');
        $this->assertEquals(1, preg_match('/\$\("#my-id"\)\.datetimepicker\(({\n(\s+.+\n)+})\);/', $output[0], $matches));
        $this->assertNotEmpty($matches[1]);
        $result = json_decode($matches[1], true);
        $expected = [
            'pickTime' => false,
            'showTodayButton' => true,
            'showClear' => true,
            'icons' => [
                    'time' => 'fa fa-clock-o',
                    'date' => 'fa fa-calendar',
                    'up' => 'fa fa-chevron-up',
                    'down' => 'fa fa-chevron-down',
                    'previous' => 'fa fa-chevron-left',
                    'next' => 'fa fa-chevron-right',
                    'today' => 'fa fa-dot-circle-o',
                    'clear' => 'fa fa-trash',
                    'close' => 'fa fa-times',
            ],
            'locale' => 'en',
        ];
        $this->assertEquals($expected, $result);
    }
}
