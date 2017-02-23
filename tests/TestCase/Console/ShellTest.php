<?php
/**
 * This file is part of MeTools.
 *
 * MeTools is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * MeTools is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with MeTools.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @author      Mirko Pagliai <mirko.pagliai@gmail.com>
 * @copyright   Copyright (c) 2016, Mirko Pagliai for Nova Atlantis Ltd
 * @license     http://www.gnu.org/licenses/agpl.txt AGPL License
 * @link        http://git.novatlantis.it Nova Atlantis Ltd
 */
namespace MeTools\Test\TestCase\Console;

use Cake\Console\ConsoleIo;
use Cake\TestSuite\Stub\ConsoleOutput;
use Cake\TestSuite\TestCase;
use MeTools\Console\Shell;
use Reflection\ReflectionTrait;

/**
 * ShellTest class
 */
class ShellTest extends TestCase
{
    use ReflectionTrait;

    /**
     * @var \MeTools\Console\Shell
     */
    protected $Shell;

    /**
     * @var \Cake\TestSuite\Stub\ConsoleOutput
     */
    protected $err;

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

        $this->out = new ConsoleOutput();
        $this->err = new ConsoleOutput();
        $io = new ConsoleIo($this->out, $this->err);
        $io->level(2);

        $this->Shell = $this->getMockBuilder(Shell::class)
            ->setMethods(['in', '_stop'])
            ->setConstructorArgs([$io])
            ->getMock();
    }

    /**
     * Teardown any static object changes and restore them
     * @return void
     */
    public function tearDown()
    {
        parent::tearDown();

        unset($this->Shell);
    }

    /**
     * Tests for `_welcome()` method
     * @test
     */
    public function testWelcome()
    {
        //Invokes the `_welcome()` method
        $this->assertNull($this->invokeMethod($this->Shell, '_welcome'));
    }

    /**
     * Tests for `copyFile()` method
     * @test
     */
    public function testCopyFile()
    {
        $source = TMP . 'example';
        $dest = TMP . 'example_copy';

        //Creates the source file
        file_put_contents($source, null);

        //Tries to copy. Source doesn't exist
        $this->assertFalse($this->Shell->copyFile(TMP . 'noExistingFile', $dest));

        //Tries to copy. Destination is not writable
        $this->assertFalse($this->Shell->copyFile($source, TMP . 'noExistingDir' . DS . 'example_copy'));

        //Now it works
        $this->assertFileNotExists($dest);
        $this->assertTrue($this->Shell->copyFile($source, $dest));
        $this->assertFileExists($dest);

        //Tries to copy. Destination already exists
        $this->assertFalse($this->Shell->copyFile($source, $dest));

        unlink($source);
        unlink($dest);

        $this->assertEquals([
            '<error>File or directory /tmp/noExistingFile not readable</error>',
            '<error>File or directory /tmp/noExistingDir not writeable</error>',
        ], $this->err->messages());

        $this->assertEquals([
            'File /tmp/example_copy has been copied',
            'File or directory /tmp/example_copy already exists',
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

        $dir = TMP . 'firstDir' . DS . 'secondDir';

        //Creates the directory
        $this->assertFileNotExists($dir);
        $this->assertTrue($this->Shell->createDir($dir));
        $this->assertFileExists($dir);
        $this->assertEquals('0777', substr(sprintf('%o', fileperms($dir)), -4));

        rmdir($dir);
        rmdir(dirname($dir));

        //Tries to create. Not writable directory
        $this->assertFalse($this->Shell->createDir(DS . 'notWritable'));

        $this->assertEquals([
            'File or directory /tmp/ already exists',
            'Created /tmp/firstDir/secondDir directory',
            'Setted permissions on /tmp/firstDir/secondDir',
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
        $tmp = TMP . 'example';

        //Creates the file
        $this->assertFileNotExists($tmp);
        $this->assertTrue($this->Shell->createFile($tmp, null));
        $this->assertFileExists($tmp);

        //Tries to create. The file already exists
        $this->assertFalse($this->Shell->createFile($tmp, null));

        unlink($tmp);

        $this->assertEquals([
            '',
            'Creating file /tmp/example',
            '<success>Wrote</success> `/tmp/example`',
            'File or directory /tmp/example already exists',
        ], $this->out->messages());

        $this->assertEmpty($this->err->messages());
    }

    /**
     * Tests for `createLink()` method
     * @test
     */
    public function testCreateLink()
    {
        $source = TMP . 'origin';
        $dest = TMP . 'example';

        file_put_contents($source, null);

        //Creates the link
        $this->assertFileNotExists($dest);
        $this->assertTrue($this->Shell->createLink($source, $dest));
        $this->assertFileExists($dest);

        //Tries to create. The link already exists
        $this->assertFalse($this->Shell->createLink($source, $dest));

        //Tries to create. Source doesn't exist
        $this->Shell->createLink(TMP . 'noExistingFile', TMP . 'target');

        //Tries to create. Destination is not writable
        $this->assertFalse($this->Shell->createLink($source, TMP . 'noExistingDir' . DS . 'example'));

        unlink($source);
        unlink($dest);

        $this->assertEquals([
            'Link /tmp/example has been created',
            'File or directory /tmp/example already exists',
        ], $this->out->messages());

        $this->assertEquals([
            '<error>File or directory /tmp/noExistingFile not readable</error>',
            '<error>File or directory /tmp/noExistingDir not writeable</error>',
        ], $this->err->messages());
    }

    /**
     * Tests for `folderChmod()` method
     * @test
     */
    public function testFolderChmod()
    {
        $folder = TMP . 'exampleDir';

        //@codingStandardsIgnoreLine
        @mkdir($folder);

        //Set chmod
        $this->assertTrue($this->Shell->folderChmod($folder, 0777));
        $this->assertEquals('0777', substr(sprintf('%o', fileperms($folder)), -4));

                //@codingStandardsIgnoreLine
        @rmdir($folder);

        //Tries to set chmod for a no existing directory
        $this->assertFalse($this->Shell->folderChmod(DS . 'noExistingDir', 0777));

        $this->assertEquals(['Setted permissions on /tmp/exampleDir'], $this->out->messages());

        $this->assertEquals([
            '<error>Failed to set permissions on /noExistingDir</error>',
        ], $this->err->messages());
    }

    /**
     * Tests for `comment()` method
     * @test
     */
    public function testComment()
    {
        $this->Shell->comment('This is a text');

        $this->assertEquals(['<comment>This is a text</comment>'], $this->out->messages());
    }

    /**
     * Tests for `question()` method
     * @test
     */
    public function testQuestion()
    {
        $this->Shell->question('This is a text');

        $this->assertEquals(['<question>This is a text</question>'], $this->out->messages());
    }

    /**
     * Tests for `warning()` method
     * @test
     */
    public function testWarning()
    {
        $this->Shell->warning('This is a text');

        $this->assertEquals(['<warning>This is a text</warning>'], $this->err->messages());
    }
}
