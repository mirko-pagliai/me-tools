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
 */
trait ConsoleIntegrationTestTrait
{
    use BaseConsoleIntegrationTestTrait;

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
