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
use MeTools\TestSuite\TestCase;

/**
 * InstallShellTest class
 */
class InstallShellTest extends TestCase
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
        //@codingStandardsIgnoreLine
        @unlink(WWW_ROOT . 'me_tools');

        $this->out = new ConsoleOutput;
        $this->err = new ConsoleOutput;
        $this->io = new ConsoleIo($this->out, $this->err);
        $this->io->level(2);

        $this->InstallShell = $this->getMockBuilder(InstallShell::class)
            ->setMethods(['in', '_stop'])
            ->setConstructorArgs([$this->io])
            ->getMock();
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
            [WWW_ROOT . 'robots.txt', TMP . 'invalid.json', APP . 'composer.json']
        ) as $file) {
            if (basename($file) !== 'empty') {
                //@codingStandardsIgnoreLine
                @unlink($file);
            }
        }
    }

    /**
     * Test for `__construct()` method
     * @test
     */
    public function testConstruct()
    {
        $this->assertNotEmpty($this->getProperty($this->InstallShell, 'fonts'));
        $this->assertNotEmpty($this->getProperty($this->InstallShell, 'links'));
        $this->assertNotEmpty($this->getProperty($this->InstallShell, 'paths'));
    }

    /**
     * Test for `all()` method
     * @test
     */
    public function testAll()
    {
        //Gets all methods from `InstallShell`, except for the `all()` method
        $methods = array_diff(getChildMethods(InstallShell::class), ['all']);

        $this->InstallShell = $this->getMockBuilder(InstallShell::class)
            ->setMethods(array_merge(['_stop', 'in'], $methods))
            ->setConstructorArgs([$this->io])
            ->getMock();

        $this->InstallShell->method('in')->will($this->returnValue('y'));

        //Sets a callback for each method
        foreach ($methods as $method) {
            $this->InstallShell->method($method)
                ->will($this->returnCallback(function () use ($method) {
                    $this->out->write($method);
                }));
        }

        //Calls with `force` options
        $this->InstallShell->params['force'] = true;
        $this->InstallShell->all();

        $expectedMethodsCalledInOrder = [
            'createDirectories',
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

        $this->assertEquals($expectedMethodsCalledInOrder, $this->out->messages());
        $this->assertEmpty($this->err->messages());
    }

    /**
     * Tests for `copyFonts()` method
     * @test
     */
    public function testCopyFonts()
    {
        $this->InstallShell->copyFonts();

        $this->assertEquals([
            'Link ' . rtr(WWW_ROOT) . 'fonts/fontawesome-webfont.eot has been created',
            'Link ' . rtr(WWW_ROOT) . 'fonts/fontawesome-webfont.ttf has been created',
            'Link ' . rtr(WWW_ROOT) . 'fonts/fontawesome-webfont.woff has been created',
            'Link ' . rtr(WWW_ROOT) . 'fonts/fontawesome-webfont.woff2 has been created',
        ], $this->out->messages());
        $this->assertEmpty($this->err->messages());
    }

    /**
     * Tests for `createDirectories()` method
     * @test
     */
    public function testCreateDirectories()
    {
        $this->InstallShell->createDirectories();

        foreach ([
            TMP . 'cache' . DS . 'models',
            TMP . 'cache' . DS . 'persistent',
            TMP . 'cache' . DS . 'views',
            TMP . 'cache',
            WWW_ROOT . 'files',
        ] as $dir) {
            //@codingStandardsIgnoreLine
            @rmdir($dir);
        }

        $this->assertEquals([
            'File or directory ' . LOGS . ' already exists',
            'File or directory ' . TMP . ' already exists',
            'Created ' . TMP . 'cache directory',
            'Setted permissions on ' . TMP . 'cache',
            'Created ' . TMP . 'cache' . DS . 'models directory',
            'Setted permissions on ' . TMP . 'cache' . DS . 'models',
            'Created ' . TMP . 'cache' . DS . 'persistent directory',
            'Setted permissions on ' . TMP . 'cache' . DS . 'persistent',
            'Created ' . TMP . 'cache' . DS . 'views directory',
            'Setted permissions on ' . TMP . 'cache' . DS . 'views',
            'File or directory ' . TMP . 'sessions already exists',
            'File or directory ' . TMP . 'tests already exists',
            'Created ' . rtr(WWW_ROOT) . 'files directory',
            'Setted permissions on ' . rtr(WWW_ROOT) . 'files',
            'File or directory ' . rtr(WWW_ROOT) . 'fonts already exists',
            'File or directory ' . rtr(WWW_ROOT) . 'vendor already exists',
        ], $this->out->messages());
        $this->assertEmpty($this->err->messages());
    }

    /**
     * Tests for `createRobots()` method
     * @test
     */
    public function testCreateRobots()
    {
        $this->InstallShell->createRobots();
        $this->assertFileExists(WWW_ROOT . 'robots.txt');

        $this->assertEquals(
            file_get_contents(WWW_ROOT . 'robots.txt'),
            'User-agent: *' . PHP_EOL . 'Disallow: /admin/' . PHP_EOL .
            'Disallow: /ckeditor/' . PHP_EOL . 'Disallow: /css/' . PHP_EOL .
            'Disallow: /js/' . PHP_EOL . 'Disallow: /vendor/'
        );

        $this->assertNotEmpty($this->out->messages());
        $this->assertEmpty($this->err->messages());
    }

    /**
     * Tests for `createPluginsLinks()` method
     * @test
     */
    public function testCreatePluginsLinks()
    {
        $this->InstallShell->createPluginsLinks();

        $this->assertFileExists(WWW_ROOT . 'me_tools');

        $this->assertEquals([
            '',
            'Skipping plugin Assets. It does not have webroot folder.',
            '',
            'For plugin: MeTools',
            '---------------------------------------------------------------',
            'Created symlink ' . WWW_ROOT . 'me_tools',
            'Done',
        ], $this->out->messages());
        $this->assertEmpty($this->err->messages());
    }

    /**
     * Tests for `createVendorsLinks()` method
     * @test
     */
    public function testCreateVendorsLinks()
    {
        $this->InstallShell->createVendorsLinks();

        $this->assertEquals([
            'Link ' . rtr(WWW_ROOT) . 'vendor/bootstrap-datetimepicker has been created',
            'Link ' . rtr(WWW_ROOT) . 'vendor/jquery has been created',
            'Link ' . rtr(WWW_ROOT) . 'vendor/moment has been created',
            'Link ' . rtr(WWW_ROOT) . 'vendor/font-awesome has been created',
            'Link ' . rtr(WWW_ROOT) . 'vendor/fancybox has been created',
            'Link ' . rtr(WWW_ROOT) . 'vendor/bootstrap has been created',
        ], $this->out->messages());
        $this->assertEmpty($this->err->messages());
    }

    /**
     * Test for `fixComposerJson()` method
     * @test
     */
    public function testFixComposerJson()
    {
        //Tries to fix a no existing file
        $this->InstallShell->fixComposerJson('noExisting');

        //Writes an invalid composer.json file
        $file = TMP . 'invalid.json';
        file_put_contents($file, 'String');

        //Tries to fix an invalid file
        $this->InstallShell->fixComposerJson($file);

        //Writes `tests/test_app/composer.json` file
        $file = APP . 'composer.json';

        $json = [
            'name' => 'example',
            'description' => 'example of composer.json',
            'type' => 'project',
            'require' => ['php' => '>=5.5.9'],
            'autoload' => ['psr-4' => ['App' => 'src']],
        ];
        file_put_contents($file, json_encode($json, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));

        //Fixes the file
        $this->InstallShell->fixComposerJson($file);

        //Tries to fix the main `composer.json` file
        $this->InstallShell->fixComposerJson();

        $this->assertEquals([
            'The file ' . rtr(APP) . 'composer.json has been fixed',
            'The file ' . rtr(ROOT . DS . 'composer.json') . ' doesn\'t need to be fixed',
        ], $this->out->messages());
        $this->assertEquals([
            '<error>File or directory noExisting not writeable</error>',
            '<error>The file ' . TMP . 'invalid.json does not seem a valid composer.json file</error>',
        ], $this->err->messages());
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

        $this->InstallShell->method('all')
            ->will($this->returnCallback(function () {
                $this->out->write('all');
            }));

        $this->InstallShell->main();

        $this->assertEquals(['all'], $this->out->messages());
        $this->assertEmpty($this->err->messages());
    }

    /**
     * Test for `setPermissions()` method
     * @test
     */
    public function testSetPermissions()
    {
        //Resets the `paths` property, removing some values
        $paths = array_diff(array_unique($this->InstallShell->paths), [
            TMP,
            WWW_ROOT . 'fonts',
            WWW_ROOT . 'vendor',
        ]);
        $this->setProperty($this->InstallShell, 'paths', $paths);

        $this->InstallShell->setPermissions();

        $this->assertEquals([
            'Setted permissions on ' . LOGS,
            'Setted permissions on ' . TMP . 'sessions',
            'Setted permissions on ' . TMP . 'tests',
        ], $this->out->messages());
        $this->assertEquals([
            '<error>Failed to set permissions on ' . TMP . 'cache</error>',
            '<error>Failed to set permissions on ' . TMP . 'cache/models</error>',
            '<error>Failed to set permissions on ' . TMP . 'cache/persistent</error>',
            '<error>Failed to set permissions on ' . TMP . 'cache/views</error>',
            '<error>Failed to set permissions on ' . rtr(WWW_ROOT) . 'files</error>',
        ], $this->err->messages());
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
            'copyFonts',
            'createDirectories',
            'createPluginsLinks',
            'createRobots',
            'createVendorsLinks',
            'fixComposerJson',
            'setPermissions',
        ], $parser->subcommands());
        $this->assertEquals('Executes some tasks to make the system ready to work', $parser->getDescription());
        $this->assertArrayKeysEqual(['force', 'help', 'quiet', 'verbose'], $parser->options());
    }
}
