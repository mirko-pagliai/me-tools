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
namespace MeTools\Test\TestCase\Controller\Component;

use Cake\Controller\ComponentRegistry;
use Cake\Controller\Controller;
use Cake\Network\Request;
use Cake\Network\Session;
use MeTools\Controller\Component\FlashComponent;
use MeTools\TestSuite\TestCase;

/**
 * FlashComponentTest class
 */
class FlashComponentTest extends TestCase
{
    /**
     * @var \MeTools\Controller\Component\FlashComponent
     */
    protected $Flash;

    /**
     * @var \Cake\Network\Session
     */
    protected $Session;

    /**
     * Setup the test case, backup the static object values so they can be
     * restored. Specifically backs up the contents of Configure and paths in
     *  App if they have not already been backed up
     * @return void
     */
    public function setUp()
    {
        parent::setUp();

        $this->Flash = new FlashComponent(new ComponentRegistry(new Controller(new Request)));
        $this->Session = new Session;
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

        foreach ([
            'alert' => 'alert-warning',
            'error' => 'alert-danger',
            'notice' => 'alert-info',
            'success' => 'alert-success',
        ] as $methodToCall => $expectedClass) {
            $expected = [[
                'message' => $text,
                'key' => 'flash',
                'element' => 'MeTools.Flash/flash',
                'params' => ['class' => $expectedClass],
            ]];
            call_user_func([$this->Flash, $methodToCall], $text);
            $this->assertEquals($expected, $this->Session->read('Flash.flash'));
            $this->Session->delete('Flash.flash');
        }

        //With custom class
        $expected = [[
            'message' => $text,
            'key' => 'flash',
            'element' => 'MeTools.Flash/flash',
            'params' => ['class' => 'my-class'],
        ]];
        $this->Flash->success($text, ['params' => ['class' => 'my-class']]);
        $this->assertEquals($expected, $this->Session->read('Flash.flash'));
        $this->Session->delete('Flash.flash');

        //With other name
        $expected = [[
            'message' => $text,
            'key' => 'flash',
            'element' => 'Flash/other_name',
            'params' => [],
        ]];
        $this->Flash->otherName($text);
        $this->assertEquals($expected, $this->Session->read('Flash.flash'));
        $this->Session->delete('Flash.flash');

        //With plugin as `false`
        $expected = [[
            'message' => $text,
            'key' => 'flash',
            'element' => 'Flash/success',
            'params' => [],
        ]];
        $this->Flash->success($text, ['plugin' => false]);
        $this->assertEquals($expected, $this->Session->read('Flash.flash'));
        $this->Session->delete('Flash.flash');

        //With other plugin
        $expected = [[
            'message' => $text,
            'key' => 'flash',
            'element' => 'MyPlugin.Flash/success',
            'params' => [],
        ]];
        $this->Flash->success($text, ['plugin' => 'MyPlugin']);
        $this->assertEquals($expected, $this->Session->read('Flash.flash'));
        $this->Session->delete('Flash.flash');
    }
}
