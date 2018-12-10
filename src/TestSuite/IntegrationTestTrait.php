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
 * @since       2.18.0
 */
namespace MeTools\TestSuite;

use Cake\TestSuite\IntegrationTestTrait as BaseIntegrationTestTrait;

/**
 * A trait intended to make integration tests of your controllers easier
 */
trait IntegrationTestTrait
{
    use BaseIntegrationTestTrait;

    /**
     * Called before every test method
     * @return void
     */
    public function setUp()
    {
        parent::setUp();

        $this->useHttpServer(true);
    }

    /**
     * Asserts that a cookie is empty
     * @param string $name The cookie name
     * @param string $message The failure message that will be appended to the
     *  generated message
     * @return void
     */
    public function assertCookieIsEmpty($name, $message = '')
    {
        $this->_response ?: $this->fail('Not response set, cannot assert cookies');
        $this->assertEmpty($this->_response->getCookie($name)['value'], $message);
    }

    /**
     * Asserts flash message contents
     * @param string $expected The expected contents
     * @param int $key Flash message key
     * @param string $message The failure message that will be appended to the
     *  generated message
     * @return void
     */
    public function assertFlashMessage($expected, $key = 0, $message = '')
    {
        $this->assertSession($expected, sprintf('Flash.flash.%d.message', $key), $message);
    }

    /**
     * Asserts that the response status code is in the 2xx range and the
     *  response content is not empty.
     * @param string $message The failure message that will be appended to the
     *  generated message
     * @return void
     */
    public function assertResponseOkAndNotEmpty($message = '')
    {
        $this->assertResponseOk($message) && $this->assertResponseNotEmpty($message);
    }
}
