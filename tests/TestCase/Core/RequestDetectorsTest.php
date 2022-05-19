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
namespace MeTools\Test\TestCase\Core;

use Cake\Http\ServerRequest;
use Cake\Routing\RouteBuilder;
use Cake\Routing\Router;
use MeTools\TestSuite\TestCase;

/**
 * RequestDetectorsTest class
 */
class RequestDetectorsTest extends TestCase
{
    /**
     * @var \Cake\Http\ServerRequest
     */
    public $Request;

    /**
     * Called before every test method
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $router = Router::createRouteBuilder('/');
        $router->scope('/', function (RouteBuilder $routes): void {
            $routes->connect('/', ['controller' => 'pages', 'action' => 'display', 'home']);
            $routes->connect('/some_alias', ['controller' => 'tests_apps', 'action' => 'some_method']);
            $routes->fallbacks();
        });

        $this->Request = (new ServerRequest())->withParam('action', 'myAction')
            ->withParam('controller', 'myController')
            ->withParam('prefix', 'myPrefix');
    }

    /**
     * Tests for `is('action')` detector
     * @test
     */
    public function testIsAction(): void
    {
        $this->assertTrue($this->Request->is('action', 'myAction'));
        $this->assertFalse($this->Request->is('action', 'notMyAction'));

        //Multiple actions
        $this->assertTrue($this->Request->is('action', ['myAction', 'notMyAction']));
        $this->assertFalse($this->Request->is('action', ['notMyAction', 'againNotMyAction']));

        //Action + Controller
        $this->assertTrue($this->Request->is('action', 'myAction', 'myController'));
        $this->assertFalse($this->Request->is('action', 'myAction', 'notMyController'));

        //Multiple actions + controller
        $this->assertTrue($this->Request->is('action', ['myAction', 'notMyAction'], 'myController'));
        $this->assertTrue($this->Request->is('action', ['myAction', 'notMyAction'], ['myController', 'notMyController']));
        $this->assertFalse($this->Request->is('action', ['notMyAction', 'againNotMyAction'], 'myController'));
        $this->assertFalse($this->Request->is('action', ['myAction', 'notMyAction'], 'notMyController'));
        $this->assertFalse($this->Request->is('action', ['notMyAction', 'againNotMyAction'], 'notMyController'));
    }

    /**
     * Tests for `is('controller')` detector
     * @test
     */
    public function testIsController(): void
    {
        $this->assertTrue($this->Request->is('controller', 'myController'));
        $this->assertFalse($this->Request->is('controller', 'notMyController'));

        //Multiple controllers
        $this->assertTrue($this->Request->is('controller', ['myController', 'notMyController']));
        $this->assertFalse($this->Request->is('controller', ['notMyController', 'againNotMyController']));
    }

    /**
     * Tests for `is('localhost')` detector
     * @test
     */
    public function testIsLocalhost(): void
    {
        $this->assertFalse($this->Request->is('localhost'));

        foreach (['127.0.0.1', '::1'] as $remoteIp) {
            $this->Request = $this->Request->withEnv('REMOTE_ADDR', $remoteIp);
            $this->assertTrue($this->Request->is('localhost'));
        }
    }

    /**
     * Tests for `is('prefix')` detector
     * @test
     */
    public function testIsPrefix(): void
    {
        $this->assertTrue($this->Request->is('prefix', 'myPrefix'));
        $this->assertTrue($this->Request->is('prefix', ['myPrefix', 'notMyPrefix']));
        $this->assertFalse($this->Request->is('prefix', 'notMyPrefix'));
    }

    /**
     * Tests for `is('url')` detector
     * @test
     */
    public function testIsUrl(): void
    {
        $this->Request = $this->Request->withEnv('REQUEST_URI', '/some_alias');

        //Url as array of params
        $this->assertTrue($this->Request->is('url', ['controller' => 'tests_apps', 'action' => 'some_method']));
        $this->assertFalse($this->Request->is('url', ['controller' => 'tests_apps', 'action' => 'noMethod']));

        //Urls as strings
        $this->assertTrue($this->Request->is('url', '/some_alias'));
        $this->assertTrue($this->Request->is('url', '/some_alias/'));
        $this->assertFalse($this->Request->is('url', '/some_alias/noExisting'));

        $this->Request = $this->Request->withEnv('REQUEST_URI', '/');

        //Url as array of params
        $this->assertTrue($this->Request->is('url', ['controller' => 'pages', 'action' => 'display', 'home']));
        $this->assertFalse($this->Request->is('url', ['controller' => 'pages', 'action' => 'noExisting', 'home']));
        $this->assertFalse($this->Request->is('url', ['controller' => 'pages', 'action' => 'display', 'noExisting']));

        //Urls as strings
        $this->assertTrue($this->Request->is('url', '/'));
        $this->assertFalse($this->Request->is('url', '/noExisting'));
    }

    /**
     * Tests for `is('url')` detector, with query strings
     * @test
     */
    public function testIsUrlQueryString(): void
    {
        $this->Request = $this->Request->withEnv('REQUEST_URI', '/some_alias');
        $this->assertTrue($this->Request->is('url', '/some_alias'));
        $this->assertTrue($this->Request->is('url', '/some_alias', false));

        $this->Request = $this->Request->withEnv('REQUEST_URI', '/some_alias?key=value');
        $this->assertTrue($this->Request->is('url', '/some_alias'));
        $this->assertFalse($this->Request->is('url', '/some_alias', false));
    }
}
