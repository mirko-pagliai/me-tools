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
use Tools\Filesystem;

/**
 * CreateDirectoriesCommandTest class
 */
class CreateDirectoriesCommandTest extends CommandTestCase
{
    /**
     * @test
     * @uses \MeTools\Command\Install\CreateDirectoriesCommand::execute()
     */
    public function testExecute(): void
    {
        $expectedDirs = array_merge(...array_values(Configure::readFromPlugins('WritableDirs')));
        $this->assertIsArrayNotEmpty($expectedDirs);

        $this->exec('me_tools.create_directories -v');
        $this->assertExitSuccess();
        foreach ($expectedDirs as $expectedDir) {
            $this->assertOutputContains('File or directory `' . Filesystem::instance()->rtr($expectedDir) . '` already exists');
        }
    }
}
