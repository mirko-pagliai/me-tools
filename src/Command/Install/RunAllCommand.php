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
 * @since       2.18.0
 */
namespace MeTools\Command\Install;

use Cake\Console\Arguments;
use Cake\Console\ConsoleIo;
use Cake\Console\ConsoleOptionParser;
use Cake\Utility\Hash;
use MeTools\Command\Install\CreateDirectoriesCommand;
use MeTools\Command\Install\CreatePluginsLinksCommand;
use MeTools\Command\Install\CreateRobotsCommand;
use MeTools\Command\Install\CreateVendorsLinksCommand;
use MeTools\Command\Install\FixComposerJsonCommand;
use MeTools\Command\Install\SetPermissionsCommand;
use MeTools\Console\Command;
use Tools\Exceptionist;

/**
 * Executes all available commands
 */
class RunAllCommand extends Command
{
    /**
     * Questions
     * @var array<array<string, (\MeTools\Console\Command&\PHPUnit\Framework\MockObject\MockObject)|string>>
     */
    public $questions = [];

    /**
     * Hook method for defining this command's option parser
     * @param \Cake\Console\ConsoleOptionParser $parser The parser to be defined
     * @return \Cake\Console\ConsoleOptionParser
     */
    protected function buildOptionParser(ConsoleOptionParser $parser): ConsoleOptionParser
    {
        return $parser->setDescription(__d('me_tools', 'Executes all available commands'))
            ->addOption('force', [
                'boolean' => true,
                'default' => false,
                'help' => __d('me_tools', 'Executes tasks without prompting'),
                'short' => 'f',
            ]);
    }

    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();

        $this->questions = [
            [
                'question' => __d('me_tools', 'Create default directories?'),
                'default' => 'N',
                'command' => CreateDirectoriesCommand::class,
            ],
            [
                'question' => __d('me_tools', 'Set directories permissions?'),
                'default' => 'Y',
                'command' => SetPermissionsCommand::class,
            ],
            [
                'question' => __d('me_tools', 'Create {0}?', 'robots.txt'),
                'default' => 'Y',
                'command' => CreateRobotsCommand::class,
            ],
            [
                'question' => __d('me_tools', 'Fix {0}?', 'composer.json'),
                'default' => 'N',
                'command' => FixComposerJsonCommand::class,
            ],
            [
                'question' => __d('me_tools', 'Create symbolic links for plugins assets?'),
                'default' => 'Y',
                'command' => CreatePluginsLinksCommand::class,
            ],
            [
                'question' => __d('me_tools', 'Create symbolic links for vendor assets?'),
                'default' => 'Y',
                'command' => CreateVendorsLinksCommand::class,
            ],
        ];
    }

    /**
     * Executes all available commands
     * @param \Cake\Console\Arguments $args The command arguments
     * @param \Cake\Console\ConsoleIo $io The console io
     * @return void
     */
    public function execute(Arguments $args, ConsoleIo $io): void
    {
        $questions = $args->getOption('force') ? Hash::extract($this->questions, '{n}[default=Y]') : $this->questions;

        foreach ((array)$questions as $question) {
            Exceptionist::isTrue(!array_diff(array_keys($question), ['question', 'default', 'command']), 'Invalid question keys');
            [$question, $default, $command] = array_values($question);

            //The method must be executed if the `force` mode is set or if the
            //  user answers yes to the question
            $toBeExecuted = true;
            if (!$args->getOption('force')) {
                $ask = $io->askChoice($question, $default === 'Y' ? ['Y', 'n'] : ['y', 'N'], $default);
                $toBeExecuted = in_array($ask, ['Y', 'y']);
            }

            if ($toBeExecuted) {
                $command = is_string($command) ? new $command() : $command;
                $command->run($args->getOption('verbose') ? ['--verbose'] : [], $io);
            }
        }
    }
}
