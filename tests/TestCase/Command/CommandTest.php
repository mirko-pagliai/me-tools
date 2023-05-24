<?php
/** @noinspection PhpUnhandledExceptionInspection */
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
namespace MeTools\Test\TestCase\Console;

use Cake\Console\ConsoleIo;
use Cake\Console\TestSuite\StubConsoleOutput;
use MeTools\Command\Command;
use MeTools\TestSuite\CommandTestCase;
use Tools\Filesystem;

/**
 * CommandTest class
 */
class CommandTest extends CommandTestCase
{
    /**
     * @var \Cake\Console\ConsoleIo&\PHPUnit\Framework\MockObject\MockObject
     */
    protected $io;

    /**
     * Called before every test method
     * @return void
     */
    protected function setUp(): void
    {
        $this->Command = $this->getMockForAbstractClass(Command::class);

        $this->_out = new StubConsoleOutput();
        $this->_err = new StubConsoleOutput();
        $this->io = $this->getMockBuilder(ConsoleIo::class)
            ->setConstructorArgs([$this->_out, $this->_err, null, null])
            ->addMethods(['in'])
            ->getMock();
        $this->io->level(ConsoleIo::VERBOSE);

        parent::setUp();
    }

    /**
     * Called after every test method
     * @return void
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        Filesystem::rmdirRecursive(TMP . 'exampleDir');
    }

    /**
     * @test
     * @uses \MeTools\Command\Command::copyFile()
     */
    public function testCopyFile(): void
    {
        $source = TMP . 'exampleDir' . DS . 'source';
        $dest = TMP . 'exampleDir' . DS . 'dest';
        Filesystem::createFile($source);

        //Tries to copy. Source doesn't exist, then destination is not writable
        $this->assertFalse($this->Command->copyFile($this->io, TMP . 'noExistingFile', $dest));
        $this->assertFalse($this->Command->copyFile($this->io, $source, TMP . 'noExistingDir' . DS . 'example_copy'));
        $this->assertErrorContains('File or directory `' . TMP . 'noExistingFile` is not readable');
        $this->assertErrorContains('File or directory `' . TMP . 'noExistingDir` is not writable');

        //Now it works
        $this->assertTrue($this->Command->copyFile($this->io, $source, $dest));
        $this->assertFileExists($dest);
        $this->assertOutputContains('File `' . $dest . '` has been copied');

        //Tries to copy. Destination already exists
        $this->assertFalse($this->Command->copyFile($this->io, $source, $dest));
        $this->assertOutputContains('File or directory `' . $dest . '` already exists');
    }

    /**
     * @test
     * @uses \MeTools\Command\Command::folderChmod()
     */
    public function testFolderChmod(): void
    {
        $dir = TMP . 'exampleDir';
        Filesystem::instance()->mkdir($dir);

        //Set chmod
        $this->assertTrue($this->Command->folderChmod($this->io, $dir));
        $this->assertDirectoryIsWritable($dir);
        $this->assertOutputContains('Set permissions on `' . $dir . '`');

        //Tries to set chmod for a no existing directory
        $this->assertFalse($this->Command->folderChmod($this->io, DS . 'noExistingDir'));
        $this->assertErrorContains('Failed to set permissions on `' . DS . 'noExistingDir`');
    }

    /**
     * @test
     * @uses \MeTools\Command\Command::isVerbose()
     */
    public function testIsVerbose(): void
    {
        $this->assertTrue($this->Command->isVerbose($this->io));

        $this->io->level(ConsoleIo::NORMAL);
        $this->assertFalse($this->Command->isVerbose($this->io));
    }
}
