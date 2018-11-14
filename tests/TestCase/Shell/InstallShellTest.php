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

use Cake\Console\ConsoleOptionParser;
use Cake\Http\BaseApplication;
use Cake\Utility\Inflector;
use MeTools\Shell\InstallShell;
use MeTools\TestSuite\ConsoleIntegrationTestCase;
use MeTools\TestSuite\Traits\MockTrait;

/**
 * InstallShellTest class
 */
class InstallShellTest extends ConsoleIntegrationTestCase
{
    use MockTrait;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    protected $Shell;

    /**
     * @var array
     */
    protected $debug;

    /**
     * Called before every test method
     * @return void
     */
    public function setUp()
    {
        parent::setUp();

        $app = $this->getMockForAbstractClass(BaseApplication::class, ['']);
        $app->addPlugin('MeTools')->pluginBootstrap();

        //Deletes symbolic links for plugin assets
        safe_unlink(WWW_ROOT . 'me_tools');

        $this->Shell = $this->getMockForShell(InstallShell::class);
    }

    /**
     * Called after every test method
     * @return void
     */
    public function tearDown()
    {
        safe_unlink_recursive(WWW_ROOT . 'vendor', 'empty');
        safe_unlink(WWW_ROOT . 'robots.txt');
        safe_unlink(TMP . 'invalid.json');
        safe_unlink(APP . 'composer.json');

        parent::tearDown();
    }

    /**
     * Test for `all()` method
     * @test
     */
    public function testAll()
    {
        //Gets all methods from `InstallShell`, except for the `all()` method
        $methods = array_diff(get_child_methods(InstallShell::class), ['all']);

        $InstallShell = $this->getMockForShell(InstallShell::class, array_merge(['_stop', 'in'], $methods));
        $InstallShell->method('in')->will($this->returnValue('y'));

        //Sets a callback for each method
        foreach ($methods as $method) {
            $InstallShell->method($method)->will($this->returnCallback(function () use ($method) {
                $this->debug[] = $method;
            }));
        }

        //Calls with `force` options
        $InstallShell->params['force'] = true;
        $InstallShell->all();

        $expectedMethodsCalledInOrder = [
            'setPermissions',
            'createRobots',
            'fixComposerJson',
            'createPluginsLinks',
            'createVendorsLinks',
        ];
        $this->assertEquals($expectedMethodsCalledInOrder, $this->debug);

        //Calls with no interactive mode
        $this->debug = [];
        unset($InstallShell->params['force']);
        $InstallShell->interactive = false;
        $InstallShell->all();
        $expectedMethodsCalledInOrder = array_merge(['createDirectories'], $expectedMethodsCalledInOrder);
        $this->assertEquals($expectedMethodsCalledInOrder, $this->debug);
    }

    /**
     * Tests for `createDirectories()` method
     * @test
     */
    public function testCreateDirectories()
    {
        foreach ([TMP, TMP . 'cache', WWW_ROOT . 'vendor'] as $path) {
            safe_mkdir($path, 0777, true);
            $pathsAlreadyExist[] = $path;
        }

        $pathsToBeCreated = array_diff($this->Shell->paths, $pathsAlreadyExist);
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

        $this->assertErrorEmpty();
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
        $this->assertErrorEmpty();
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
        $this->assertErrorEmpty();
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

        foreach ($this->Shell->links as $link) {
            $this->assertOutputContains('Link `' . rtr(WWW_ROOT) . 'vendor' . DS . $link . '` has been created');
        }

        $this->assertErrorEmpty();
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
        $this->assertErrorEmpty();

        //Tries to fix a no existing file
        $this->exec('me_tools.install fix_composer_json -p ' . TMP . 'noExisting -v');
        $this->assertExitWithError();
        $this->assertErrorContains('File or directory `' . TMP . 'noExisting` is not writable');

        //Tries to fix an invalid composer.json file
        $file = TMP . 'invalid.json';
        file_put_contents($file, 'String');
        $this->exec('me_tools.install fix_composer_json -p ' . $file . ' -v');
        $this->assertExitWithError();
        $this->assertErrorContains('The file ' . $file . ' does not seem a valid composer.json file');

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
        $this->assertErrorEmpty();
    }

    /**
     * Test for `main()` method
     * @test
     */
    public function testMain()
    {
        $InstallShell = $this->getMockForShell(InstallShell::class, ['in', '_stop', 'all']);
        $InstallShell->expects($this->once())->method('all');
        $InstallShell->main();
    }

    /**
     * Test for `setPermissions()` method
     * @test
     */
    public function testSetPermissions()
    {
        $this->exec('me_tools.install set_permissions -v');
        $this->assertExitWithSuccess();

        foreach ($this->Shell->paths as $path) {
            $this->assertOutputContains('Setted permissions on `' . rtr($path) . '`');
        }

        $this->assertErrorEmpty();
    }

    /**
     * Test for `getOptionParser()` method
     * @test
     */
    public function testGetOptionParser()
    {
        $parser = $this->Shell->getOptionParser();
        $this->assertInstanceOf(ConsoleOptionParser::class, $parser);
        $this->assertEquals('Executes some tasks to make the system ready to work', $parser->getDescription());
        $this->assertArrayKeysEqual(['force', 'help', 'quiet', 'verbose'], $parser->options());

        $expectedMethods = array_diff(get_child_methods(InstallShell::class), ['main']);
        $expectedMethods = array_values(array_map([Inflector::class, 'underscore'], $expectedMethods));
        $this->assertArrayKeysEqual($expectedMethods, $parser->subcommands());
    }
}
