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

use Cake\Controller\Controller;
use Cake\Event\EventInterface;
use Cake\TestSuite\Constraint\Session\SessionEquals;
use Cake\TestSuite\IntegrationTestTrait as CakeIntegrationTestTrait;
use MeTools\Controller\Component\UploaderComponent;

/**
 * A trait intended to make integration tests of your controllers easier
 */
trait IntegrationTestTrait
{
    use CakeIntegrationTestTrait {
        CakeIntegrationTestTrait::controllerSpy as cakeControllerSpy;
    }

    /**
     * Adds additional event spies to the controller/view event manager
     * @param \Cake\Event\EventInterface $event A dispatcher event
     * @param \Cake\Controller\Controller|null $controller Controller instance
     * @return void
     */
    public function controllerSpy(EventInterface $event, ?Controller $controller = null): void
    {
        $this->cakeControllerSpy($event, $controller);

        $this->_controller->viewBuilder()->setLayout('with_flash');

        if ($this->_controller->components()->has('Uploader')) {
            $this->_controller->Uploader = $this->getMockForComponent(UploaderComponent::class, ['move_uploaded_file']);

            $this->_controller->Uploader->method('move_uploaded_file')
                ->will($this->returnCallback(function ($filename, $destination) {
                    return rename($filename, $destination);
                }));
        }
    }

    /**
     * Asserts that a cookie is empty
     * @param string $name The cookie name
     * @param string $message The failure message that will be appended to the
     *  generated message
     * @return void
     */
    public function assertCookieIsEmpty(string $name, string $message = ''): void
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
    public function assertFlashMessage(string $expected, int $key = 0, string $message = ''): void
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
    public function assertResponseOkAndNotEmpty(string $message = ''): void
    {
        $this->assertResponseOk($message) && $this->assertResponseNotEmpty($message);
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
        $verboseMessage = $this->extractVerboseMessage($message);
        $this->assertThat(null, new SessionEquals($this->_requestSession, $path), $verboseMessage);
    }
}
