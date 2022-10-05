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
 */
namespace MeTools\Test\TestCase\TestSuite;

use Cake\Controller\Controller;
use Cake\Event\Event;
use Cake\Http\Cookie\Cookie;
use Cake\Http\Response;
use Cake\Http\Session;
use MeTools\TestSuite\IntegrationTestTrait;
use MeTools\TestSuite\TestCase;
use Tools\Filesystem;

/**
 * IntegrationTestTraitTest class
 */
class IntegrationTestTraitTest extends TestCase
{
    use IntegrationTestTrait;

    /**
     * Called before every test method
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->_response = new Response();
    }

    /**
     * Test for `controllerSpy()` method
     * @test
     * @noinspection PhpUndefinedFieldInspection
     */
    public function testControllerSpy(): void
    {
        $this->_controller = new Controller();
        $this->_controller->loadComponent('MeTools.Uploader');
        $this->controllerSpy(new Event('myEvent'), $this->_controller);

        $this->assertIsMock($this->_controller->Uploader);
        $source = Filesystem::instance()->createTmpFile();
        $destination = TMP . 'example2';
        $this->assertFileDoesNotExist($destination);
        $this->invokeMethod($this->_controller->Uploader, 'move_uploaded_file', [$source, $destination]);
        $this->assertFileDoesNotExist($source);
        $this->assertFileExists($destination);
        unlink($destination);
    }

    /**
     * Test for `assertCookieIsEmpty()` method
     * @test
     */
    public function testAssertCookieIsEmpty(): void
    {
        $this->assertCookieIsEmpty('test-cookie');

        $this->_response = $this->_response->withCookie(new Cookie('test-cookie', ''));
        $this->assertCookieIsEmpty('test-cookie');

        //With no response
        $this->expectAssertionFailed('Not response set, cannot assert cookies');
        $this->_response = null;
        $this->assertCookieIsEmpty('test-cookie');
    }

    /**
     * Test for `assertResponseOkAndNotEmpty()` method
     * @test
     */
    public function testAssertResponseOkAndNotEmpty(): void
    {
        $this->_response = new Response(['body' => 'string']);
        $this->assertResponseOkAndNotEmpty();
    }

    /**
     * Test for `assertSessionEmpty()` method
     * @test
     */
    public function testAssertSessionEmpty(): void
    {
        $this->_requestSession = new Session();
        $this->_requestSession->write('first.second', 'value');
        $this->_requestSession->write('first.third', 'value');
        $this->assertSessionEmpty('first.fourth');

        $this->_requestSession->delete('first.third');
        $this->assertSessionEmpty('first.third');

        $this->expectAssertionFailed();
        $this->assertSessionEmpty('first.second');
    }

    /**
     * Test for `getStatusCode()` method
     * @test
     * @uses \MeTools\TestSuite\IntegrationTestTrait::getStatusCode()
     */
    public function testGetStatusCode(): void
    {
        $this->_response = new Response(['status' => 302]);
        $this->assertSame(302, $this->getStatusCode());

        $this->expectAssertionFailed();
        $this->_response = null;
        $this->getStatusCode();
    }
}
