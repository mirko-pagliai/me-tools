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

use MeTools\TestSuite\CommandTestCase;

/**
 * CreateRobotsCommandTest class
 */
class CreateRobotsCommandTest extends CommandTestCase
{
    /**
     * @test
     * @uses \MeTools\Command\Install\CreateRobotsCommand::execute()
     */
    public function testExecute(): void
    {
        $this->exec('me_tools.create_robots -v');
        $this->assertExitSuccess();
        $this->assertOutputContains('Creating file ' . WWW_ROOT . 'robots.txt');
        $this->assertOutputContains('<success>Wrote</success> `' . WWW_ROOT . 'robots.txt`');
        $this->assertErrorEmpty();
        $this->assertStringEqualsFile(WWW_ROOT . 'robots.txt', 'User-agent: *' . PHP_EOL . 'Disallow: /admin/' . PHP_EOL .
            'Disallow: /ckeditor/' . PHP_EOL . 'Disallow: /css/' . PHP_EOL .
            'Disallow: /js/' . PHP_EOL . 'Disallow: /vendor/');

        /**
         * Runs again, the file already exists
         */
        $this->exec('me_tools.create_robots -v');
        $this->assertExitSuccess();
        $this->assertOutputContains('File or directory `' . rtr(WWW_ROOT . 'robots.txt') . '` already exists');
        $this->assertErrorEmpty();

        unlink(WWW_ROOT . 'robots.txt');
    }
}
