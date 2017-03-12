<?php
/**
 * This file is part of MeTools.
 *
 * MeTools is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * MeTools is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with MeTools.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @author      Mirko Pagliai <mirko.pagliai@gmail.com>
 * @copyright   Copyright (c) 2016, Mirko Pagliai for Nova Atlantis Ltd
 * @license     http://www.gnu.org/licenses/agpl.txt AGPL License
 * @link        http://git.novatlantis.it Nova Atlantis Ltd
 */
namespace MeTools\Test\TestCase\Core;

use Cake\Network\Request;
use Cake\TestSuite\TestCase;

/**
 * RequestDetectorsTest class
 */
class RequestDetectorsTest extends TestCase
{
    /**
     * @var \Cake\Network\Request
     */
    public $Request;

    /**
     * Setup the test case, backup the static object values so they can be
     * restored. Specifically backs up the contents of Configure and paths in
     *  App if they have not already been backed up
     * @return void
     */
    public function setUp()
    {
        parent::setUp();

        //Creates request
        $this->Request = new Request;
        $this->Request->params = [
            'action' => 'myAction',
            'controller' => 'myController',
            'prefix' => 'myPrefix',
        ];
    }

    /**
     * Teardown any static object changes and restore them
     * @return void
     */
    public function tearDown()
    {
        parent::tearDown();

        unset($this->Request);
    }

    /**
     * Tests for `is('action')` detector
     * @return void
     * @test
     */
    public function testIsAction()
    {
        $this->assertTrue($this->Request->is('action', 'myAction'));
        $this->assertFalse($this->Request->is('action', 'notMyAction'));
        $this->assertTrue($this->Request->isAction('myAction'));
        $this->assertFalse($this->Request->isAction('notMyAction'));

        //Multiple actions
        $this->assertTrue($this->Request->isAction(['myAction', 'notMyAction']));
        $this->assertFalse($this->Request->isAction(['notMyAction', 'againNotMyAction']));

        //Action + Controller
        $this->assertTrue($this->Request->is('action', 'myAction', 'myController'));
        $this->assertFalse($this->Request->is('action', 'myAction', 'notMyController'));
        $this->assertTrue($this->Request->isAction('myAction', 'myController'));
        $this->assertFalse($this->Request->isAction('myAction', 'notMyController'));

        //Multiple actions + controller
        $this->assertTrue($this->Request->isAction(['myAction', 'notMyAction'], 'myController'));
        $this->assertFalse($this->Request->isAction(['notMyAction', 'againNotMyAction'], 'myController'));
        $this->assertFalse($this->Request->isAction(['myAction', 'notMyAction'], 'notMyController'));
        $this->assertFalse($this->Request->isAction(['notMyAction', 'againNotMyAction'], 'notMyController'));
    }

    /**
     * Tests for `is('controller')` detector
     * @return void
     * @test
     */
    public function testIsController()
    {
        $this->assertTrue($this->Request->is('controller', 'myController'));
        $this->assertFalse($this->Request->is('controller', 'notMyController'));
        $this->assertTrue($this->Request->isController('myController'));
        $this->assertFalse($this->Request->isController('notMyController'));

        //Multiple controllers
        $this->assertTrue($this->Request->isController(['myController', 'notMyController']));
        $this->assertFalse($this->Request->isController(['notMyController', 'againNotMyController']));
    }

    /**
     * Tests for `is('localhost')` detector
     * @return void
     * @test
     */
    public function testIsLocalhost()
    {
        $this->assertFalse($this->Request->is('localhost'));
        $this->assertFalse($this->Request->isLocalhost());

        $this->Request->env('REMOTE_ADDR', '127.0.0.1');

        $this->assertTrue($this->Request->is('localhost'));
        $this->assertTrue($this->Request->isLocalhost());

        $this->Request->env('REMOTE_ADDR', '::1');

        $this->assertTrue($this->Request->is('localhost'));
        $this->assertTrue($this->Request->isLocalhost());
    }

    /**
     * Tests for `is('prefix')` detector
     * @return void
     * @test
     */
    public function testIsPrefix()
    {
        $this->assertTrue($this->Request->is('prefix', 'myPrefix'));
        $this->assertFalse($this->Request->is('prefix', 'notMyPrefix'));
        $this->assertTrue($this->Request->isPrefix('myPrefix'));
        $this->assertFalse($this->Request->isPrefix('notMyPrefix'));
    }

    /**
     * Tests for `is('url')` detector
     * @return void
     * @test
     */
    public function testIsUrl()
    {
        $this->Request->here = '/some_alias';

        //Url as array of params
        $this->assertTrue($this->Request->is('url', ['controller' => 'tests_apps', 'action' => 'some_method']));
        $this->assertTrue($this->Request->isUrl(['controller' => 'tests_apps', 'action' => 'some_method']));
        $this->assertFalse($this->Request->is('url', ['controller' => 'tests_apps', 'action' => 'noMethod']));
        $this->assertFalse($this->Request->isUrl(['controller' => 'tests_apps', 'action' => 'noMethod']));

        //Urls as strings
        $this->assertTrue($this->Request->is('url', '/some_alias'));
        $this->assertTrue($this->Request->isUrl('/some_alias'));
        $this->assertTrue($this->Request->is('url', '/some_alias/'));
        $this->assertTrue($this->Request->isUrl('/some_alias/'));
        $this->assertFalse($this->Request->is('url', '/some_alias/noExisting'));
        $this->assertFalse($this->Request->isUrl('/some_alias/noExisting'));

        $this->Request->here = '/';

        //Url as array of params
        $this->assertTrue($this->Request->is('url', ['controller' => 'pages', 'action' => 'display', 'home']));
        $this->assertTrue($this->Request->isUrl(['controller' => 'pages', 'action' => 'display', 'home']));
        $this->assertFalse($this->Request->is('url', ['controller' => 'pages', 'action' => 'noExisting', 'home']));
        $this->assertFalse($this->Request->isUrl(['controller' => 'pages', 'action' => 'noExisting', 'home']));
        $this->assertFalse($this->Request->is('url', ['controller' => 'pages', 'action' => 'display', 'noExisting']));
        $this->assertFalse($this->Request->isUrl(['controller' => 'pages', 'action' => 'display', 'noExisting']));

        //Urls as strings
        $this->assertTrue($this->Request->is('url', '/'));
        $this->assertTrue($this->Request->isUrl('/'));
        $this->assertFalse($this->Request->is('url', '/noExisting'));
        $this->assertFalse($this->Request->isUrl('/noExisting'));
    }
}
