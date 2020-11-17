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
namespace MeTools\Test\TestCase\Command\Install;

use Cake\Console\ConsoleIo;
use Cake\Core\Configure;
use MeTools\Command\Install\CreateDirectoriesCommand;
use MeTools\TestSuite\ConsoleIntegrationTestTrait;
use MeTools\TestSuite\TestCase;

/**
 * CreateDirectoriesCommandTest class
 */
class CreateDirectoriesCommandTest extends TestCase
{
    use ConsoleIntegrationTestTrait;

    /**
     * Tests for `execute()` method
     * @test
     */
    public function testExecute()
    {
        $io = new ConsoleIo();
        $Command = $this->getMockBuilder(CreateDirectoriesCommand::class)
            ->setMethods(['createDir'])
            ->getMock();

        $dirs = Configure::read('WRITABLE_DIRS');
        $method = $Command->expects($this->exactly(count($dirs)))->method('createDir');
        $consecutiveCalls = array_map(function (string $path) use ($io) {
            return [$io, $path];
        }, $dirs);
        call_user_func_array([$method, 'withConsecutive'], $consecutiveCalls);

        $this->assertNull($Command->run([], $io));
    }
}
