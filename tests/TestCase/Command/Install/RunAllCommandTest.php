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
use MeTools\Command\Install\RunAllCommand;
use MeTools\Console\Command;
use MeTools\TestSuite\CommandTestCase;

/**
 * RunAllCommandTest class
 */
class RunAllCommandTest extends CommandTestCase
{
    /**
     * @var class-string<\MeTools\Console\Command>[]
     */
    protected array $debug = [];

    /**
     * @test
     * @uses \MeTools\Command\Install\RunAllCommand::execute()
     */
    public function testExecute(): void
    {
        $Command = new RunAllCommand();
        $io = $this->createConfiguredMock(ConsoleIo::class, ['askChoice' => 'y']);

        $Command->questions = array_map(function (array $question): array {
            /** @var \MeTools\Console\Command&\PHPUnit\Framework\MockObject\MockObject $Command */
            $Command = $this->createPartialMock(Command::class, ['execute']);
            $Command->method('execute')->willReturnCallback(function () use ($question) {
                /** @var class-string<\MeTools\Console\Command> $commandClass */
                $commandClass = $question['command'];
                $this->debug[] = $commandClass;
            });

            return array_merge($question, ['command' => $Command]);
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
        $this->assertEquals($expected, $this->debug);
    }
}
