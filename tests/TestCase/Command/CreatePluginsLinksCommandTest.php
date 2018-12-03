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
namespace MeTools\Test\TestCase\Command;

use MeTools\TestSuite\ConsoleIntegrationTestCase;

/**
 * CreatePluginsLinksCommandTest class
 */
class CreatePluginsLinksCommandTest extends ConsoleIntegrationTestCase
{
    /**
     * Called after every test method
     * @return void
     */
    public function tearDown()
    {
        safe_unlink(WWW_ROOT . 'me_tools');
    }

    /**
     * Tests for `execute()` method
     * @test
     */
    public function testExecute()
    {
        $this->exec('me_tools.create_plugins_links -v');
        $this->assertExitWithSuccess();
        $this->assertOutputContains('Skipping plugin Assets. It does not have webroot folder.');
        $this->assertOutputContains('For plugin: MeTools');
        $this->assertOutputContains('Created symlink ' . WWW_ROOT . 'me_tools');
        $this->assertOutputContains('Done');
        $this->assertErrorEmpty();
        $this->assertFileExists(WWW_ROOT . 'me_tools');
    }
}
