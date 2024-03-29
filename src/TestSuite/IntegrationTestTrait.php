<?php
/** @noinspection PhpInternalEntityUsedInspection */
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

use Cake\TestSuite\Constraint\Session\SessionEquals;
use Cake\TestSuite\IntegrationTestTrait as CakeIntegrationTestTrait;

/**
 * A trait intended to make integration tests of your controllers easier
 */
trait IntegrationTestTrait
{
    use CakeIntegrationTestTrait;

    /**
     * Asserts that a cookie is empty
     * @param string $name The cookie name
     * @param string $message The failure message that will be appended to the generated message
     * @return void
     */
    public function assertCookieIsEmpty(string $name, string $message = ''): void
    {
        $this->_response ?: $this->fail('Not response set, cannot assert cookies');
        $cookie = $this->_response->getCookie($name);
        $this->assertTrue(!isset($cookie['value']) || !$cookie['value'], $message);
    }

    /**
     * Asserts that the response status code is in the 2xx range and the response content is not empty
     * @param string $message The failure message that will be appended to the generated message
     * @return void
     */
    public function assertResponseOkAndNotEmpty(string $message = ''): void
    {
        $this->assertResponseOk($message);
        $this->assertResponseNotEmpty($message);
    }

    /**
     * Asserts session is empty
     * @param string $path The session data path. Uses Hash::get() compatible notation
     * @param string $message The failure message that will be appended to the generated message
     * @return void
     * @since 2.18.9
     */
    public function assertSessionEmpty(string $path, string $message = ''): void
    {
        $this->assertThat(null, new SessionEquals($path), $this->extractVerboseMessage($message));
    }

    /**
     * Gets the status code from the last response.
     *
     * The status code is a 3-digit integer result code of the server's attempt to understand and satisfy the request.
     * @return int Status code
     * @since 2.21.1
     */
    protected function getStatusCode(): int
    {
        if (!$this->_response) {
            $this->fail('No response set, cannot get status code');
        }

        return $this->_response->getStatusCode();
    }
}
