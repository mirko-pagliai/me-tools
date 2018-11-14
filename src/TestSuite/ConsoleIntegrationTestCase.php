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
use MeTools\TestSuite\Traits\MockTrait;
use MeTools\TestSuite\Traits\TestCaseTrait;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * ConsoleIntegrationTestCase class
 */
abstract class ConsoleIntegrationTestCase extends CakeConsoleIntegrationTestCase
{
    use MockTrait;
    use TestCaseTrait;

    /**
     * Shell instance
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    protected $Shell;

    /**
     * If `true`, a mock instance of the shell will be created
     * @var bool
     */
    protected $autoInitializeClass = true;

    /**
     * Called before every test method
     * @return void
     * @uses $Shell
     * @uses $autoInitializeClass
     */
    public function setUp()
    {
        parent::setUp();

        if (!$this->Shell && $this->autoInitializeClass) {
            $parts = explode('\\', get_class($this));
            array_splice($parts, 1, 2, []);
            $parts[] = substr(array_pop($parts), 0, -4);
            $className = implode('\\', $parts);

            $this->Shell = $this->getMockForShell($className);
        }
    }

    /**
     * Gets all shell methods.
     *
     * It excludes the `main` method.
     * @param array $exclude Other methods you want to exclude
     * @return array
     * @uses $Shell
     */
    protected function getShellMethods(array $exclude = [])
    {
        if (empty($this->Shell)) {
            $this->fail('The property `$this->Shell` has not been set');
        }

        $class = $this->Shell instanceof MockObject ? get_parent_class($this->Shell) : $this->Shell;

        $methods = get_child_methods($class);
        if (!in_array(get_parent_class($class), [Shell::class, 'MeTools\Console\Shell'])) {
            $methods = array_merge($methods, get_child_methods(get_parent_class($class)));
        }

        $methods = array_diff($methods, array_merge(['main'], $exclude));
        sort($methods);

        return $methods;
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
