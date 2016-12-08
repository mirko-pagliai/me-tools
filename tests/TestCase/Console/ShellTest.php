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
use MeTools\Console\Shell as BaseShell;

/**
 * Makes public some protected methods/properties from `Shell`
 */
class Shell extends BaseShell
{
    public function welcome()
    {
        return parent::_welcome();
    }
}

/**
 * ShellTest class
 */
class ShellTest extends TestCase
{
    /**
     * @var \Shell
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
        $this->assertNull($this->Shell->welcome());
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

        $error = $this->err->messages();
        $this->assertEquals(2, count($error));
        $this->assertEquals('<error>File or directory /tmp/noExistingFile not readable</error>', $error[0]);
        $this->assertEquals('<error>File /tmp/noExistingDir/example_copy has not been copied</error>', $error[1]);

        //Now it works
        $this->assertFileNotExists($dest);
        $this->assertTrue($this->Shell->copyFile($source, $dest));
        $this->assertFileExists($dest);

        //Tries to copy. Destination already exists
        $this->assertFalse($this->Shell->copyFile($source, $dest));

        $output = $this->out->messages();
        $this->assertEquals(2, count($output));
        $this->assertEquals('File /tmp/example_copy has been copied', $output[0]);
        $this->assertEquals('File or directory /tmp/example_copy already exists', $output[1]);

        unlink($source);
        unlink($dest);
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

        $output = $this->out->messages();
        $this->assertEquals(3, count($output));

        //Tries to create. The file already exists
        $this->assertFalse($this->Shell->createFile($tmp, null));

        $output = $this->out->messages();
        $this->assertEquals(4, count($output));
        $this->assertEquals('File or directory /tmp/example already exists', $output[3]);

        unlink($tmp);
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

        $output = $this->out->messages();
        $this->assertEquals(1, count($output));
        $this->assertEquals('File or directory /tmp/example already exists', $output[0]);

        //Tries to create. Source doesn't exist
        $this->Shell->createLink(TMP . 'noExistingFile', TMP . 'target');

        $error = $this->err->messages();
        $this->assertEquals(1, count($error));
        $this->assertEquals('<error>File or directory /tmp/noExistingFile not readable</error>', $error[0]);

        //Tries to create. Destination is not writable
        $this->assertFalse($this->Shell->createLink($source, TMP . 'noExistingDir' . DS . 'example'));

        $error = $this->err->messages();
        $this->assertEquals(2, count($error));
        $this->assertEquals('<error>File or directory /tmp/noExistingDir not writeable</error>', $error[1]);

        unlink($source);
        unlink($dest);
    }

    /**
     * Tests for `comment()` method
     * @test
     */
    public function testComment()
    {
        $this->Shell->comment('This is a text');

        $output = $this->out->messages();
        $this->assertEquals(1, count($output));
        $this->assertEquals('<comment>This is a text</comment>', $output[0]);
    }

    /**
     * Tests for `question()` method
     * @test
     */
    public function testQuestion()
    {
        $this->Shell->question('This is a text');

        $output = $this->out->messages();
        $this->assertEquals(1, count($output));
        $this->assertEquals('<question>This is a text</question>', $output[0]);
    }

    /**
     * Tests for `warning()` method
     * @test
     */
    public function testWarning()
    {
        $this->Shell->warning('This is a text');

        $error = $this->err->messages();
        $this->assertEquals(1, count($error));
        $this->assertEquals('<warning>This is a text</warning>', $error[0]);
    }
}
