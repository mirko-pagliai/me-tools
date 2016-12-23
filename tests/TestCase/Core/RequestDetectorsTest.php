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
     * Tests detectors
     * @return void
     * @test
     */
    public function testDetectors()
    {
        //Creates request
        $request = new Request();
        $request->params = [
            'action' => 'myAction',
            'controller' => 'myController',
            'prefix' => 'myPrefix',
        ];

        //Controller
        $this->assertTrue($request->is('controller', 'myController'));
        $this->assertFalse($request->is('controller', 'notMyController'));
        $this->assertTrue($request->isController('myController'));
        $this->assertFalse($request->isController('notMyController'));

        //Multiple controllers
        $this->assertTrue($request->isController(['myController', 'notMyController']));
        $this->assertFalse($request->isController(['notMyController', 'againNotMyController']));

        //Action
        $this->assertTrue($request->is('action', 'myAction'));
        $this->assertFalse($request->is('action', 'notMyAction'));
        $this->assertTrue($request->isAction('myAction'));
        $this->assertFalse($request->isAction('notMyAction'));

        //Multiple actions
        $this->assertTrue($request->isAction(['myAction', 'notMyAction']));
        $this->assertFalse($request->isAction(['notMyAction', 'againNotMyAction']));

        //Action + Controller
        $this->assertTrue($request->is('action', 'myAction', 'myController'));
        $this->assertFalse($request->is('action', 'myAction', 'notMyController'));
        $this->assertTrue($request->isAction('myAction', 'myController'));
        $this->assertFalse($request->isAction('myAction', 'notMyController'));

        //Multiple actions + controller
        $this->assertTrue($request->isAction(['myAction', 'notMyAction'], 'myController'));
        $this->assertFalse($request->isAction(['notMyAction', 'againNotMyAction'], 'myController'));
        $this->assertFalse($request->isAction(['myAction', 'notMyAction'], 'notMyController'));
        $this->assertFalse($request->isAction(['notMyAction', 'againNotMyAction'], 'notMyController'));

        //Prefix
        $this->assertTrue($request->is('prefix', 'myPrefix'));
        $this->assertFalse($request->is('prefix', 'notMyPrefix'));
        $this->assertTrue($request->isPrefix('myPrefix'));
        $this->assertFalse($request->isPrefix('notMyPrefix'));

        //Create request
        $request = new Request();
        $request->here = '/some_alias';

        //Url as array of params
        $this->assertTrue($request->is('url', ['controller' => 'tests_apps', 'action' => 'some_method']));
        $this->assertTrue($request->isUrl(['controller' => 'tests_apps', 'action' => 'some_method']));
        $this->assertFalse($request->is('url', ['controller' => 'tests_apps', 'action' => 'noMethod']));
        $this->assertFalse($request->isUrl(['controller' => 'tests_apps', 'action' => 'noMethod']));

        //Urls as strings
        $this->assertTrue($request->is('url', '/some_alias'));
        $this->assertTrue($request->isUrl('/some_alias'));
        $this->assertTrue($request->is('url', '/some_alias/'));
        $this->assertTrue($request->isUrl('/some_alias/'));
        $this->assertFalse($request->is('url', '/some_alias/noExisting'));
        $this->assertFalse($request->isUrl('/some_alias/noExisting'));

        //Create request
        $request = new Request();
        $request->here = '/';

        //Url as array of params
        $this->assertTrue($request->is('url', ['controller' => 'pages', 'action' => 'display', 'home']));
        $this->assertTrue($request->isUrl(['controller' => 'pages', 'action' => 'display', 'home']));
        $this->assertFalse($request->is('url', ['controller' => 'pages', 'action' => 'noExisting', 'home']));
        $this->assertFalse($request->isUrl(['controller' => 'pages', 'action' => 'noExisting', 'home']));
        $this->assertFalse($request->is('url', ['controller' => 'pages', 'action' => 'display', 'noExisting']));
        $this->assertFalse($request->isUrl(['controller' => 'pages', 'action' => 'display', 'noExisting']));

        //Urls as strings
        $this->assertTrue($request->is('url', '/'));
        $this->assertTrue($request->isUrl('/'));
        $this->assertFalse($request->is('url', '/noExisting'));
        $this->assertFalse($request->isUrl('/noExisting'));
    }
}
