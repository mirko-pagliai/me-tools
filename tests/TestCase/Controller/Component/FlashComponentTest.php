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

use MeTools\TestSuite\ComponentTestCase;

/**
 * FlashComponentTest class
 */
class FlashComponentTest extends ComponentTestCase
{
    /**
     * Tests for `__call()` method
     * @test
     */
    public function testMagicCall()
    {
        $text = 'My message';
        $expectedClasses = [
            'alert' => 'alert-warning',
            'error' => 'alert-danger',
            'notice' => 'alert-info',
            'success' => 'alert-success',
        ];

        foreach ($expectedClasses as $methodToCall => $expectedClass) {
            $expected = [[
                'message' => $text,
                'key' => 'flash',
                'element' => 'MeTools.Flash/flash',
                'params' => ['class' => $expectedClass],
            ]];
            call_user_func([$this->Component, $methodToCall], $text);
            $this->assertEquals($expected, $this->Component->_session->read('Flash.flash'));
            $this->Component->_session->delete('Flash.flash');
        }

        //With custom class
        $expected = [[
            'message' => $text,
            'key' => 'flash',
            'element' => 'MeTools.Flash/flash',
            'params' => ['class' => 'my-class'],
        ]];
        $this->Component->success($text, ['params' => ['class' => 'my-class']]);
        $this->assertEquals($expected, $this->Component->_session->read('Flash.flash'));
        $this->Component->_session->delete('Flash.flash');

        //With other name
        $expected = [[
            'message' => $text,
            'key' => 'flash',
            'element' => 'Flash/other_name',
            'params' => [],
        ]];
        $this->Component->otherName($text);
        $this->assertEquals($expected, $this->Component->_session->read('Flash.flash'));
        $this->Component->_session->delete('Flash.flash');

        //With plugin as `false`
        $expected = [[
            'message' => $text,
            'key' => 'flash',
            'element' => 'Flash/success',
            'params' => [],
        ]];
        $this->Component->success($text, ['plugin' => false]);
        $this->assertEquals($expected, $this->Component->_session->read('Flash.flash'));
        $this->Component->_session->delete('Flash.flash');

        //With other plugin
        $expected = [[
            'message' => $text,
            'key' => 'flash',
            'element' => 'MyPlugin.Flash/success',
            'params' => [],
        ]];
        $this->Component->success($text, ['plugin' => 'MyPlugin']);
        $this->assertEquals($expected, $this->Component->_session->read('Flash.flash'));
        $this->Component->_session->delete('Flash.flash');
    }
}
