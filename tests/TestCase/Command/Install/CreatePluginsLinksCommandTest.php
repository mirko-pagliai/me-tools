<?php
declare(strict_types=1);
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
namespace MeTools\Test\TestCase\Command\Install;

use MeTools\TestSuite\ConsoleIntegrationTestTrait;
use MeTools\TestSuite\TestCase;

/**
 * CreatePluginsLinksCommandTest class
 */
class CreatePluginsLinksCommandTest extends TestCase
{
    use ConsoleIntegrationTestTrait;

    /**
     * Tests for `execute()` method
     * @test
     */
    public function testExecute()
    {
        $this->loadPlugins(['TestPlugin']);

        @array_map(IS_WIN ? 'rmdir_recursive' : 'unlink', [WWW_ROOT . 'me_tools', WWW_ROOT . 'test_plugin']);
        $this->exec('me_tools.create_plugins_links');
        $this->assertExitWithSuccess();
        $this->assertOutputEmpty();
        $this->assertErrorEmpty();
        $this->assertFileExists(WWW_ROOT . 'me_tools');
        $this->assertFileExists(WWW_ROOT . 'test_plugin');

        @array_map(IS_WIN ? 'rmdir_recursive' : 'unlink', [WWW_ROOT . 'me_tools', WWW_ROOT . 'test_plugin']);
        $this->exec('me_tools.create_plugins_links -v');
        $this->assertExitWithSuccess();
        $this->assertOutputContains('Skipping plugin `Assets`. It does not have webroot folder');
        $this->assertOutputContains('For plugin: MeTools');
        $this->assertOutputContains('Link `' . rtr(WWW_ROOT . 'me_tools') . '` has been created');
        $this->assertOutputContains('For plugin: TestPlugin');
        $this->assertOutputContains('Link `' . rtr(WWW_ROOT . 'test_plugin') . '` has been created');
        $this->assertErrorEmpty();
        $this->assertFileExists(WWW_ROOT . 'me_tools');
        $this->assertFileExists(WWW_ROOT . 'test_plugin');

        //Already exist
        $this->exec('me_tools.create_plugins_links -v');
        $this->assertExitWithSuccess();
        $this->assertOutputContains('File or directory `' . rtr(WWW_ROOT . 'me_tools') . '` already exists');
        $this->assertOutputContains('File or directory `' . rtr(WWW_ROOT . 'test_plugin') . '` already exists');
        $this->assertErrorEmpty();

        @array_map(IS_WIN ? 'rmdir_recursive' : 'unlink', [WWW_ROOT . 'me_tools', WWW_ROOT . 'test_plugin']);
        $this->removePlugins(['TestPlugin']);
    }
}
