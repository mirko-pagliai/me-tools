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
 */
namespace MeTools\Test\TestCase\TestSuite;

use Cake\Http\Session;
use Cake\TestSuite\Stub\Response;
use MeTools\TestSuite\IntegrationTestCase;

/**
 * IntegrationTestCaseTest class
 */
class IntegrationTestCaseTest extends IntegrationTestCase
{
    /**
     * @var \Cake\Network\Session
     */
    protected $_requestSession;

    /**
     * @var \Cake\TestSuite\Stub\Response
     */
    protected $_response;

    /**
     * Setup the test case, backup the static object values so they can be
     * restored. Specifically backs up the contents of Configure and paths in
     *  App if they have not already been backed up
     * @return void
     */
    public function setUp()
    {
        parent::setUp();

        $this->_requestSession = new Session;
        $this->_response = new Response;
    }

    /**
     * Test for `assertCookieIsEmpty()` method
     * @test
     */
    public function testAssertCookieIsEmpty()
    {
        $this->assertCookieIsEmpty('test-cookie');

        $this->_response = $this->_response->withCookie('test-cookie', null);
        $this->assertCookieIsEmpty('test-cookie');

        $this->_response = $this->_response->withCookie('test-cookie', false);
        $this->assertCookieIsEmpty('test-cookie');
    }

    /**
     * Test for `assertCookieIsEmpty()` method, with no response
     * @expectedException PHPUnit\Framework\AssertionFailedError
     * @expectedExceptionMessage Not response set, cannot assert cookies
     * @test
     */
    public function testAssertCookieIsEmptyNoResponse()
    {
        $this->_response = false;
        $this->assertCookieIsEmpty('test-cookie');
    }

    /**
     * Test for `assertFlashMessage()` method
     * @test
     */
    public function testAssertFlashMessage()
    {
        $messages = ['first flash', 'second flash'];

        foreach ($messages as $key => $expectedMessage) {
            $this->_requestSession->write('Flash.flash.' . $key . '.message', $expectedMessage);

            $this->assertFlashMessage($expectedMessage, (int)$key);
            $this->assertFlashMessage($expectedMessage, (string)$key);
        }

        //Call without key
        $this->assertFlashMessage($messages[0]);
    }

    /**
     * Test for `assertResponseOkAndNotEmpty()` method
     * @test
     */
    public function testAssertResponseOkAndNotEmpty()
    {
        $this->assertResponseOkAndNotEmpty();
    }
}
