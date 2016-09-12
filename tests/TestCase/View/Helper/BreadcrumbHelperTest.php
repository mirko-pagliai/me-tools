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
use MeTools\View\Helper\BreadcrumbHelper;

/**
 * BreadcrumbHelperTest class
 */
class BreadcrumbHelperTest extends TestCase
{
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
        $this->Breadcrumb = new BreadcrumbHelper($this->View);
    }

    /**
     * Teardown any static object changes and restore them
     * @return void
     */
    public function tearDown()
    {
        parent::tearDown();

        unset($this->Breadcrumb, $this->View);
    }

    /**
     * Tests for `add()` method
     * @return void
     * @test
     */
    public function testAdd()
    {
        $result = $this->Breadcrumb->get();
        $this->assertNull($result);

        $result = $this->Breadcrumb->add('First level', '/first');
        $this->assertNull($result);

        $result = $this->Breadcrumb->add('Second level', '/second');
        $this->assertNull($result);

        $result = $this->Breadcrumb->get();
        $this->assertNotNull($result);
    }
    /**
     * Tests for `get()` method
     * @return void
     * @test
     */
    public function testGet()
    {
        $result = $this->Breadcrumb->get();
        $this->assertNull($result);

        //It only returns the "home" item (`$startText`)
        $result = $this->Breadcrumb->get(['onlyStartText' => true]);
        $expected = [
            'ul' => ['class' => 'breadcrumb'],
            'li' => ['class' => 'active'],
            'a' => [
                'href' => '/',
                'title' => 'Homepage',
            ],
            'Homepage',
            '/a',
            '/li',
            '/ul',
        ];
        $this->assertHtml($expected, $result);

        $result = $this->Breadcrumb->add('First level', '/first');
        $this->assertNull($result);

        $result = $this->Breadcrumb->get();
        $expected = [
            'ul' => ['class' => 'breadcrumb'],
            ['li' => true],
            'a' => [
                'href' => '/',
                'title' => 'Homepage',
            ],
            'Homepage',
            '/a',
            '/li',
            ['li' => ['class' => 'active']],
            'First level',
            '/li',
            '/ul',
        ];
        $this->assertHtml($expected, $result);

        //Custom `$startText`
        $result = $this->Breadcrumb->get([], 'My homepage');
        $expected = [
            'ul' => ['class' => 'breadcrumb'],
            ['li' => true],
            'a' => [
                'href' => '/',
                'title' => 'My homepage',
            ],
            'My homepage',
            '/a',
            '/li',
            ['li' => ['class' => 'active']],
            'First level',
            '/li',
            '/ul',
        ];
        $this->assertHtml($expected, $result);

        //Empty `$startText`
        $result = $this->Breadcrumb->get([], false);
        $expected = [
            'ul' => ['class' => 'breadcrumb'],
            'li' => ['class' => 'active'],
            'First level',
            '/li',
            '/ul',
        ];
        $this->assertHtml($expected, $result);

        $result = $this->Breadcrumb->add('Second level', '/second');
        $this->assertNull($result);

        $result = $this->Breadcrumb->get();
        $expected = [
            'ul' => ['class' => 'breadcrumb'],
            ['li' => true],
            ['a' => [
                'href' => '/',
                'title' => 'Homepage',
            ]],
            'Homepage',
            '/a',
            '/li',
            ['li' => true],
            ['a' => [
                'href' => '/first',
                'title' => 'First level',
            ]],
            'First level',
            '/a',
            '/li',
            ['li' => ['class' => 'active']],
            'Second level',
            '/li',
            '/ul',
        ];
        $this->assertHtml($expected, $result);

        $result = $this->Breadcrumb->get([
            'class' => 'my-class',
            'firstClass' => 'first',
            'lastClass' => 'last',
        ]);
        $expected = [
            'ul' => ['class' => 'my-class'],
            ['li' => ['class' => 'first']],
            ['a' => [
                'href' => '/',
                'title' => 'Homepage',
            ]],
            'Homepage',
            '/a',
            '/li',
            ['li' => true],
            ['a' => [
                'href' => '/first',
                'title' => 'First level',
            ]],
            'First level',
            '/a',
            '/li',
            ['li' => ['class' => 'last']],
            'Second level',
            '/li',
            '/ul',
        ];
        $this->assertHtml($expected, $result);
    }

    /**
     * Tests for `reset()` method
     * @return void
     * @test
     */
    public function testReset()
    {
        $result = $this->Breadcrumb->get();
        $this->assertNull($result);

        $result = $this->Breadcrumb->add('First level', '/first');
        $this->assertNull($result);

        $result = $this->Breadcrumb->add('Second level', '/second');
        $this->assertNull($result);

        $result = $this->Breadcrumb->get();
        $this->assertNotNull($result);

        $result = $this->Breadcrumb->reset();
        $this->assertNull($result);

        $result = $this->Breadcrumb->get();
        $this->assertNull($result);
    }
}
