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
use Cake\Filesystem\Folder;
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
     * Setup the test case, backup the static object values so they can be
     * restored. Specifically backs up the contents of Configure and paths in
     *  App if they have not already been backed up
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
        $this->exampleFiles = [
            $this->exampleDir . DS . 'example1',
            $this->exampleDir . DS . 'example2',
        ];

        //@codingStandardsIgnoreLine
        @mkdir($this->exampleDir);
    }

    /**
     * Teardown any static object changes and restore them
     * @return void
     */
    public function tearDown()
    {
        parent::tearDown();

        list($dirs, $files) = ((new Folder($this->exampleDir))->tree());

        foreach ($files as $file) {
            //@codingStandardsIgnoreLine
            @unlink($file);
        }

        foreach ($dirs as $dir) {
            //@codingStandardsIgnoreLine
            @rmdir($dir);
        }
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
        //Creates the source file
        file_put_contents($this->exampleFiles[0], null);

        //Tries to copy. Source doesn't exist
        $this->assertFalse($this->Shell->copyFile(TMP . 'noExistingFile', $this->exampleFiles[0]));

        //Tries to copy. Destination is not writable
        $this->assertFalse($this->Shell->copyFile($this->exampleFiles[0], TMP . 'noExistingDir' . DS . 'example_copy'));

        //Now it works
        $this->assertTrue($this->Shell->copyFile($this->exampleFiles[0], $this->exampleFiles[1]));
        $this->assertFileExists($this->exampleFiles[1]);

        //Tries to copy. Destination already exists
        $this->assertFalse($this->Shell->copyFile($this->exampleFiles[0], $this->exampleFiles[1]));

        $this->assertEquals([
            '<error>File or directory ' . TMP . 'noExistingFile not readable</error>',
            '<error>File or directory ' . TMP . 'noExistingDir not writeable</error>',
        ], $this->err->messages());
        $this->assertEquals([
            'File ' . $this->exampleFiles[1] . ' has been copied',
            'File or directory ' . $this->exampleFiles[1] . ' already exists',
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
        $this->assertEquals('0777', substr(sprintf('%o', fileperms($dir)), -4));

        //Tries to create. Not writable directory
        $this->assertFalse($this->Shell->createDir(DS . 'notWritable'));

        $this->assertEquals([
            'File or directory ' . TMP . ' already exists',
            'Created ' . $dir . ' directory',
            'Setted permissions on ' . $dir,
        ], $this->out->messages());

        $this->assertEquals([
            '<error>Failed to create file or directory /notWritable</error>',
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
            'File or directory ' . $this->exampleFiles[0] . ' already exists',
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

        //Tries to create. The link already exists
        $this->assertFalse($this->Shell->createLink($this->exampleFiles[0], $this->exampleFiles[1]));

        //Tries to create. Source doesn't exist
        $this->Shell->createLink(TMP . 'noExistingFile', TMP . 'target');

        //Tries to create. Destination is not writable
        $this->assertFalse($this->Shell->createLink($this->exampleFiles[0], TMP . 'noExistingDir' . DS . 'example'));

        $this->assertEquals([
            'Link ' . $this->exampleFiles[1] . ' has been created',
            'File or directory ' . $this->exampleFiles[1] . ' already exists',
        ], $this->out->messages());
        $this->assertEquals([
            '<error>File or directory ' . TMP . 'noExistingFile not readable</error>',
            '<error>File or directory ' . TMP . 'noExistingDir not writeable</error>',
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
        $this->assertEquals('0777', substr(sprintf('%o', fileperms($this->exampleDir)), -4));

        //Tries to set chmod for a no existing directory
        $this->assertFalse($this->Shell->folderChmod(DS . 'noExistingDir', 0777));

        $this->assertEquals(['Setted permissions on ' . $this->exampleDir], $this->out->messages());
        $this->assertEquals([
            '<error>Failed to set permissions on /noExistingDir</error>',
        ], $this->err->messages());
    }

    /**
     * Tests for `out()` method (`comment()`, `question()` and `warning()`)
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
