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
     * @var \Cake\Console\ConsoleIo
     */
    protected $io;

    /**
     * @var \MeTools\Test\TestCase\Console\Shell
     */
    protected $Shell;

    /**
     * Setup the test case, backup the static object values so they can be
     * restored. Specifically backs up the contents of Configure and paths in
     *  App if they have not already been backed up
     * @return void
     */
    public function setUp()
    {
        parent::setUp();

        $this->io = $this->getMockBuilder('Cake\Console\ConsoleIo')
            ->disableOriginalConstructor()
            ->getMock();
        $this->Shell = new Shell($this->io);
    }

    /**
     * Teardown any static object changes and restore them
     * @return void
     */
    public function tearDown()
    {
        parent::tearDown();

        unset($this->Shell, $this->io);
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
        $copy = TMP . 'example_copy';

        file_put_contents($source, 'Test');

        $this->Shell->params = ['verbose' => true];

        $this->io->expects($this->once())
            ->method('verbose')
            ->with('File /tmp/example_copy has been copied', 1);

        $this->assertFileNotExists($copy);
        $this->assertTrue($this->Shell->copyFile($source, $copy));
        $this->assertFileExists($copy);

        unlink($source);
        unlink($copy);
    }

    /**
     * Tests for `createFile()` method
     * @test
     */
    public function testCreateFile()
    {
        $tmp = TMP . 'example';

        if (file_exists($tmp)) {
            unlink($tmp);
        }

        $this->assertTrue($this->Shell->createFile($tmp, null));
        $this->assertFileExists($tmp);

        unlink($tmp);
    }

    /**
     * Tests for `createFile()` method, using a file that already exists
     * @test
     */
    public function testCreateFileAlreadyExists()
    {
        $tmp = TMP . 'example';

        file_put_contents($tmp, null);

        $this->Shell->params = ['verbose' => true];

        $this->io->expects($this->once())
            ->method('verbose')
            ->with('File or directory /tmp/example already exists', 1);

        $this->assertFalse($this->Shell->createFile($tmp, null));

        unlink($tmp);
    }

    /**
     * Tests for `createLink()` method
     * @test
     */
    public function testCreateLink()
    {
        $origin = TMP . 'origin';
        $target = TMP . 'example';

        file_put_contents($origin, null);

        if (file_exists($target)) {
            unlink($target);
        }

        $this->assertTrue($this->Shell->createLink($origin, $target));
        $this->assertFileExists($target);

        unlink($origin);
        unlink($target);
    }

    /**
     * Tests for `createLink()` method, using a file that already exists
     * @test
     */
    public function testCreateLinkAlreadyExists()
    {
        $origin = TMP . 'origin';
        $target = TMP . 'example';

        file_put_contents($origin, null);
        file_put_contents($target, null);

        $this->Shell->params = ['verbose' => true];

        $this->io->expects($this->once())
            ->method('verbose')
            ->with('File or directory /tmp/example already exists', 1);

        $this->assertFalse($this->Shell->createLink($origin, $target));

        unlink($origin);
        unlink($target);
    }

    /**
     * Tests for `createLink()` method, using a no existing file
     * @test
     */
    public function testCreateLinkFileNoExisting()
    {
        $this->io->expects($this->once())
            ->method('err')
            ->with('<error>File or directory /tmp/noExistingFile not readable</error>', 1);

        $this->Shell->createLink(TMP . 'noExistingFile', TMP . 'target');
    }

    /**
     * Tests for `createLink()` method, using a no existing directory
     * @test
     */
    public function testCreateLinkNoExistingDir()
    {
        $origin = TMP . 'origin';
        $target = TMP . 'noExistingDir' . DS . 'example';

        file_put_contents($origin, null);

        $this->io->expects($this->once())
            ->method('err')
            ->with('<error>File or directory /tmp/noExistingDir not writeable</error>', 1);

        $this->assertFalse($this->Shell->createLink($origin, $target));

        unlink($origin);
    }

    /**
     * Tests for `comment()` method
     * @test
     */
    public function testComment()
    {
        $this->io->expects($this->once())
            ->method('out')
            ->with('<comment>This is a text</comment>', 1);

        $this->Shell->comment('This is a text');
    }

    /**
     * Tests for `question()` method
     * @test
     */
    public function testQuestion()
    {
        $this->io->expects($this->once())
            ->method('out')
            ->with('<question>This is a text</question>', 1);

        $this->Shell->question('This is a text');
    }

    /**
     * Tests for `warning()` method
     * @test
     */
    public function testWarning()
    {
        $this->io->expects($this->once())
            ->method('err')
            ->with('<warning>This is a text</warning>', 1);

        $this->Shell->warning('This is a text');
    }
}
