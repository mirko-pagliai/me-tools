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

use Cake\View\View;
use MeTools\TestSuite\TestCase;
use MeTools\View\Helper\BreadcrumbsHelper;

/**
 * MeTools\View\Helper\BreadcrumbsHelper Test Case
 */
class BreadcrumbsHelperTest extends TestCase
{
    /**
     * @var \MeTools\View\Helper\BreadcrumbsHelper
     */
    protected $Breadcrumbs;

    /**
     * Setup the test case, backup the static object values so they can be
     * restored. Specifically backs up the contents of Configure and paths in
     *  App if they have not already been backed up
     * @return void
     */
    public function setUp()
    {
        parent::setUp();

        $this->Breadcrumbs = new BreadcrumbsHelper(new View);
    }

    /**
     * Tests for `add()` method
     * @test
     */
    public function testAdd()
    {
        $this->Breadcrumbs->add('First', '/');
        $this->Breadcrumbs->add('Second', '/', ['class' => 'custom-class']);

        $expected = [
            'ul' => ['class' => 'breadcrumb'],
            ['li' => ['class' => 'breadcrumb-item']],
            'a' => ['href' => '/'],
            'First',
            '/a',
            '/li',
            ['li' => ['class' => 'breadcrumb-item custom-class']],
            'span' => [],
            'Second',
            '/span',
            '/li',
            '/ul',
        ];
        $this->assertHtml($expected, $this->Breadcrumbs->render());
    }

    /**
     * Tests for `prepend()` method
     * @test
     */
    public function testPrepend()
    {
        $this->Breadcrumbs->add('Second', '/');
        $this->Breadcrumbs->prepend('First', '/');

        $expected = [
            'ul' => ['class' => 'breadcrumb'],
            ['li' => ['class' => 'breadcrumb-item']],
            'a' => ['href' => '/'],
            'First',
            '/a',
            '/li',
            ['li' => ['class' => 'breadcrumb-item']],
            'span' => [],
            'Second',
            '/span',
            '/li',
            '/ul',
        ];
        $this->assertHtml($expected, $this->Breadcrumbs->render());
    }

    /**
     * Tests for `render()` method
     * @test
     */
    public function testRender()
    {
        $this->assertSame('', $this->Breadcrumbs->render());

        $this->Breadcrumbs->add('First', '/');
        $this->Breadcrumbs->add('Second', '/');

        $expected = [
            'ul' => ['class' => 'breadcrumb'],
            ['li' => ['class' => 'breadcrumb-item']],
            'a' => ['href' => '/'],
            'First',
            '/a',
            '/li',
            ['li' => ['class' => 'breadcrumb-item']],
            'span' => [],
            'Second',
            '/span',
            '/li',
            '/ul',
        ];
        $this->assertHtml($expected, $this->Breadcrumbs->render());
    }
}
