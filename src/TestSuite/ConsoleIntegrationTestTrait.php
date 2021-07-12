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
namespace MeTools\TestSuite;

use Cake\TestSuite\ConsoleIntegrationTestTrait as BaseConsoleIntegrationTestTrait;
use MeTools\Console\Command;

/**
 * A trait intended to make integration tests of cake console commands easier
 */
trait ConsoleIntegrationTestTrait
{
    use BaseConsoleIntegrationTestTrait;

    /**
     * Command instance
     * @var \MeTools\Console\Command
     */
    protected $Command;

    /**
     * Called before every test method
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        if (preg_match('/TraitTest$/', get_class($this))) {
            return;
        }

        if (!$this->Command && !empty($this->autoInitializeClass)) {
            /** @var class-string<\MeTools\Console\Command> $className */
            $className = $this->getOriginClassNameOrFail($this);
            $this->Command = new $className();
        }

        if ($this->Command && method_exists($this->Command, 'initialize')) {
            $this->Command->initialize();
        }

        if (string_ends_with($className ?? $this->getOriginClassName($this), 'Command')) {
            $this->useCommandRunner();
        }
    }

    /**
     * Asserts shell exited with the error code
     * @param string $message Failure message to be appended to the generated
     *  message
     * @return void
     */
    public function assertExitWithError(string $message = ''): void
    {
        $this->assertExitCode(Command::CODE_ERROR, $message);
    }

    /**
     * Asserts shell exited with the success code
     * @param string $message Failure message to be appended to the generated
     *  message
     * @return void
     */
    public function assertExitWithSuccess(string $message = ''): void
    {
        $this->assertExitCode(Command::CODE_SUCCESS, $message);
    }

    /**
     * Asserts that `stdout` is not empty
     * @param string $message Failure message to be appended to the generated
     *  message
     * @return void
     * @since 2.17.6
     */
    public function assertOutputNotEmpty(string $message = 'stdout was empty'): void
    {
        $this->assertNotEmpty($this->_out->messages(), $message);
    }
}
