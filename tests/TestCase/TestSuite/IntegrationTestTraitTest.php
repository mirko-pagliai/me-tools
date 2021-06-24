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
use PHPUnit\Framework\AssertionFailedError;
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
    public function setUp(): void
    {
        parent::setUp();

        $this->_response = new Response();
    }

    /**
     * Test for `controllerSpy()` method
     * @test
     */
    public function testControllerSpy(): void
    {
        $this->_controller = new Controller();
        $this->_controller->loadComponent('MeTools.Uploader');
        $this->controllerSpy(new Event('myEvent'), $this->_controller);
        $this->assertEquals('with_flash', $this->_controller->viewBuilder()->getLayout());

        /** @phpstan-ignore-next-line */
        $this->assertIsMock($this->_controller->Uploader);
        $source = (new Filesystem())->createTmpFile();
        $destination = TMP . 'example2';
        $this->assertFileDoesNotExist($destination);
        /** @phpstan-ignore-next-line */
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
        $this->expectException(AssertionFailedError::class);
        $this->expectExceptionMessage('Not response set, cannot assert cookies');
        /** @phpstan-ignore-next-line */
        $this->_response = null;
        $this->assertCookieIsEmpty('test-cookie');
    }

    /**
     * Test for `assertFlashMessage()` method
     * @test
     */
    public function testAssertFlashMessage(): void
    {
        $messages = ['first flash', 'second flash'];
        $this->_requestSession = new Session();

        foreach ($messages as $key => $expectedMessage) {
            $this->_requestSession->write('Flash.flash.' . $key . '.message', $expectedMessage);
            $this->assertFlashMessage($expectedMessage, (int)$key);
        }

        //Call without key
        $this->assertFlashMessage($messages[0]);
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
    public function testSessionEmpty(): void
    {
        $this->_requestSession = new Session();
        $this->_requestSession->write('first.second', 'value');
        $this->_requestSession->write('first.third', 'value');
        $this->assertSessionEmpty('first.fourth');

        $this->_requestSession->delete('first.third');
        $this->assertSessionEmpty('first.third');

        $this->expectException(AssertionFailedError::class);
        $this->assertSessionEmpty('first.second');
    }
}
