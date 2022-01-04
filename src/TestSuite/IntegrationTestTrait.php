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
use Cake\Core\Configure;
use Cake\Event\EventInterface;
use Cake\TestSuite\Constraint\Session\SessionEquals;
use Cake\TestSuite\IntegrationTestTrait as CakeIntegrationTestTrait;
use MeTools\Controller\Component\UploaderComponent;

/**
 * A trait intended to make integration tests of your controllers easier
 * @method string _getBodyAsString() Get the response body as string
 * @method void disableErrorHandlerMiddleware() Disable the error handler middleware
 * @property \Cake\Controller\Controller $_controller
 * @property \Cake\Http\Response $_response
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
            /** @var \MeTools\Controller\Component\UploaderComponent&\PHPUnit\Framework\MockObject\MockObject $Uploader */
            $Uploader = $this->getMockForComponent(UploaderComponent::class, ['move_uploaded_file']);
            $Uploader->method('move_uploaded_file')
                ->will($this->returnCallback(function (string $filename, string $destination): bool {
                    return rename($filename, $destination);
                }));
            /** @phpstan-ignore-next-line */
            $this->_controller->Uploader = $Uploader;
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
        $cookie = $this->_response->getCookie($name);
        $this->assertTrue(!isset($cookie['value']) || !$cookie['value'], $message);
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
        $verboseMessage = $this->extractVerboseMessage($message);
        /**
         * @todo to be removed when a CakePHP version greater than `4.1` is required
         */
        /** @phpstan-ignore-next-line */
        $sessionEquals = version_compare(Configure::version(), '4.1', '>=') ? new SessionEquals($path) : new SessionEquals($this->_requestSession, $path);
        $this->assertThat(null, $sessionEquals, $verboseMessage);
    }
}
