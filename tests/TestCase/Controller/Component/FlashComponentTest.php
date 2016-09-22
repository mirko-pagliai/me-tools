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
namespace MeTools\Test\TestCase\Controller\Component;

use Cake\Controller\ComponentRegistry;
use Cake\Controller\Controller;
use Cake\Network\Request;
use Cake\Network\Session;
use Cake\TestSuite\TestCase;
use MeTools\Controller\Component\FlashComponent;

/**
 * FlashComponentTest class
 */
class FlashComponentTest extends TestCase
{
    /**
     * Setup the test case, backup the static object values so they can be
     * restored. Specifically backs up the contents of Configure and paths in
     *  App if they have not already been backed up
     * @return void
     */
    public function setUp()
    {
        parent::setUp();

        $this->Controller = new Controller(new Request(['session' => new Session()]));
        $this->ComponentRegistry = new ComponentRegistry($this->Controller);
        $this->Flash = new FlashComponent($this->ComponentRegistry);
        $this->Session = new Session();
    }

    /**
     * Teardown any static object changes and restore them
     * @return void
     */
    public function tearDown()
    {
        parent::tearDown();

        $this->Session->destroy();
    }

    /**
     * Tests for `__call()` method
     * @return void
     * @test
     */
    public function testMagicCall()
    {
        $text = 'My message';

        $this->assertNull($this->Session->read('Flash.flash'));

        $this->Flash->alert($text);

        $result = $this->Session->read('Flash.flash');
        $expected = [
            [
                'message' => $text,
                'key' => 'flash',
                'element' => 'MeTools.Flash/flash',
                'params' => [
                    'class' => 'alert-warning'
                ],
            ],
        ];
        $this->assertEquals($expected, $result);

        $this->Flash->error($text);

        $result = $this->Session->read('Flash.flash');
        $expected[] = [
            'message' => $text,
            'key' => 'flash',
            'element' => 'MeTools.Flash/flash',
            'params' => [
                'class' => 'alert-danger'
            ],
        ];
        $this->assertEquals($expected, $result);

        $this->Flash->notice($text);

        $result = $this->Session->read('Flash.flash');
        $expected[] = [
            'message' => $text,
            'key' => 'flash',
            'element' => 'MeTools.Flash/flash',
            'params' => [
                'class' => 'alert-info'
            ],
        ];
        $this->assertEquals($expected, $result);

        $this->Flash->success($text);

        $result = $this->Session->read('Flash.flash');
        $expected[] = [
            'message' => $text,
            'key' => 'flash',
            'element' => 'MeTools.Flash/flash',
            'params' => [
                'class' => 'alert-success'
            ],
        ];
        $this->assertEquals($expected, $result);

        //With custom class
        $this->Flash->success($text, ['params' => ['class' => 'my-class']]);

        $result = $this->Session->read('Flash.flash');
        $expected[] = [
            'message' => $text,
            'key' => 'flash',
            'element' => 'MeTools.Flash/flash',
            'params' => [
                'class' => 'my-class',
            ],
        ];
        $this->assertEquals($expected, $result);

        //With other name
        $this->Flash->otherName($text);

        $result = $this->Session->read('Flash.flash');
        $expected[] = [
            'message' => $text,
            'key' => 'flash',
            'element' => 'Flash/other_name',
            'params' => [],
        ];
        $this->assertEquals($expected, $result);

        //With plugin as `false`
        $this->Flash->success($text, ['plugin' => false]);

        $result = $this->Session->read('Flash.flash');
        $expected[] = [
            'message' => $text,
            'key' => 'flash',
            'element' => 'Flash/success',
            'params' => [],
        ];
        $this->assertEquals($expected, $result);

        //With other plugin
        $this->Flash->success($text, ['plugin' => 'MyPlugin']);

        $result = $this->Session->read('Flash.flash');
        $expected[] = [
            'message' => $text,
            'key' => 'flash',
            'element' => 'MyPlugin.Flash/success',
            'params' => [],
        ];
        $this->assertEquals($expected, $result);
    }
}
