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
use Cake\TestSuite\TestCase;
use MeTools\Shell\InstallShell;
use Reflection\ReflectionTrait;

/**
 * InstallShellTest class
 */
class InstallShellTest extends TestCase
{
    use ReflectionTrait;

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

        $this->out = new ConsoleOutput();
        $this->err = new ConsoleOutput();
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

        //Deletes all fonts and vendors
        foreach (array_merge(
            glob(WWW_ROOT . 'fonts' . DS . '*'),
            glob(WWW_ROOT . 'vendor' . DS . '*')
        ) as $file) {
            if (basename($file) !== 'empty') {
                //@codingStandardsIgnoreLine
                @unlink($file);
            }
        }

        unset($this->InstallShell, $this->io, $this->err, $this->out);
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
            'Link tests/test_app/webroot/fonts/fontawesome-webfont.eot has been created',
            'Link tests/test_app/webroot/fonts/fontawesome-webfont.ttf has been created',
            'Link tests/test_app/webroot/fonts/fontawesome-webfont.woff has been created',
            'Link tests/test_app/webroot/fonts/fontawesome-webfont.woff2 has been created',
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
            rmdir($dir);
        }

        $this->assertEquals([
            'File or directory /tmp/ already exists',
            'File or directory /tmp/ already exists',
            'Created /tmp/cache directory',
            'Setted permissions on /tmp/cache',
            'Created /tmp/cache/models directory',
            'Setted permissions on /tmp/cache/models',
            'Created /tmp/cache/persistent directory',
            'Setted permissions on /tmp/cache/persistent',
            'Created /tmp/cache/views directory',
            'Setted permissions on /tmp/cache/views',
            'File or directory /tmp/sessions already exists',
            'File or directory /tmp/tests already exists',
            'Created tests/test_app/webroot/files directory',
            'Setted permissions on tests/test_app/webroot/files',
            'File or directory tests/test_app/webroot/fonts already exists',
            'File or directory tests/test_app/webroot/vendor already exists',
        ], $this->out->messages());

        $this->assertEmpty($this->err->messages());
    }

    /**
     * Tests for `createRobots()` method
     * @test
     */
    public function testCreateRobots()
    {
        $this->assertFileNotExists(WWW_ROOT . 'robots.txt');
        $this->InstallShell->createRobots();
        $this->assertFileExists(WWW_ROOT . 'robots.txt');

        $this->assertEquals(
            file_get_contents(WWW_ROOT . 'robots.txt'),
            'User-agent: *' . PHP_EOL . 'Disallow: /admin/' . PHP_EOL .
            'Disallow: /ckeditor/' . PHP_EOL . 'Disallow: /css/' . PHP_EOL .
            'Disallow: /js/' . PHP_EOL . 'Disallow: /vendor/'
        );

        unlink(WWW_ROOT . 'robots.txt');

        $this->assertNotEmpty($this->out->messages());
        $this->assertEmpty($this->err->messages());
    }

    /**
     * Tests for `createPluginsLinks()` method
     * @test
     */
    public function testCreatePluginsLinks()
    {
        $this->assertFileNotExists(WWW_ROOT . 'me_tools');

        $this->InstallShell->createPluginsLinks();

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

        $this->assertFileExists(WWW_ROOT . 'me_tools');
    }

    /**
     * Tests for `createVendorsLinks()` method
     * @test
     */
    public function testCreateVendorsLinks()
    {
        $this->InstallShell->createVendorsLinks();

        $this->assertEquals([
            'Link tests/test_app/webroot/vendor/bootstrap-datetimepicker has been created',
            'Link tests/test_app/webroot/vendor/jquery has been created',
            'Link tests/test_app/webroot/vendor/moment has been created',
            'Link tests/test_app/webroot/vendor/font-awesome has been created',
            'Link tests/test_app/webroot/vendor/fancybox has been created',
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

        unlink($file);

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

        unlink($file);

        //Tries to fix the main `composer.json` file
        $this->InstallShell->fixComposerJson();

        $this->assertEquals([
            'The file tests/test_app/composer.json has been fixed',
            'The file ' . rtr(ROOT . DS . 'composer.json') . ' doesn\'t need to be fixed',
        ], $this->out->messages());

        $this->assertEquals([
            '<error>File or directory noExisting not writeable</error>',
            '<error>The file /tmp/invalid.json does not seem a valid composer.json file</error>',
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
            'Setted permissions on /tmp/sessions',
            'Setted permissions on /tmp/tests',
        ], $this->out->messages());

        $this->assertEquals([
            '<error>Failed to set permissions on /tmp/cache</error>',
            '<error>Failed to set permissions on /tmp/cache/models</error>',
            '<error>Failed to set permissions on /tmp/cache/persistent</error>',
            '<error>Failed to set permissions on /tmp/cache/views</error>',
            '<error>Failed to set permissions on tests/test_app/webroot/files</error>',
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
        $this->assertEquals([
            'all',
            'copyFonts',
            'createDirectories',
            'createPluginsLinks',
            'createRobots',
            'createVendorsLinks',
            'fixComposerJson',
            'setPermissions',
        ], array_keys($parser->subcommands()));
        $this->assertEquals('Executes some tasks to make the system ready to work', $parser->getDescription());
        $this->assertEquals(['force', 'help', 'quiet', 'verbose'], array_keys($parser->options()));
    }
}
