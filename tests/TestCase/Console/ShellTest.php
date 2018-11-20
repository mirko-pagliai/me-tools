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
use MeTools\TestSuite\ConsoleIntegrationTestCase;
use MeTools\TestSuite\Traits\MockTrait;
use MeTools\TestSuite\Traits\TestCaseTrait;

/**
 * ShellTest class
 */
class ShellTest extends ConsoleIntegrationTestCase
{
    use MockTrait;
    use TestCaseTrait;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    protected $Shell;

    /**
     * @var string
     */
    protected $exampleDir = TMP . 'exampleDir';

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

        safe_mkdir($this->exampleDir);

        $this->_out = new ConsoleOutput;
        $this->_err = new ConsoleOutput;

        $this->Shell = $this->getMockForShell(Shell::class, ['in', '_stop']);
        $this->Shell->setIo(new ConsoleIo($this->_out, $this->_err));
        $this->Shell->getIo()->level(2);
    }

    /**
     * Called after every test method
     * @return void
     */
    public function tearDown()
    {
        safe_rmdir_recursive($this->exampleDir);

        parent::tearDown();
    }

    /**
     * Tests for `__call()` method
     * @test
     */
    public function testMagicCall()
    {
        $this->Shell->comment('Test');
        $this->Shell->question('Test');
        $this->Shell->warning('Test');
        $this->assertOutputContains('<comment>Test</comment>');
        $this->assertOutputContains('<question>Test</question>');
        $this->assertErrorContains('<warning>Test</warning>');
    }

    /**
     * Tests for `__call()` method with a no existing method
     * @expectedException BadMethodCallException
     * @expectedExceptionMessage  The `noExisting` method does not exist
     * @test
     */
    public function testMagicCallNoExistingMethod()
    {
        $this->Shell->noExisting();
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

        $this->assertOutputContains('File `' . $dest . '` has been copied');
        $this->assertOutputContains('File or directory `' . $dest . '` already exists');
        $this->assertErrorContains('File or directory `' . TMP . 'noExistingFile` is not readable');
        $this->assertErrorContains('File or directory `' . TMP . 'noExistingDir` is not writable');
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

        $this->assertOutputContains('File or directory `' . TMP . '` already exists');
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
        $this->assertFalse($this->Shell->createDir(DS . 'notWritable'));
        $this->assertOutputEmpty();
        $this->assertErrorContains('Failed to create file or directory `/notWritable`');
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

        $this->assertOutputContains('Creating file ' . $this->exampleFiles[0]);
        $this->assertOutputContains('<success>Wrote</success> `' . $this->exampleFiles[0] . '`');
        $this->assertOutputContains('File or directory `' . $this->exampleFiles[0] . '` already exists');
        $this->assertErrorEmpty();
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

        $this->assertOutputContains('Link `' . $this->exampleFiles[1] . '` has been created');
        $this->assertOutputContains('File or directory `' . $this->exampleFiles[1] . '` already exists');
        $this->assertErrorContains('File or directory `' . TMP . 'noExistingFile` is not readable');
        $this->assertErrorContains('File or directory `' . TMP . 'noExistingDir` is not writable');
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

        $this->assertOutputContains('Setted permissions on `' . $this->exampleDir . '`');
        $this->assertErrorContains('Failed to set permissions on `' . DS . 'noExistingDir`');
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
}
