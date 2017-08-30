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
 * @since       2.14.0
 */
namespace MeTools\TestSuite;

use Cake\TestSuite\IntegrationTestCase as CakeIntegrationTestCase;
use MeTools\TestSuite\Traits\TestCaseTrait;
use Reflection\ReflectionTrait;

/**
 * A test case class intended to make integration tests of your controllers
 *  easier.
 *
 * This test class provides a number of helper methods and features that make
 *  dispatching requests and checking their responses simpler. It favours full
 *  integration tests over mock objects as you can test more of your code
 *  easily and avoid some of the maintenance pitfalls that mock objects create.
 */
class IntegrationTestCase extends CakeIntegrationTestCase
{
    use ReflectionTrait;
    use TestCaseTrait;

    /**
     * Teardown any static object changes and restore them
     * @return void
     */
    public function tearDown()
    {
        parent::tearDown();

        $this->deleteAllLogs();
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
        if (!$this->_response) {
            $this->fail('Not response set, cannot assert cookies');
        }

        $result = $this->_response->cookie($name);
        $this->assertEmpty($result['value'], $message);
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
     * @return void
     */
    public function assertResponseOkAndNotEmpty()
    {
        $this->assertResponseOk() && $this->assertResponseNotEmpty();
    }
}
