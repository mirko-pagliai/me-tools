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

use Cake\Command\PluginAssetsSymlinkCommand;
use Cake\Console\Arguments;
use Cake\Console\ConsoleIo;
use Cake\Console\ConsoleOptionParser;
use MeTools\Command\Command;
use Tools\Exceptionist;

/**
 * Executes all available commands
 */
class RunAllCommand extends Command
{
    /**
     * Questions
     * @var array{question: string, default: bool, command: \Cake\Command\Command}[]
     */
    public array $questions = [];

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
                'default' => false,
                'command' => new CreateDirectoriesCommand(),
            ],
            [
                'question' => __d('me_tools', 'Set directories permissions?'),
                'default' => true,
                'command' => new SetPermissionsCommand(),
            ],
            [
                'question' => __d('me_tools', 'Create {0}?', 'robots.txt'),
                'default' => true,
                'command' => new CreateRobotsCommand(),
            ],
            [
                'question' => __d('me_tools', 'Create symbolic links for plugins assets?'),
                'default' => true,
                'command' => new PluginAssetsSymlinkCommand(),
            ],
            [
                'question' => __d('me_tools', 'Create symbolic links for vendor assets?'),
                'default' => true,
                'command' => new CreateVendorsLinksCommand(),
            ],
        ];
    }

    /**
     * Executes all available commands
     * @param \Cake\Console\Arguments $args The command arguments
     * @param \Cake\Console\ConsoleIo $io The console io
     * @return void
     * @throws \ErrorException
     */
    public function execute(Arguments $args, ConsoleIo $io): void
    {
        $questions = $this->questions;
        if ($args->getOption('force')) {
            $questions = array_filter($questions, fn(array $question): bool => $question['default']);
        }

        foreach ($questions as $question) {
            Exceptionist::isTrue(!array_diff(array_keys($question), ['question', 'default', 'command']), 'Invalid question keys');
            /** @var \Cake\Command\Command $Command */
            [$question, $default, $Command] = array_values($question);

            //The method must be executed if the `force` mode is set or if the user answers yes to the question
            $toBeExecuted = true;
            if (!$args->getOption('force')) {
                $ask = $io->askChoice($question, $default ? ['Y', 'n'] : ['y', 'N'], $default ? 'y' : 'n');
                $toBeExecuted = in_array($ask, ['Y', 'y']);
            }

            if ($toBeExecuted) {
                $Command->run($args->getOption('verbose') ? ['--verbose'] : [], $io);
            }
        }
    }
}
