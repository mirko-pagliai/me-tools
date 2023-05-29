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
use MeTools\Command\Install\SetPermissionsCommand;
use MeTools\Core\Configure;
use MeTools\TestSuite\CommandTestCase;
use Symfony\Component\Filesystem\Exception\IOException;
use Tools\Filesystem;

/**
 * SetPermissionsCommandTest class
 */
class SetPermissionsCommandTest extends CommandTestCase
{
    /**
     * @test
     * @uses \MeTools\Command\Install\SetPermissionsCommand::execute()
     */
    public function testExecute(): void
    {
        /**
         * Runs with a dir that does not exist
         */
        Configure::write('MeTools.WritableDirs', [TMP . 'noExisting']);
        $this->_out = new StubConsoleOutput();
        $this->_err = new StubConsoleOutput();
        $Command = new SetPermissionsCommand();
        $this->_exitCode = $Command->run(['-v'], new ConsoleIo($this->_out, $this->_err));
        $this->assertExitSuccess();
        $this->assertOutputContains('File or directory `' . TMP . 'noExisting` does not exist');
        $this->assertErrorEmpty();

        /**
         * Runs again and sets the permissions
         */
        $tmpFile = Filesystem::createTmpFile();
        Configure::write('MeTools.WritableDirs', [$tmpFile]);
        $this->_out = new StubConsoleOutput();
        $this->_err = new StubConsoleOutput();
        $Command = new SetPermissionsCommand();
        $this->_exitCode = $Command->run(['-v'], new ConsoleIo($this->_out, $this->_err));
        $this->assertExitSuccess();
        $this->assertOutputContains('Set permissions on `' . $tmpFile . '`');
        $this->assertErrorEmpty();

        /**
         * `Filesystem::chmod()` will throw an exception
         */
        $this->_err = new StubConsoleOutput();
        $Filesystem = $this->createPartialMock(Filesystem::class, ['chmod']);
        $Filesystem->method('chmod')->willThrowException(new IOException('Message for exception'));
        $Command = $this->createPartialMock(SetPermissionsCommand::class, ['getFilesystem']);
        $Command->method('getFilesystem')->willReturn($Filesystem);
        $this->_exitCode = $Command->run(['-v'], new ConsoleIo(null, $this->_err));
        $this->assertExitError();
        $this->assertErrorContains('Message for exception');
    }
}
