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

use MeTools\Controller\Component\FlashComponent;
use MeTools\TestSuite\ComponentTestCase;
use MeTools\TestSuite\TestCase;

/**
 * ComponentTestCaseTest class
 */
class ComponentTestCaseTest extends TestCase
{
    /**
     * @test
     * @uses \MeTools\TestSuite\ComponentTestCase::__get()
     */
    public function testGetMagicMethod(): void
    {
        $ComponentTestCase = $this->getMockForAbstractClass(ComponentTestCase::class, [], '', true, true, true, ['getOriginClassNameOrFail']);
        $ComponentTestCase->method('getOriginClassNameOrFail')->willReturn(FlashComponent::class);
        $this->assertInstanceOf(FlashComponent::class, $ComponentTestCase->Component);
    }
}
