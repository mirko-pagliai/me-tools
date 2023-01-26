<?php
/** @noinspection PhpUnhandledExceptionInspection */
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

use MeTools\TestSuite\CommandTestCase;
use Tools\Filesystem;

/**
 * CreatePluginsLinksCommandTest class
 */
class CreatePluginsLinksCommandTest extends CommandTestCase
{
    /**
     * @test
     * @uses \MeTools\Command\Install\CreatePluginsLinksCommand::execute()
     */
    public function testExecute(): void
    {
        $Filesystem = new Filesystem();
        $clear = fn() => array_map(fn(string $path): bool => file_exists($path) && $Filesystem->rmdirRecursive($path), [WWW_ROOT . 'me_tools', WWW_ROOT . 'test_plugin']);

        $this->loadPlugins(['TestPlugin' => []]);

        $clear();
        $this->exec('me_tools.create_plugins_links -v');
        $this->assertExitSuccess();
        $this->assertOutputContains('Skipping plugin `Assets`. It does not have webroot folder');
        $this->assertOutputContains('For plugin: MeTools');
        $this->assertOutputContains('Link `' . $Filesystem->rtr(WWW_ROOT . 'me_tools') . '` has been created');
        $this->assertOutputContains('For plugin: TestPlugin');
        $this->assertOutputContains('Link `' . $Filesystem->rtr(WWW_ROOT . 'test_plugin') . '` has been created');
        $this->assertErrorEmpty();
        $this->assertFileExists(WWW_ROOT . 'me_tools');
        $this->assertFileExists(WWW_ROOT . 'test_plugin');

        $clear();
        $this->removePlugins(['TestPlugin']);
    }
}
