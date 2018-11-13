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
 * @since       2.15.0
 */
namespace MeTools\TestSuite;

use Cake\Console\Shell;
use Cake\TestSuite\ConsoleIntegrationTestCase as CakeConsoleIntegrationTestCase;
use Cake\Utility\Inflector;

/**
 * ConsoleIntegrationTestCase class
 */
abstract class ConsoleIntegrationTestCase extends CakeConsoleIntegrationTestCase
{
    /**
     * Internal method to get the help output for the current command.
     *
     * In other words, it runs the command:
     * `cake Plugin.shell_name [args]`
     *
     * And returns the output as string
     * @return string
     */
    protected function getHelpOutput()
    {
        $parts = explode('\\', get_class($this));
        $command = Inflector::underscore(substr(array_pop($parts), 0, -9));

        $prefix = first_value($parts);
        if ($prefix !== 'App') {
            $command = sprintf('%s.%s', Inflector::underscore($prefix), $command);
        }

        //Executes the command
        $command .= ' -h';
        $this->exec($command);

        return first_value($this->_out->messages());
    }

    /**
     * Gets the description for the current command
     * @return string
     * @uses getHelpOptionOutput()
     */
    public function getParserDescription()
    {
        $message = $this->getHelpOptionOutput();

        if (!preg_match('/^(.+)\v{2}<info>Usage:<\/info>/', $message, $matches)) {
            $this->fail('Unable to retrevie the shell description');
        }

        return $matches[1];
    }

    /**
     * Gets the options for the current command
     * @return array
     * @uses getHelpOptionOutput()
     */
    public function getParserOptions()
    {
        $message = $this->getHelpOptionOutput();

        if (!preg_match('/<info>Options:<\/info>\v{2}((.|\v)+)\v$/', $message, $matches)) {
            $this->fail('Unable to retrevie the shell options');
        }

        $options = explode(PHP_EOL, $matches[1]);

        return array_map(function ($line) {
            if (!preg_match('/^--(\w+)(,\s+-(\w))?\s+(.+)$/', $line, $matches)) {
                $this->fail('Unable to parse the shell options');
            }

            list(, $name,, $short, $help) = $matches;

            return array_filter(compact('name', 'short', 'help'));
        }, $options);
    }

    /**
     * Gets the subcommand for the current command
     * @return array
     * @uses getHelpOptionOutput()
     */
    public function getParserSubcommands()
    {
        $message = $this->getHelpOptionOutput();

        if (!preg_match('/<info>Subcommands:<\/info>\v+((\V+\v)+\V+)\v+To see help on a subcommand/', $message, $matches)) {
            $this->fail('Unable to retrevie the shell subcommands');
        }

        $subcommands = explode(PHP_EOL, $matches[1]);

        return array_map(function ($subcommand) {
            if (!preg_match('/^(\S+)\s+(.+)$/', $subcommand, $matches)) {
                $this->fail('Unable to parse the subcommand');
            }

            list(, $name, $help) = $matches;

            return compact('name', 'help');
        }, $subcommands);
    }

    /**
     * Asserts shell exited with the error code
     * @param string $message Failure message to be appended to the generated
     *  message
     * @return void
     */
    public function assertExitWithError($message = '')
    {
        $this->assertExitCode(Shell::CODE_ERROR, $message);
    }

    /**
     * Asserts shell exited with the success code
     * @param string $message Failure message to be appended to the generated
     *  message
     * @return void
     */
    public function assertExitWithSuccess($message = '')
    {
        $this->assertExitCode(Shell::CODE_SUCCESS, $message);
    }
}
