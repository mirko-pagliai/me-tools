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

use Cake\Console\ConsoleIo;
use Cake\Core\Configure;
use MeTools\Command\Install\SetPermissionsCommand;
use MeTools\TestSuite\ConsoleIntegrationTestTrait;
use MeTools\TestSuite\TestCase;
use Tools\Filesystem;

/**
 * SetPermissionsCommandTest class
 */
class SetPermissionsCommandTest extends TestCase
{
    use ConsoleIntegrationTestTrait;

    /**
     * Tests for `execute()` method
     * @uses \MeTools\Command\Install\SetPermissionsCommand::execute()
     * @test
     */
    public function testExecute(): void
    {
        $expectedDirs = array_unique(Configure::readOrFail('WRITABLE_DIRS'));
        $this->exec('me_tools.set_permissions -v');
        $this->assertExitSuccess();
        foreach (array_map([Filesystem::instance(), 'rtr'], $expectedDirs) as $expectedDir) {
            $this->assertOutputContains('Set permissions on `' . $expectedDir . '`');
        }
    }
}
