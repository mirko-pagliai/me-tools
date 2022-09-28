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

namespace MeTools\Test\TestCase\Controller\Component;

use MeTools\TestSuite\ComponentTestCase;

/**
 * FlashComponentTest class
 * @property \MeTools\Controller\Component\FlashComponent $Component
 */
class FlashComponentTest extends ComponentTestCase
{
    /**
     * Tests for `__call()` method
     * @uses \MeTools\Controller\Component\FlashComponent::__call()
     * @test
     */
    public function testMagicCall(): void
    {
        $session = $this->Component->getController()->getRequest()->getSession();
        $text = 'My message';

        foreach ([
                     'alert' => 'alert-warning',
                     'error' => 'alert-danger',
                     'notice' => 'alert-info',
                     'success' => 'alert-success',
                 ] as $method => $expectedClass) {
            $expected = [[
                'message' => $text,
                'key' => 'flash',
                'element' => 'MeTools.flash/flash',
                'params' => ['class' => $expectedClass],
            ]];
            $this->Component->$method($text);
            $this->assertEquals($expected, $session->read('Flash.flash'));
            $session->delete('Flash.flash');
        }

        //With custom class
        $expected = [[
            'message' => $text,
            'key' => 'flash',
            'element' => 'MeTools.flash/flash',
            'params' => ['class' => 'my-class'],
        ]];
        $this->Component->success($text, ['params' => ['class' => 'my-class']]);
        $this->assertEquals($expected, $session->read('Flash.flash'));
        $session->delete('Flash.flash');

        //With other name
        $expected = [[
            'message' => $text,
            'key' => 'flash',
            'element' => 'flash/other_name',
            'params' => [],
        ]];
        /** @noinspection PhpUndefinedMethodInspection */
        $this->Component->otherName($text);
        $this->assertEquals($expected, $session->read('Flash.flash'));
        $session->delete('Flash.flash');

        //With plugin as `false`
        $expected = [[
            'message' => $text,
            'key' => 'flash',
            'element' => 'flash/success',
            'params' => [],
        ]];
        $this->Component->success($text, ['plugin' => false]);
        $this->assertEquals($expected, $session->read('Flash.flash'));
        $session->delete('Flash.flash');

        //With other plugin
        $expected = [[
            'message' => $text,
            'key' => 'flash',
            'element' => 'MyPlugin.flash/success',
            'params' => [],
        ]];
        $this->Component->success($text, ['plugin' => 'MyPlugin']);
        $this->assertEquals($expected, $session->read('Flash.flash'));
        $session->delete('Flash.flash');
    }
}
