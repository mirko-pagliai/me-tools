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
 * PaginatorHelperTest class
 * @property \MeTools\View\Helper\PaginatorHelper $Helper
 */
class PaginatorHelperTest extends HelperTestCase
{
    /**
     * Tests for `next()` method
     * @test
     */
    public function testNext(): void
    {
        $expected = [
            'li' => ['class' => 'next page-item disabled'],
            'a' => ['class' => 'page-link', 'href' => '', 'onclick' => 'return false;'],
            'Next',
            '/a',
            '/li',
        ];
        $this->assertHtml($expected, $this->Helper->next('Next'));

        //Using `icon` option
        $expected = [
            'li' => ['class' => 'next page-item disabled'],
            'a' => ['class' => 'page-link', 'href' => '', 'onclick' => 'return false;'],
            'Next',
            ' ',
            'i' => ['class' => 'fas fa-chevron-right'],
            ' ',
            '/i',
            '/a',
            '/li',
        ];
        $this->assertHtml($expected, $this->Helper->next('Next', ['icon' => 'chevron-right']));
    }

    /**
     * Tests for `prev()` method
     * @test
     */
    public function testPrev(): void
    {
        $expected = [
            'li' => ['class' => 'prev page-item disabled'],
            'a' => ['class' => 'page-link', 'href' => '', 'onclick' => 'return false;'],
            'Previous',
            '/a',
            '/li',
        ];
        $this->assertHtml($expected, $this->Helper->prev('Previous'));

        //Using `icon` option
        $expected = [
            'li' => ['class' => 'prev page-item disabled'],
            'a' => ['class' => 'page-link', 'href' => '', 'onclick' => 'return false;'],
            'i' => ['class' => 'fas fa-chevron-left'],
            ' ',
            '/i',
            ' ',
            'Previous',
            '/a',
            '/li',
        ];
        $this->assertHtml($expected, $this->Helper->prev('Previous', ['icon' => 'chevron-left']));
    }
}
