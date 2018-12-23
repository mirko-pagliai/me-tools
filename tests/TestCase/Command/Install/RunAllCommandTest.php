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

use Cake\Console\Arguments;
use Cake\Console\ConsoleIo;
use MeTools\Console\Command;
use MeTools\TestSuite\ConsoleIntegrationTestTrait;
use MeTools\TestSuite\TestCase;

/**
 * RunAllCommandTest class
 */
class RunAllCommandTest extends TestCase
{
    use ConsoleIntegrationTestTrait;

    /**
     * If `true`, a mock instance of the shell will be created
     * @var bool
     */
    protected $autoInitializeClass = true;

    /**
     * @var array
     */
    protected $debug = [];

    /**
     * Tests for `execute()` method
     * @test
     */
    public function testExecute()
    {
        $this->exec('me_tools.install -f -v');
        $this->assertExitWithSuccess();
        $this->assertOutputNotEmpty();
        $this->assertErrorEmpty();

        $io = $this->getMockBuilder(ConsoleIo::class)
            ->setMethods(['askChoice'])
            ->getMock();

        $io->method('askChoice')->will($this->returnValue('y'));

        $this->Command->questions = array_map(function ($question) {
            $command = $this->getMockBuilder(Command::class)
                ->setMethods(['execute'])
                ->getMock();
            $command->method('execute')->will($this->returnCallback(function () use ($question) {
                $this->debug[] = $question['command'];
            }));
            $question['command'] = $command;

            return $question;
        }, $this->Command->questions);

        $expected = [
            'MeTools\Command\Install\SetPermissionsCommand',
            'MeTools\Command\Install\CreateRobotsCommand',
            'MeTools\Command\Install\CreatePluginsLinksCommand',
            'MeTools\Command\Install\CreateVendorsLinksCommand',
        ];
        $this->Command->execute(new Arguments([], ['force' => true], []), $io);
        $this->assertEquals($expected, $this->debug);

        $this->debug = [];

        $expected = [
            'MeTools\Command\Install\CreateDirectoriesCommand',
            'MeTools\Command\Install\SetPermissionsCommand',
            'MeTools\Command\Install\CreateRobotsCommand',
            'MeTools\Command\Install\FixComposerJsonCommand',
            'MeTools\Command\Install\CreatePluginsLinksCommand',
            'MeTools\Command\Install\CreateVendorsLinksCommand',
        ];
        $this->Command->execute(new Arguments([], [], []), $io);
        $this->assertEquals($expected, $this->debug);
    }
}
