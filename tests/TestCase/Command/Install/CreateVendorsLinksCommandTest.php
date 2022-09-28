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
use MeTools\Command\Install\CreateVendorsLinksCommand;
use MeTools\TestSuite\ConsoleIntegrationTestTrait;
use MeTools\TestSuite\TestCase;

/**
 * CreateVendorsLinksCommandTest class
 */
class CreateVendorsLinksCommandTest extends TestCase
{
    use ConsoleIntegrationTestTrait;

    /**
     * Tests for `execute()` method
     * @test
     */
    public function testExecute(): void
    {
        $io = new ConsoleIo();
        $Command = $this->getMockBuilder(CreateVendorsLinksCommand::class)
            ->onlyMethods(['createLink'])
            ->getMock();

        $links = Configure::read('VENDOR_LINKS');
        $method = $Command->expects($this->exactly(count($links)))->method('createLink');
        $consecutiveCalls = array_map(fn($origin, string $target): array => [$io, ROOT . 'vendor' . DS . $origin, WWW_ROOT . 'vendor' . DS . $target], array_keys($links), $links);
        call_user_func_array([$method, 'withConsecutive'], $consecutiveCalls);

        $this->assertNull($Command->run([], $io));
    }
}
