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

        $controller = new Controller(new Request);
        $componentRegistry = new ComponentRegistry($controller);
        $this->Flash = new FlashComponent($componentRegistry);
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

        $this->Flash->alert($text);

        $result = $this->Session->read('Flash.flash');
        $expected = [[
            'message' => $text,
            'key' => 'flash',
            'element' => 'MeTools.Flash/flash',
            'params' => ['class' => 'alert-warning'],
        ]];
        $this->assertEquals($expected, $result);

        $this->Flash->error($text);

        $result = $this->Session->read('Flash.flash');
        $expected[] = [
            'message' => $text,
            'key' => 'flash',
            'element' => 'MeTools.Flash/flash',
            'params' => ['class' => 'alert-danger'],
        ];
        $this->assertEquals($expected, $result);

        $this->Flash->notice($text);

        $result = $this->Session->read('Flash.flash');
        $expected[] = [
            'message' => $text,
            'key' => 'flash',
            'element' => 'MeTools.Flash/flash',
            'params' => ['class' => 'alert-info'],
        ];
        $this->assertEquals($expected, $result);

        $this->Flash->success($text);

        $result = $this->Session->read('Flash.flash');
        $expected[] = [
            'message' => $text,
            'key' => 'flash',
            'element' => 'MeTools.Flash/flash',
            'params' => ['class' => 'alert-success'],
        ];
        $this->assertEquals($expected, $result);

        //With custom class
        $this->Flash->success($text, ['params' => ['class' => 'my-class']]);

        $result = $this->Session->read('Flash.flash');
        $expected[] = [
            'message' => $text,
            'key' => 'flash',
            'element' => 'MeTools.Flash/flash',
            'params' => ['class' => 'my-class'],
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
