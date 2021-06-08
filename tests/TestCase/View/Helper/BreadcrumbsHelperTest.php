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

use MeTools\TestSuite\HelperTestCase;

/**
 * MeTools\View\Helper\BreadcrumbsHelper Test Case
 */
class BreadcrumbsHelperTest extends HelperTestCase
{
    /**
     * Tests for `add()` method
     * @test
     */
    public function testAdd(): void
    {
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
        $this->Helper->add('First', '/');
        $this->Helper->add('Second', '/', ['class' => 'custom-class']);
        $this->assertHtml($expected, $this->Helper->render());
    }

    /**
     * Tests for `prepend()` method
     * @test
     */
    public function testPrepend(): void
    {
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
        $this->Helper->add('Second', '/');
        $this->Helper->prepend('First', '/');
        $this->assertHtml($expected, $this->Helper->render());
    }

    /**
     * Tests for `render()` method
     * @test
     */
    public function testRender(): void
    {
        $this->assertSame('', $this->Helper->render());

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
        $this->Helper->add('First', '/');
        $this->Helper->add('Second', '/');
        $this->assertHtml($expected, $this->Helper->render());
    }
}
