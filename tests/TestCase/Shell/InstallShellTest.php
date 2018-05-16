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
namespace MeTools\Test\TestCase\Shell;

use Cake\Console\ConsoleIo;
use Cake\TestSuite\Stub\ConsoleOutput;
use MeTools\Shell\InstallShell;
use MeTools\TestSuite\ConsoleIntegrationTestCase;

/**
 * InstallShellTest class
 */
class InstallShellTest extends ConsoleIntegrationTestCase
{
    /**
     * @var \MeTools\Shell\InstallShell
     */
    protected $InstallShell;

    /**
     * @var \Cake\TestSuite\Stub\ConsoleOutput
     */
    protected $err;

    /**
     * @var \Cake\Console\ConsoleIo
     */
    protected $io;

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

        //Deletes symbolic links for plugin assets
        safe_unlink(WWW_ROOT . 'me_tools');

        $this->out = new ConsoleOutput;
        $this->err = new ConsoleOutput;
        $this->io = new ConsoleIo($this->out, $this->err);
        $this->io->level(2);

        $this->InstallShell = new InstallShell;
    }

    /**
     * Teardown any static object changes and restore them
     * @return void
     */
    public function tearDown()
    {
        parent::tearDown();

        //Deletes all files
        foreach (array_merge(
            glob(WWW_ROOT . 'fonts' . DS . '*'),
            glob(WWW_ROOT . 'vendor' . DS . '*'),
            [
                WWW_ROOT . 'robots.txt',
                TMP . 'invalid.json',
                APP . 'composer.json',
            ]
        ) as $file) {
            if (basename($file) !== 'empty') {
                safe_unlink($file);
            }
        }
    }

    /**
     * Test for `all()` method
     * @test
     */
    public function testAll()
    {
        //Gets all methods from `InstallShell`, except for the `all()` method
        $methods = array_diff(get_child_methods(InstallShell::class), ['all']);

        $this->InstallShell = $this->getMockBuilder(InstallShell::class)
            ->setMethods(array_merge(['_stop', 'in'], $methods))
            ->setConstructorArgs([$this->io])
            ->getMock();

        $this->InstallShell->method('in')->will($this->returnValue('y'));

        //Sets a callback for each method
        foreach ($methods as $method) {
            $this->InstallShell->method($method)->will($this->returnCallback(function () use ($method) {
                $this->out->write($method);
            }));
        }

        //Calls with `force` options
        $this->InstallShell->params['force'] = true;
        $this->InstallShell->all();

        $expectedMethodsCalledInOrder = [
            'setPermissions',
            'createRobots',
            'fixComposerJson',
            'createPluginsLinks',
            'createVendorsLinks',
            'copyFonts',
        ];

        $this->assertEquals($expectedMethodsCalledInOrder, $this->out->messages());
        $this->assertEmpty($this->err->messages());

        //Resets out messages()
        $this->setProperty($this->out, '_out', []);

        //Calls with no interactive mode
        unset($this->InstallShell->params['force']);
        $this->InstallShell->interactive = false;
        $this->InstallShell->all();

        $expectedMethodsCalledInOrder = array_merge(['createDirectories'], $expectedMethodsCalledInOrder);
        $this->assertEquals($expectedMethodsCalledInOrder, $this->out->messages());
        $this->assertEmpty($this->err->messages());
    }

    /**
     * Tests for `copyFonts()` method
     * @test
     */
    public function testCopyFonts()
    {
        $files = array_map('basename', $this->InstallShell->fonts);

        $this->exec('me_tools.install copy_fonts -v');
        $this->assertExitWithSuccess();

        foreach ($files as $file) {
            $this->assertOutputContains('Link `' . rtr(WWW_ROOT) . 'fonts' . DS . $file . '` has been created');
        }

        $this->exec('me_tools.install copy_fonts -v');
        $this->assertExitWithSuccess();

        foreach ($files as $file) {
            $this->assertOutputContains('File or directory `' . rtr(WWW_ROOT) . 'fonts' . DS . $file . '` already exists');
        }
    }

    /**
     * Tests for `createDirectories()` method
     * @test
     */
    public function testCreateDirectories()
    {
        foreach ([
            TMP,
            TMP . 'cache',
            WWW_ROOT . 'fonts',
            WWW_ROOT . 'vendor',
        ] as $path) {
            safe_mkdir($path, 0777, true);
            $pathsAlreadyExist[] = $path;
        }

        $pathsToBeCreated = array_diff($this->InstallShell->paths, $pathsAlreadyExist);
        array_walk($pathsToBeCreated, 'safe_rmdir');

        $this->exec('me_tools.install create_directories -v');
        $this->assertExitWithSuccess();

        foreach ($pathsAlreadyExist as $path) {
            $this->assertOutputContains('File or directory `' . rtr($path) . '` already exists');
        }

        foreach ($pathsToBeCreated as $path) {
            $this->assertOutputContains('Created `' . rtr($path) . '` directory');
            $this->assertOutputContains('Setted permissions on `' . rtr($path) . '`');
        }
    }

    /**
     * Tests for `createRobots()` method
     * @test
     */
    public function testCreateRobots()
    {
        $this->exec('me_tools.install create_robots -v');
        $this->assertExitWithSuccess();
        $this->assertOutputContains('Creating file ' . WWW_ROOT . 'robots.txt');
        $this->assertOutputContains('<success>Wrote</success> `' . WWW_ROOT . 'robots.txt`');

        $this->assertStringEqualsFile(
            WWW_ROOT . 'robots.txt',
            'User-agent: *' . PHP_EOL . 'Disallow: /admin/' . PHP_EOL .
            'Disallow: /ckeditor/' . PHP_EOL . 'Disallow: /css/' . PHP_EOL .
            'Disallow: /js/' . PHP_EOL . 'Disallow: /vendor/'
        );
    }

    /**
     * Tests for `createPluginsLinks()` method
     * @test
     */
    public function testCreatePluginsLinks()
    {
        $this->exec('me_tools.install create_plugins_links -v');
        $this->assertExitWithSuccess();
        $this->assertOutputContains('Skipping plugin Assets. It does not have webroot folder.');
        $this->assertOutputContains('For plugin: MeTools');
        $this->assertOutputContains('Created symlink ' . WWW_ROOT . 'me_tools');
        $this->assertOutputContains('Done');
        $this->assertFileExists(WWW_ROOT . 'me_tools');
    }

    /**
     * Tests for `createVendorsLinks()` method
     * @test
     */
    public function testCreateVendorsLinks()
    {
        $this->exec('me_tools.install create_vendors_links -v');
        $this->assertExitWithSuccess();

        foreach ($this->getProperty($this->InstallShell, 'links') as $link) {
            $this->assertOutputContains('Link `' . rtr(WWW_ROOT) . 'vendor' . DS . $link . '` has been created');
        }
    }

    /**
     * Test for `fixComposerJson()` method
     * @test
     */
    public function testFixComposerJson()
    {
        //Tries to fix the main `composer.json` file
        $this->exec('me_tools.install fix_composer_json -v');
        $this->assertExitWithSuccess();
        $this->assertOutputContains('The file ' . rtr(ROOT . DS . 'composer.json') . ' doesn\'t need to be fixed');

        //Tries to fix a no existing file
        $this->exec('me_tools.install fix_composer_json -p ' . TMP . 'noExisting -v');
        $this->assertExitWithError();
        $this->assertErrorContains('<error>File or directory `' . TMP . 'noExisting` is not writable</error>');

        //Tries to fix an invalid composer.json file
        $file = TMP . 'invalid.json';
        file_put_contents($file, 'String');
        $this->exec('me_tools.install fix_composer_json -p ' . $file . ' -v');
        $this->assertExitWithError();
        $this->assertErrorContains('<error>The file ' . $file . ' does not seem a valid composer.json file</error>');

        //Fixes a valid composer.json file
        $file = APP . 'composer.json';
        file_put_contents($file, json_encode([
            'name' => 'example',
            'description' => 'example of composer.json',
            'type' => 'project',
            'require' => ['php' => '>=5.5.9'],
            'autoload' => ['psr-4' => ['App' => 'src']],
        ], JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
        $this->exec('me_tools.install fix_composer_json -p ' . $file . ' -v');
        $this->assertExitWithSuccess();
        $this->assertOutputContains('The file ' . rtr($file) . ' has been fixed');
    }

    /**
     * Test for `main()` method
     * @test
     */
    public function testMain()
    {
        $this->InstallShell = $this->getMockBuilder(InstallShell::class)
            ->setMethods(['in', '_stop', 'all'])
            ->setConstructorArgs([$this->io])
            ->getMock();

        $this->InstallShell->expects($this->once())->method('all');

        $this->InstallShell->main();
    }

    /**
     * Test for `setPermissions()` method
     * @test
     */
    public function testSetPermissions()
    {
        $this->exec('me_tools.install set_permissions -v');
        $this->assertExitWithSuccess();

        foreach ($this->InstallShell->paths as $path) {
            $this->assertOutputContains('Setted permissions on `' . rtr($path) . '`');
        }
    }

    /**
     * Test for `getOptionParser()` method
     * @test
     */
    public function testGetOptionParser()
    {
        $parser = $this->InstallShell->getOptionParser();

        $this->assertInstanceOf('Cake\Console\ConsoleOptionParser', $parser);
        $this->assertArrayKeysEqual([
            'all',
            'copy_fonts',
            'create_directories',
            'create_plugins_links',
            'create_robots',
            'create_vendors_links',
            'fix_composer_json',
            'set_permissions',
        ], $parser->subcommands());
        $this->assertEquals('Executes some tasks to make the system ready to work', $parser->getDescription());
        $this->assertArrayKeysEqual(['force', 'help', 'quiet', 'verbose'], $parser->options());
    }
}
