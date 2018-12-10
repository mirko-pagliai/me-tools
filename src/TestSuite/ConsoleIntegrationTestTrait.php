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
 * @since       2.18
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
     * Shell instance
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    protected $Shell;

    /**
     * Called before every test method
     * @return void
     * @uses $Shell
     * @uses $autoInitializeClass
     */
    public function setUp()
    {
        parent::setUp();

        $className = $this->getOriginClassName($this);
        if (!$this->Shell && !empty($this->autoInitializeClass)) {
            $this->Shell = $this->getMockForShell($className);
        }

        $parts = explode('\\', $className);
        if (next($parts) === 'Command') {
            $this->useCommandRunner();
        }
    }

    /**
     * Asserts shell exited with the error code
     * @param string $message Failure message to be appended to the generated
     *  message
     * @return void
     */
    public function assertExitWithError($message = '')
    {
        $this->assertExitCode(Command::CODE_ERROR, $message);
    }

    /**
     * Asserts shell exited with the success code
     * @param string $message Failure message to be appended to the generated
     *  message
     * @return void
     */
    public function assertExitWithSuccess($message = '')
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
    public function assertOutputNotEmpty($message = '')
    {
        $this->assertNotEmpty($this->_out->messages(), $message);
    }
}
