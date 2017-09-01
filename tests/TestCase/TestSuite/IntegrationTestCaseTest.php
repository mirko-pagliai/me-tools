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

use Cake\Network\Session;
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
        $this->_requestSession->write('Flash.flash.0.message', 'first flash');
        $this->_requestSession->write('Flash.flash.1.message', 'second flash');

        $this->assertFlashMessage('first flash');
        $this->assertFlashMessage('first flash', 0);
        $this->assertFlashMessage('first flash', '0');
        $this->assertFlashMessage('second flash', 1);
        $this->assertFlashMessage('second flash', '1');
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
