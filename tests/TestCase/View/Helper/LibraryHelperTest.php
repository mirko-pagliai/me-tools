<?php
/**
 * This file is part of MeTools.
 *
 * MeTools is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * MeTools is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with MeTools.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @author      Mirko Pagliai <mirko.pagliai@gmail.com>
 * @copyright   Copyright (c) 2016, Mirko Pagliai for Nova Atlantis Ltd
 * @license     http://www.gnu.org/licenses/agpl.txt AGPL License
 * @link        http://git.novatlantis.it Nova Atlantis Ltd
 */
namespace MeTools\Test\TestCase\View\Helper;

use Cake\TestSuite\TestCase;
use Cake\View\View;
use MeTools\Test\TestCase\View\Helper\LibraryHelper;

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
     * @var \Cake\View\View;
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

        $this->View = new View();
        $this->Library = new LibraryHelper($this->View);
    }

    /**
     * Teardown any static object changes and restore them
     * @return void
     */
    public function tearDown()
    {
        parent::tearDown();

        unset($this->Library, $this->View);
    }

    /**
     * Tests for `beforeLayout` method
     * @test
     */
    public function testBeforeLayout()
    {
        $this->assertEmpty($this->Library->output());

        $this->Library->output(['//first', '//second']);
        $this->Library->beforeLayout(new \Cake\Event\Event(null), null);
        $this->assertEmpty($this->Library->output());
        $result = $this->View->Blocks->get('script_bottom');

        $result = preg_split('/\n\s*/', $result);
        $expected = ['<script>', '//<![CDATA[', '$(function() {', '//first', '//second', '});', '//]]>', '</script>'];
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

        $this->assertNotEmpty($result);

        $result = explode(PHP_EOL, $result);

        $this->assertEquals('<script>', $result[0]);
        $this->assertEquals('</script>', $result[count($result) - 1]);
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

        unlink(WWW_ROOT . 'ckeditor' . DS . 'ckeditor.js');
    }

    /**
     * Tests for `ckeditor` method, with the jQuery adapter
     * @test
     */
    public function testCkeditorWithJqueryAdapter()
    {
        file_put_contents(WWW_ROOT . 'ckeditor' . DS . 'ckeditor.js', null);
        file_put_contents(WWW_ROOT . 'ckeditor' . DS . 'adapters' . DS . 'jquery.js', null);

        $this->Library->ckeditor();
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

        unlink(WWW_ROOT . 'ckeditor' . DS . 'ckeditor.js');
        unlink(WWW_ROOT . 'ckeditor' . DS . 'adapters' . DS . 'jquery.js');
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

        unlink(WWW_ROOT . 'ckeditor' . DS . 'ckeditor.js');
        unlink(WWW_ROOT . 'js' . DS . 'ckeditor_init.js');
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

        unlink(WWW_ROOT . 'ckeditor' . DS . 'ckeditor.js');
        unlink(WWW_ROOT . 'js' . DS . 'ckeditor_init.php');
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
            'script' => [
                'src' => 'preg:/\/assets\/js\/[a-z0-9]+\.js/',
            ],
            '/script',
        ];
        $this->assertHtml($expected, $result);

        $this->assertEquals(['$().slugify("form #title", "form #slug");'], $this->Library->output());
    }
}
