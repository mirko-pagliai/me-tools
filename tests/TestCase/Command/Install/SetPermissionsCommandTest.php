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

use MeTools\Core\Configure;
use MeTools\TestSuite\CommandTestCase;

/**
 * SetPermissionsCommandTest class
 */
class SetPermissionsCommandTest extends CommandTestCase
{
    /**
     * @test
     * @uses \MeTools\Command\Install\SetPermissionsCommand::execute()
     */
    public function testExecute(): void
    {
        $this->exec('me_tools.set_permissions -v');
        $this->assertExitSuccess();
        foreach (Configure::readFromPlugins('WritableDirs') as $expectedDir) {
            $this->assertOutputContains('Set permissions on `' . rtr($expectedDir) . '`');
        }
    }
}
