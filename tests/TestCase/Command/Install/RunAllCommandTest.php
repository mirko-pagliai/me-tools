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

use Cake\Command\PluginAssetsSymlinkCommand;
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
     * Internal method to get a `RunAllCommand` where the `execute()` method of each sub-command outs its classname
     * @param \Cake\Console\ConsoleIo $io A `ConsoleIo` instance
     * @return \MeTools\Command\Install\RunAllCommand
     */
    protected function getRunAllCommand(ConsoleIo $io): RunAllCommand
    {
        $RunAllCommand = new RunAllCommand();

        foreach ($RunAllCommand->questions as $k => $question) {
            /** @var \MeTools\Command\Command&\PHPUnit\Framework\MockObject\MockObject $SubCommand */
            $SubCommand = $this->createPartialMock(Command::class, ['execute']);
            $SubCommand->method('execute')->willReturnCallback(fn() => $io->out(get_class($question['command'])));
            $RunAllCommand->questions[$k]['command'] = $SubCommand;
        }

        return $RunAllCommand;
    }

    /**
     * @test
     * @uses \MeTools\Command\Install\RunAllCommand::execute()
     */
    public function testExecute(): void
    {
        //Runs the command with `force` and `verbose`
        $expected = [
            'MeTools\Command\Install\SetPermissionsCommand',
            'MeTools\Command\Install\CreateRobotsCommand',
            PluginAssetsSymlinkCommand::class,
            'MeTools\Command\Install\CreateVendorsLinksCommand',
        ];
        $out = new StubConsoleOutput();
        $io = new ConsoleIo($out);
        $this->assertNull($this->getRunAllCommand($io)->run(['-f', '-v'], $io));
        $this->assertEquals($expected, $out->messages());

        //Runs the command by answering `y` to each question
        $expected = [
            'MeTools\Command\Install\CreateDirectoriesCommand',
            'MeTools\Command\Install\SetPermissionsCommand',
            'MeTools\Command\Install\CreateRobotsCommand',
            PluginAssetsSymlinkCommand::class,
            'MeTools\Command\Install\CreateVendorsLinksCommand',
        ];
        $out = new StubConsoleOutput();
        $io = $this->getMockBuilder(ConsoleIo::class)
            ->setConstructorArgs([$out])
            ->onlyMethods(['askChoice'])
            ->getMock();
        $io->method('askChoice')->willReturn('y');
        $this->assertNull($this->getRunAllCommand($io)->run([], $io));
        $this->assertEquals($expected, $out->messages());
    }
}
