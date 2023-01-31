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

use Cake\Core\Configure;
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
        $this->exec('me_tools.create_directories -v');
        $this->assertExitSuccess();
        foreach (array_map([Filesystem::instance(), 'rtr'], Configure::readOrFail('WRITABLE_DIRS')) as $expectedDir) {
            $this->assertOutputContains('File or directory `' . $expectedDir . '` already exists');
        }
    }
}
