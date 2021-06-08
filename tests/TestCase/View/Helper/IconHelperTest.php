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
namespace MeTools\Test\TestCase\View\Helper;

use MeTools\TestSuite\HelperTestCase;
use MeTools\View\OptionsParser;

/**
 * IconHelperTest class
 */
class IconHelperTest extends HelperTestCase
{
    /**
     * Tests for `addIconToText()` method
     * @test
     */
    public function testAddIconToText(): void
    {
        $text = 'My text';

        $options = optionsParser(['icon' => 'home']);
        [$result, $options] = $this->Helper->addIconToText($text, $options);
        $this->assertEquals('<i class="fas fa-home"> </i> ' . $text, $result);
        $this->assertInstanceOf(OptionsParser::class, $options);
        $this->assertFalse($options->exists('icon'));
        $this->assertFalse($options->exists('icon-align'));

        //Missing `icon` option
        $options = optionsParser(['class' => 'my-class', 'icon-align' => 'right']);
        [$result, $options] = $this->Helper->addIconToText($text, $options);
        $this->assertEquals($text, $result);
        $this->assertInstanceOf(OptionsParser::class, $options);
        $this->assertFalse($options->exists('icon'));
        $this->assertFalse($options->exists('icon-align'));
        $this->assertEquals('my-class', $options->get('class'));

        //Empty text
        $options = optionsParser(['icon' => 'home']);
        [$result, $options] = $this->Helper->addIconToText(null, $options);
        $this->assertEquals('<i class="fas fa-home"> </i>', $result);
        $this->assertInstanceOf(OptionsParser::class, $options);
        $this->assertFalse($options->exists('icon'));
        $this->assertFalse($options->exists('icon-align'));

        //Using `icon-align` option
        $options = optionsParser(['icon' => 'home', 'icon-align' => 'right']);
        [$result, $options] = $this->Helper->addIconToText($text, $options);
        $this->assertEquals($text . ' <i class="fas fa-home"> </i>', $result);
        $this->assertInstanceOf(OptionsParser::class, $options);
        $this->assertFalse($options->exists('icon'));
        $this->assertFalse($options->exists('icon-align'));

        //Invalid `icon-align` option
        $options = optionsParser(['icon' => 'home', 'icon-align' => 'left']);
        [$result, $options] = $this->Helper->addIconToText($text, $options);
        $this->assertEquals('<i class="fas fa-home"> </i> ' . $text, $result);
        $this->assertInstanceOf(OptionsParser::class, $options);
        $this->assertFalse($options->exists('icon'));
        $this->assertFalse($options->exists('icon-align'));
    }

    /**
     * Test for `icon()` method
     * @test
     */
    public function testIcons(): void
    {
        $expected = ['i' => ['class' => 'preg:/(fa|fab|fal|far|fas) fa\-home/'], ' ', '/i'];
        foreach ([
            'home',
            'fa-home',
            'fa home',
            'fas home',
            'fab home',
            'fal home',
            'far home',
            'fas home',
            'fa fa-home',
            ['home'],
            ['fa', 'home'],
            ['fas', 'home'],
        ] as $icons) {
            $this->assertHtml($expected, $this->Helper->icon($icons));
        }
        $this->assertHtml($expected, $this->Helper->icon('fa', 'fa-home'));

        $expected = ['i' => ['class' => 'fas fa-hand-o-right fa-2x'], ' ', '/i'];
        foreach (['hand-o-right 2x', ['hand-o-right', '2x']] as $icons) {
            $this->assertHtml($expected, $this->Helper->icon($icons));
        }
        $this->assertHtml($expected, $this->Helper->icon('hand-o-right', '2x'));
    }
}
