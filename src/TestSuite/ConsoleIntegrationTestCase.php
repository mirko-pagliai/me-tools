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
use MeTools\TestSuite\Traits\TestCaseTrait;
use Reflection\ReflectionTrait;

/**
 * ConsoleIntegrationTestCase class
 */
class ConsoleIntegrationTestCase extends CakeConsoleIntegrationTestCase
{
    use ReflectionTrait;
    use TestCaseTrait;

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
