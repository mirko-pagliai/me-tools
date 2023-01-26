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

use MeTools\Command\Install\CreateDirectoriesCommand;
use MeTools\TestSuite\CommandTestCase;
use MeTools\TestSuite\TestCase;

/**
 * CommandTestCaseTest class
 */
class CommandTestCaseTest extends TestCase
{
    /**
     * @test
     * @uses \MeTools\TestSuite\CommandTestCase::__get()
     */
    public function testGetMagicMethod(): void
    {
        $CommandTestCase = $this->getMockForAbstractClass(CommandTestCase::class, [], '', true, true, true, ['getOriginClassNameOrFail']);
        $CommandTestCase->method('getOriginClassNameOrFail')->willReturn(CreateDirectoriesCommand::class);
        $this->assertInstanceOf(CreateDirectoriesCommand::class, $CommandTestCase->Command);
    }
}
