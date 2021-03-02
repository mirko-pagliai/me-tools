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
use Tools\Filesystem;

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
        $clear = function (): void {
            $method = IS_WIN ? [Filesystem::instance(), 'rmdirRecursive'] : 'unlink';
            @array_map($method, [WWW_ROOT . 'me_tools', WWW_ROOT . 'test_plugin']);
        };

        $this->loadPlugins(['TestPlugin']);

        $clear();
        $this->exec('me_tools.create_plugins_links -v');
        $this->assertExitWithSuccess();
        $this->assertOutputContains('Skipping plugin `Assets`. It does not have webroot folder');
        $this->assertOutputContains('For plugin: MeTools');
        $this->assertOutputContains('Link `' . Filesystem::instance()->rtr(WWW_ROOT . 'me_tools') . '` has been created');
        $this->assertOutputContains('For plugin: TestPlugin');
        $this->assertOutputContains('Link `' . Filesystem::instance()->rtr(WWW_ROOT . 'test_plugin') . '` has been created');
        $this->assertErrorEmpty();
        $this->assertFileExists(WWW_ROOT . 'me_tools');
        $this->assertFileExists(WWW_ROOT . 'test_plugin');

        $clear();
        $this->removePlugins(['TestPlugin']);
    }
}
