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
use MeTools\Console\Command;
use MeTools\TestSuite\ConsoleIntegrationTestTrait;
use MeTools\TestSuite\TestCase;
use Tools\Filesystem;

/**
 * CommandTest class
 */
class CommandTest extends TestCase
{
    use ConsoleIntegrationTestTrait;

    /**
     * @var \Cake\Console\ConsoleIo
     */
    protected $io;

    /**
     * Called before every test method
     * @return void
     */
    protected function setUp(): void
    {
        $this->Command = $this->getMockForAbstractClass(Command::class);

        parent::setUp();

        $this->_out = new StubConsoleOutput();
        $this->_err = new StubConsoleOutput();
        $this->io = $this->getMockBuilder(ConsoleIo::class)
            ->setConstructorArgs([$this->_out, $this->_err, null, null])
            ->addMethods(['in'])
            ->getMock();
        $this->io->level(ConsoleIo::VERBOSE);
    }

    /**
     * Called after every test method
     * @return void
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        Filesystem::instance()->rmdirRecursive(TMP . 'exampleDir');
    }

    /**
     * Tests for `copyFile()` method
     * @test
     */
    public function testCopyFile(): void
    {
        $source = TMP . 'exampleDir' . DS . 'source';
        $dest = TMP . 'exampleDir' . DS . 'dest';
        Filesystem::instance()->createFile($source);

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
     * Tests for `createDir()` method
     * @uses \MeTools\Console\Command::createDir()
     * @test
     */
    public function testCreateDir(): void
    {
        //Tries to create. Directory already exists
        $this->assertFalse($this->Command->createDir($this->io, TMP));
        $this->assertOutputContains('File or directory `' . Filesystem::instance()->rtr(TMP) . '` already exists');

        //Creates the directory
        $dir = TMP . 'exampleDir' . DS . 'firstDir' . DS . 'secondDir';
        $this->assertTrue($this->Command->createDir($this->io, $dir));
        $this->assertFileExists($dir);
        $this->assertDirectoryIsWritable($dir);
        $this->assertOutputContains('Created `' . $dir . '` directory');
        $this->assertOutputContains('Set permissions on `' . $dir . '`');

        $this->assertErrorEmpty();
    }

    /**
     * Tests for `createDir()` method, with a not writable directory
     * @requires OS Linux
     * @uses \MeTools\Console\Command::createDir()
     * @test
     */
    public function testCreateDirNotWritableDir(): void
    {
        $this->assertFalse($this->Command->createDir($this->io, DS . 'notWritable'));
        $this->assertOutputEmpty();
        $this->assertErrorContains('Failed to create file or directory `/notWritable` with message: permission denied');
    }

    /**
     * Tests for `createFile()` method
     * @uses \MeTools\Console\Command::createDir()
     * @test
     */
    public function testCreateFile(): void
    {
        $source = TMP . 'exampleDir' . DS . 'example';
        if (!file_exists(dirname($source))) {
            mkdir(dirname($source), 0777, true);
        }

        //Creates the file
        $this->assertTrue($this->Command->createFile($this->io, $source, 'test'));
        $this->assertFileExists($source);
        $this->assertOutputContains('Creating file ' . $source);
        $this->assertOutputContains('<success>Wrote</success> `' . $source . '`');

        //Tries to create. The file already exists
        $this->assertFalse($this->Command->createFile($this->io, $source, 'test'));
        $this->assertOutputContains('File or directory `' . $source . '` already exists');
        $this->assertErrorEmpty();
    }

    /**
     * Tests for `createLink()` method
     * @requires OS Linux
     * @test
     */
    public function testCreateLink(): void
    {
        $source = TMP . 'exampleDir' . DS . 'source';
        $dest = TMP . 'exampleDir' . DS . 'dest';
        Filesystem::instance()->createFile($source);

        //Creates the link
        $this->assertTrue($this->Command->createLink($this->io, $source, $dest));
        $this->assertFileExists($dest);
        $this->assertOutputContains('Link `' . $dest . '` has been created');

        //Tries to create. The link already exists, the source doesn't exist, then the destination is not writable
        $this->assertFalse($this->Command->createLink($this->io, $source, $dest));
        $this->assertFalse($this->Command->createLink($this->io, TMP . 'noExistingFile', TMP . 'target'));
        $this->assertFalse($this->Command->createLink($this->io, $source, TMP . 'noExistingDir' . DS . 'example'));
        $this->assertOutputContains('File or directory `' . $dest . '` already exists');
        $this->assertErrorContains('File or directory `' . TMP . 'noExistingFile` is not readable');
        $this->assertErrorContains('File or directory `' . TMP . 'noExistingDir` is not writable');
    }

    /**
     * Tests for `folderChmod()` method
     * @test
     */
    public function testFolderChmod(): void
    {
        $dir = TMP . 'exampleDir';
        if (!file_exists($dir)) {
            mkdir($dir, 0777, true);
        }

        //Set chmod
        $this->assertTrue($this->Command->folderChmod($this->io, $dir));
        $this->assertDirectoryIsWritable($dir);
        $this->assertOutputContains('Set permissions on `' . $dir . '`');

        //Tries to set chmod for a no existing directory
        $this->assertFalse($this->Command->folderChmod($this->io, DS . 'noExistingDir'));
        $this->assertErrorContains('Failed to set permissions on `' . DS . 'noExistingDir`');
    }
}
