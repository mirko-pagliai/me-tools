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
namespace MeTools\Test\TestCase\Console;

use Cake\Console\ConsoleIo;
use Cake\TestSuite\Stub\ConsoleOutput;
use MeTools\TestSuite\ConsoleIntegrationTestTrait;
use MeTools\TestSuite\TestCase;

/**
 * CommandTest class
 */
class CommandTest extends TestCase
{
    use ConsoleIntegrationTestTrait;

    /**
     * @var \Cake\TestSuite\Stub\ConsoleOutput
     */
    protected $_err;

    /**
     * @var \Cake\TestSuite\Stub\ConsoleOutput
     */
    protected $_out;

    /**
     * @var \Cake\Console\ConsoleIo
     */
    protected $io;

    /**
     * Called before every test method
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->Command = $this->getMockBuilder($this->getOriginClassName($this))
                ->setMethods(null)
                ->getMock();
        $this->_out = new ConsoleOutput();
        $this->_err = new ConsoleOutput();
        $this->io = $this->getMockBuilder(ConsoleIo::class)
            ->setConstructorArgs([$this->_out, $this->_err, null, null])
            ->setMethods(['in'])
            ->getMock();
        $this->io->level(ConsoleIo::VERBOSE);
    }

    /**
     * Called after every test method
     * @return void
     */
    public function tearDown(): void
    {
        parent::tearDown();

        rmdir_recursive(TMP . 'exampleDir');
    }

    /**
     * Tests for `copyFile()` method
     * @test
     */
    public function testCopyFile()
    {
        $source = TMP . 'exampleDir' . DS . 'source';
        $dest = TMP . 'exampleDir' . DS . 'dest';
        create_file($source);

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
     * @test
     */
    public function testCreateDir()
    {
        //Tries to create. Directory already exists
        $this->assertFalse($this->Command->createDir($this->io, TMP));
        $this->assertOutputContains('File or directory `' . rtr(TMP) . '` already exists');

        //Creates the directory
        $dir = TMP . 'exampleDir' . DS . 'firstDir' . DS . 'secondDir';
        $this->assertTrue($this->Command->createDir($this->io, $dir));
        $this->assertFileExists($dir);
        $this->assertFilePerms('0777', $dir);
        $this->assertOutputContains('Created `' . $dir . '` directory');
        $this->assertOutputContains('Setted permissions on `' . $dir . '`');

        $this->assertErrorEmpty();
    }

    /**
     * Tests for `createDir()` method, with a not writable directory
     * @group onlyUnix
     * @test
     */
    public function testCreateDirNotWritableDir()
    {
        $this->assertFalse($this->Command->createDir($this->io, DS . 'notWritable'));
        $this->assertOutputEmpty();
        $this->assertErrorContains('Failed to create file or directory `/notWritable`');
    }

    /**
     * Tests for `createFile()` method
     * @test
     */
    public function testCreateFile()
    {
        $source = TMP . 'exampleDir' . DS . 'example';
        @mkdir(dirname($source), 0777, true);

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
     * @group onlyUnix
     * @test
     */
    public function testCreateLink()
    {
        $source = TMP . 'exampleDir' . DS . 'source';
        $dest = TMP . 'exampleDir' . DS . 'dest';
        create_file($source);

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
    public function testFolderChmod()
    {
        $dir = TMP . 'exampleDir';
        @mkdir($dir, 0777, true);

        //Set chmod
        $this->assertTrue($this->Command->folderChmod($this->io, $dir, 0777));
        $this->assertFilePerms('0777', $dir);
        $this->assertOutputContains('Setted permissions on `' . $dir . '`');

        //Tries to set chmod for a no existing directory
        $this->assertFalse($this->Command->folderChmod($this->io, DS . 'noExistingDir', 0777));
        $this->assertErrorContains('Failed to set permissions on `' . DS . 'noExistingDir`');
    }
}
