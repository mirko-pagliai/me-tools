<?php
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
use MeTools\TestSuite\ConsoleIntegrationTestCase;

/**
 * CommandTest class
 */
class CommandTest extends ConsoleIntegrationTestCase
{
    /**
     * @var array
     */
    protected $exampleFiles = [
        TMP . 'exampleDir' . DS . 'example1',
        TMP . 'exampleDir' . DS . 'example2',
    ];

    /**
     * Called before every test method
     * @return void
     */
    public function setUp()
    {
        parent::setUp();

        $this->_out = new ConsoleOutput;
        $this->_err = new ConsoleOutput;
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
    public function tearDown()
    {
        parent::tearDown();

        safe_rmdir_recursive(dirname(first_value($this->exampleFiles)));
    }

    /**
     * Tests for `copyFile()` method
     * @test
     */
    public function testCopyFile()
    {
        list($source, $dest) = $this->exampleFiles;
        safe_mkdir(dirname($source), 0777, true);
        file_put_contents($source, null);

        //Tries to copy. Source doesn't exist, then destination is not writable
        $this->assertFalse($this->Shell->copyFile($this->io, TMP . 'noExistingFile', $dest));
        $this->assertFalse($this->Shell->copyFile($this->io, $source, TMP . 'noExistingDir' . DS . 'example_copy'));
        $this->assertErrorContains('File or directory `' . TMP . 'noExistingFile` is not readable');
        $this->assertErrorContains('File or directory `' . TMP . 'noExistingDir` is not writable');

        //Now it works
        $this->assertTrue($this->Shell->copyFile($this->io, $source, $dest));
        $this->assertFileExists($dest);
        $this->assertOutputContains('File `' . $dest . '` has been copied');

        //Tries to copy. Destination already exists
        $this->assertFalse($this->Shell->copyFile($this->io, $source, $dest));
        $this->assertOutputContains('File or directory `' . $dest . '` already exists');
    }

    /**
     * Tests for `createDir()` method
     * @test
     */
    public function testCreateDir()
    {
        //Tries to create. Directory already exists
        $this->assertFalse($this->Shell->createDir($this->io, TMP));
        $this->assertOutputContains('File or directory `' . TMP . '` already exists');

        //Creates the directory
        $dir = dirname(first_value($this->exampleFiles)) . DS . 'firstDir' . DS . 'secondDir';
        $this->assertTrue($this->Shell->createDir($this->io, $dir));
        $this->assertFileExists($dir);
        $this->assertFilePerms($dir, '0777');
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
        $this->assertFalse($this->Shell->createDir($this->io, DS . 'notWritable'));
        $this->assertOutputEmpty();
        $this->assertErrorContains('Failed to create file or directory `/notWritable`');
    }

    /**
     * Tests for `createFile()` method
     * @test
     */
    public function testCreateFile()
    {
        list($source) = $this->exampleFiles;
        safe_mkdir(dirname($source), 0777, true);

        //Creates the file
        $this->assertTrue($this->Shell->createFile($this->io, $source, 'test'));
        $this->assertFileExists($source);
        $this->assertOutputContains('Creating file ' . $source);
        $this->assertOutputContains('<success>Wrote</success> `' . $source . '`');

        //Tries to create. The file already exists
        $this->assertFalse($this->Shell->createFile($this->io, $source, 'test'));
        $this->assertOutputContains('File or directory `' . $source . '` already exists');
        $this->assertErrorEmpty();
    }

    /**
     * Tests for `createLink()` method
     * @test
     */
    public function testCreateLink()
    {
        list($source, $dest) = $this->exampleFiles;
        safe_mkdir(dirname($source), 0777, true);
        file_put_contents($source, null);

        //Creates the link
        $this->assertTrue($this->Shell->createLink($this->io, $source, $dest));
        $this->assertFileExists($dest);
        $this->assertOutputContains('Link `' . $dest . '` has been created');

        //Tries to create. The link already exists, the source doesn't exist, then the destination is not writable
        $this->assertFalse($this->Shell->createLink($this->io, $source, $dest));
        $this->assertFalse($this->Shell->createLink($this->io, TMP . 'noExistingFile', TMP . 'target'));
        $this->assertFalse($this->Shell->createLink($this->io, $source, TMP . 'noExistingDir' . DS . 'example'));
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
        $dir = dirname(first_value($this->exampleFiles));
        safe_mkdir($dir, 0777, true);

        //Set chmod
        $this->assertTrue($this->Shell->folderChmod($this->io, $dir, 0777));
        $this->assertFilePerms($dir, '0777');
        $this->assertOutputContains('Setted permissions on `' . $dir . '`');

        //Tries to set chmod for a no existing directory
        $this->assertFalse($this->Shell->folderChmod($this->io, DS . 'noExistingDir', 0777));
        $this->assertErrorContains('Failed to set permissions on `' . DS . 'noExistingDir`');
    }
}
