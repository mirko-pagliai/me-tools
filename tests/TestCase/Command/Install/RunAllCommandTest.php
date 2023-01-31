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
use Cake\Console\TestSuite\StubConsoleOutput;
use MeTools\Command\Command;
use MeTools\Command\Install\RunAllCommand;
use MeTools\TestSuite\CommandTestCase;

/**
 * RunAllCommandTest class
 */
class RunAllCommandTest extends CommandTestCase
{
    /**
     * @test
     * @uses \MeTools\Command\Install\RunAllCommand::execute()
     */
    public function testExecute(): void
    {
        $out = new StubConsoleOutput();
        $io = $this->getMockBuilder(ConsoleIo::class)
            ->setConstructorArgs([$out])
            ->onlyMethods(['askChoice'])
            ->getMock();
        $io->method('askChoice')->willReturn('y');

        $Command = new RunAllCommand();
        $Command->questions = array_map(function (array $question) use ($io): array {
            /** @var \MeTools\Command\Command&\PHPUnit\Framework\MockObject\MockObject $SubCommand */
            $SubCommand = $this->createPartialMock(Command::class, ['execute']);
            $SubCommand->method('execute')->willReturnCallback(fn() => $io->out(get_class($question['command'])));

            return array_merge($question, ['command' => $SubCommand]);
        }, $Command->questions);

        $expected = [
            'MeTools\Command\Install\CreateDirectoriesCommand',
            'MeTools\Command\Install\SetPermissionsCommand',
            'MeTools\Command\Install\CreateRobotsCommand',
            'MeTools\Command\Install\FixComposerJsonCommand',
            'MeTools\Command\Install\CreatePluginsLinksCommand',
            'MeTools\Command\Install\CreateVendorsLinksCommand',
        ];
        $this->assertNull($Command->run([], $io));
        $this->assertEquals($expected, $out->messages());
    }
}
