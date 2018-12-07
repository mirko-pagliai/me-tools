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
namespace MeTools\Test\TestCase\Command\Install;

use Cake\Core\Configure;
use MeTools\TestSuite\ConsoleIntegrationTestCase;
use Tools\TestSuite\TestCaseTrait;

/**
 * SetPermissionsCommandTest class
 */
class SetPermissionsCommandTest extends ConsoleIntegrationTestCase
{
    use TestCaseTrait;

    /**
     * Tests for `execute()` method
     * @test
     */
    public function testExecute()
    {
        $this->exec('me_tools.set_permissions -v');
        $this->assertExitWithSuccess();

        foreach (Configure::read('WRITABLE_DIRS') as $path) {
            $this->assertOutputContains('Setted permissions on `' . rtr($path) . '`');
        }

        $this->assertErrorEmpty();
    }
}
