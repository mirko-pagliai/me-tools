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

use Cake\Console\TestSuite\ConsoleIntegrationTestTrait as BaseConsoleIntegrationTestTrait;

/**
 * A trait intended to make integration tests of cake console commands easier
 * @property \Cake\Console\TestSuite\StubConsoleOutput|null $_err Console error output stub
 * @property \Cake\Console\ConsoleInput|null $_in Console input mock
 * @property \MeTools\Console\Command $Command The command instance for which a test is being performed
 */
trait ConsoleIntegrationTestTrait
{
    use BaseConsoleIntegrationTestTrait;

    /**
     * Get magic method.
     *
     * It provides access to the cached properties of the test.
     * @param string $name Property name
     * @return \MeTools\Console\Command|void
     * @throws \ReflectionException
     */
    public function __get(string $name)
    {
        if ($name === 'Command') {
            if (empty($this->_cache['Command'])) {
                $this->_cache['Command'] = new $this->originClassName();
                $this->_cache['Command']->initialize();
            }

            return $this->_cache['Command'];
        }

        return parent::__get($name);
    }

    /**
     * Called before every test method
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->useCommandRunner();
    }

    /**
     * Asserts that `stdout` is not empty
     * @param string $message Failure message to be appended to the generated message
     * @return void
     * @since 2.17.6
     */
    public function assertOutputNotEmpty(string $message = 'stdout was empty'): void
    {
        $this->assertNotEmpty($this->_out->messages(), $message);
    }
}
