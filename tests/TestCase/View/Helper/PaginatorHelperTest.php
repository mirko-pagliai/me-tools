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
     * @test
     * @uses \MeTools\View\Helper\PaginatorHelper::next()
     */
    public function testNext(): void
    {
        $expected = '<li class="page-item disabled"><a class="page-link" href="#">Next</a></li>';
        $this->assertSame($expected, $this->Helper->next('Next'));

        //Using `icon` option
        $expected = '<li class="page-item disabled"><a class="page-link" href="#">Next <i class="fa fa-chevron-right"> </i></a></li>';
        $this->assertSame($expected, $this->Helper->next('Next', ['icon' => 'chevron-right']));
    }

    /**
     * @test
     * @uses \MeTools\View\Helper\PaginatorHelper::prev()
     */
    public function testPrev(): void
    {
        $expected = '<li class="page-item disabled"><a class="page-link" href="#">Previous</a></li>';
        $this->assertSame($expected, $this->Helper->prev('Previous'));

        //Using `icon` option
        $expected = '<li class="page-item disabled"><a class="page-link" href="#"><i class="fa fa-chevron-left"> </i> Previous</a></li>';
        $this->assertSame($expected, $this->Helper->prev('Previous', ['icon' => 'chevron-left']));
    }
}
