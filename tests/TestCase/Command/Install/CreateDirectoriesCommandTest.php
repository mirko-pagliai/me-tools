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
use Cake\Console\TestSuite\StubConsoleOutput;
use Cake\Core\Configure;
use MeTools\Command\Install\CreateDirectoriesCommand;
use MeTools\TestSuite\CommandTestCase;
use Symfony\Component\Filesystem\Exception\IOException;
use Tools\Filesystem;

/**
 * CreateDirectoriesCommandTest class
 */
class CreateDirectoriesCommandTest extends CommandTestCase
{
    /**
     * @test
     * @uses \MeTools\Command\Install\CreateDirectoriesCommand::execute()
     */
    public function testExecute(): void
    {
        $this->exec('me_tools.create_directories -v');
        $this->assertExitSuccess();
        foreach (Configure::read('MeTools.WritableDirs') as $expectedDir) {
            $this->assertOutputContains('File or directory `' . rtr($expectedDir) . '` already exists');
        }
        $this->assertErrorEmpty();

        $this->_out = new StubConsoleOutput();
        $this->_err = new StubConsoleOutput();
        $Command = $this->createPartialMock(CreateDirectoriesCommand::class, ['verboseIfFileExists']);
        $Command->method('verboseIfFileExists')->willReturn(false);
        $this->_exitCode = $Command->run(['-v'], new ConsoleIo($this->_out, $this->_err));
        $this->assertExitSuccess();
        foreach (Configure::read('MeTools.WritableDirs') as $expectedDir) {
            $this->assertOutputContains('Created `' . rtr($expectedDir) . '` directory');
        }
        $this->assertErrorEmpty();

        $this->_err = new StubConsoleOutput();
        $Filesystem = $this->createPartialMock(Filesystem::class, ['mkdir']);
        $Filesystem->method('mkdir')->willThrowException(new IOException('Message for exception'));
        $Command = $this->createPartialMock(CreateDirectoriesCommand::class, ['getFilesystem', 'verboseIfFileExists']);
        $Command->method('verboseIfFileExists')->willReturn(false);
        $Command->method('getFilesystem')->willReturn($Filesystem);
        $this->_exitCode = $Command->run(['-v'], new ConsoleIo(null, $this->_err));
        $this->assertExitError();
        $this->assertErrorContains('Message for exception');
    }
}
