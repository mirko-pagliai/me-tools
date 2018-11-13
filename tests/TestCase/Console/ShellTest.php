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
use MeTools\Console\Shell;
use MeTools\TestSuite\TestCase;

/**
 * ShellTest class
 */
class ShellTest extends TestCase
{
    /**
     * @var \MeTools\Console\Shell
     */
    protected $Shell;

    /**
     * @var \Cake\TestSuite\Stub\ConsoleOutput
     */
    protected $err;

    /**
     * @var string
     */
    protected $exampleDir;

    /**
     * @var array
     */
    protected $exampleFiles;

    /**
     * @var \Cake\TestSuite\Stub\ConsoleOutput
     */
    protected $out;

    /**
     * Called before every test method
     * @return void
     */
    public function setUp()
    {
        parent::setUp();

        $this->out = new ConsoleOutput;
        $this->err = new ConsoleOutput;
        $io = new ConsoleIo($this->out, $this->err);
        $io->level(2);

        $this->Shell = $this->getMockBuilder(Shell::class)
            ->setMethods(['in', '_stop'])
            ->setConstructorArgs([$io])
            ->getMock();

        $this->exampleDir = TMP . 'exampleDir';
        safe_mkdir($this->exampleDir);

        $this->exampleFiles[0] = $this->exampleDir . DS . 'example1';
        $this->exampleFiles[1] = $this->exampleDir . DS . 'example2';
    }

    /**
     * Called after every test method
     * @return void
     */
    public function tearDown()
    {
        parent::tearDown();

        safe_rmdir_recursive($this->exampleDir);
    }

    /**
     * Tests for `_welcome()` method
     * @test
     */
    public function testWelcome()
    {
        $this->assertNull($this->invokeMethod($this->Shell, '_welcome'));
    }

    /**
     * Tests for `copyFile()` method
     * @test
     */
    public function testCopyFile()
    {
        list($source, $dest) = $this->exampleFiles;

        //Creates the source file
        file_put_contents($source, null);

        //Tries to copy. Source doesn't exist, then destination is not writable
        $this->assertFalse($this->Shell->copyFile(TMP . 'noExistingFile', $dest));
        $this->assertFalse($this->Shell->copyFile($source, TMP . 'noExistingDir' . DS . 'example_copy'));

        //Now it works
        $this->assertTrue($this->Shell->copyFile($source, $dest));
        $this->assertFileExists($dest);

        //Tries to copy. Destination already exists
        $this->assertFalse($this->Shell->copyFile($source, $dest));

        $this->assertEquals([
            '<error>File or directory `' . TMP . 'noExistingFile` is not readable</error>',
            '<error>File or directory `' . TMP . 'noExistingDir` is not writable</error>',
        ], $this->err->messages());
        $this->assertEquals([
            'File `' . $dest . '` has been copied',
            'File or directory `' . $dest . '` already exists',
        ], $this->out->messages());
    }

    /**
     * Tests for `createDir()` method
     * @test
     */
    public function testCreateDir()
    {
        //Tries to create. Directory already exists
        $this->assertFalse($this->Shell->createDir(TMP));

        $dir = $this->exampleDir . DS . 'firstDir' . DS . 'secondDir';

        //Creates the directory
        $this->assertTrue($this->Shell->createDir($dir));
        $this->assertFileExists($dir);
        $this->assertFilePerms($dir, '0777');

        $this->assertEquals([
            'File or directory `' . TMP . '` already exists',
            'Created `' . $dir . '` directory',
            'Setted permissions on `' . $dir . '`',
        ], $this->out->messages());
        $this->assertEmpty($this->err->messages());
    }

    /**
     * Tests for `createDir()` method, with a not writable directory
     * @group onlyUnix
     * @test
     */
    public function testCreateDirNotWritableDir()
    {
        $this->assertFalse($this->Shell->createDir(DS . 'notWritable'));
        $this->assertEmpty($this->out->messages());
        $this->assertEquals([
            '<error>Failed to create file or directory `/notWritable`</error>',
        ], $this->err->messages());
    }

    /**
     * Tests for `createFile()` method
     * @test
     */
    public function testCreateFile()
    {
        //Creates the file
        $this->assertTrue($this->Shell->createFile($this->exampleFiles[0], null));
        $this->assertFileExists($this->exampleFiles[0]);

        //Tries to create. The file already exists
        $this->assertFalse($this->Shell->createFile($this->exampleFiles[0], null));

        $this->assertEquals([
            '',
            'Creating file ' . $this->exampleFiles[0],
            '<success>Wrote</success> `' . $this->exampleFiles[0] . '`',
            'File or directory `' . $this->exampleFiles[0] . '` already exists',
        ], $this->out->messages());
        $this->assertEmpty($this->err->messages());
    }

    /**
     * Tests for `createLink()` method
     * @test
     */
    public function testCreateLink()
    {
        file_put_contents($this->exampleFiles[0], null);

        //Creates the link
        $this->assertTrue($this->Shell->createLink($this->exampleFiles[0], $this->exampleFiles[1]));
        $this->assertFileExists($this->exampleFiles[1]);

        //Tries to create. The link already exists, the source doesn't exist, the the destination is not writable
        $this->assertFalse($this->Shell->createLink($this->exampleFiles[0], $this->exampleFiles[1]));
        $this->assertFalse($this->Shell->createLink(TMP . 'noExistingFile', TMP . 'target'));
        $this->assertFalse($this->Shell->createLink($this->exampleFiles[0], TMP . 'noExistingDir' . DS . 'example'));

        $this->assertEquals([
            'Link `' . $this->exampleFiles[1] . '` has been created',
            'File or directory `' . $this->exampleFiles[1] . '` already exists',
        ], $this->out->messages());
        $this->assertEquals([
            '<error>File or directory `' . TMP . 'noExistingFile` is not readable</error>',
            '<error>File or directory `' . TMP . 'noExistingDir` is not writable</error>',
        ], $this->err->messages());
    }

    /**
     * Tests for `folderChmod()` method
     * @test
     */
    public function testFolderChmod()
    {
        //Set chmod
        $this->assertTrue($this->Shell->folderChmod($this->exampleDir, 0777));
        $this->assertFilePerms($this->exampleDir, '0777');

        //Tries to set chmod for a no existing directory
        $this->assertFalse($this->Shell->folderChmod(DS . 'noExistingDir', 0777));

        $this->assertEquals(['Setted permissions on `' . $this->exampleDir . '`'], $this->out->messages());
        $this->assertEquals([
            '<error>Failed to set permissions on `' . DS . 'noExistingDir`</error>',
        ], $this->err->messages());
    }

    /**
     * Tests for `hasParam()` method
     * @test
     */
    public function testHasParam()
    {
        $this->Shell->params = [
            'false' => false,
            'null' => null,
            'string' => 'string',
            'true' => true,
        ];

        foreach (array_keys($this->Shell->params) as $param) {
            $this->assertTrue($this->Shell->hasParam($param));
        }

        $this->assertFalse($this->Shell->hasParam('noExisting'));
    }

    /**
     * Tests for `out()` methods (`comment()`, `question()` and `warning()`)
     * @test
     */
    public function testOutMethods()
    {
        foreach (['comment', 'question', 'warning'] as $method) {
            $this->Shell->$method('Test');
        }

        $this->assertEquals([
            '<comment>Test</comment>',
            '<question>Test</question>',
        ], $this->out->messages());
        $this->assertEquals(['<warning>Test</warning>'], $this->err->messages());
    }
}
