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
namespace MeTools\Test\TestCase\TestSuite;

use MeTools\TestSuite\HelperTestCase;
use MeTools\TestSuite\TestCase;
use MeTools\View\Helper\HtmlHelper;

/**
 * HelperTestCaseTest class
 */
class HelperTestCaseTest extends TestCase
{
    /**
     * @test
     * @uses \MeTools\TestSuite\HelperTestCase::__get()
     */
    public function testGetMagicMethod(): void
    {
        $HelperTestCase = $this->getMockForAbstractClass(HelperTestCase::class, [], '', true, true, true, ['getOriginClassName']);
        $HelperTestCase->method('getOriginClassName')->willReturn(HtmlHelper::class);
        $this->assertInstanceOf(HtmlHelper::class, $HelperTestCase->Helper);
    }
}
