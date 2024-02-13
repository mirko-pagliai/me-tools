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

use Cake\View\View;
use MeTools\TestSuite\TestCase;
use MeTools\View\Helper\IconHelper;

/**
 * IconHelperTest class
 */
class IconHelperTest extends TestCase
{
    /**
     * @var \MeTools\View\Helper\IconHelper
     */
    protected IconHelper $Helper;

    /**
     * @inheritDoc
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->Helper ??= new IconHelper(new View());
    }

    /**
     * @test
     * @uses \MeTools\View\Helper\IconHelper::addIconToText()
     */
    public function testAddIconToText(): void
    {
        $text = 'My text';

        $options = ['icon' => 'home'];
        [$result, $options] = $this->Helper->addIconToText($text, $options);
        $this->assertEquals('<i class="fa fa-home"> </i> ' . $text, $result);
        $this->assertArrayNotHasKey('icon', $options);
        $this->assertArrayNotHasKey('icon-align', $options);

        //Missing `icon` option
        $options = ['class' => 'my-class', 'icon-align' => 'right'];
        [$result, $options] = $this->Helper->addIconToText($text, $options);
        $this->assertEquals($text, $result);
        $this->assertArrayNotHasKey('icon', $options);
        $this->assertArrayNotHasKey('icon-align', $options);
        $this->assertEquals('my-class', $options['class']);

        //Empty text
        $options = ['icon' => 'home'];
        [$result, $options] = $this->Helper->addIconToText(null, $options);
        $this->assertEquals('<i class="fa fa-home"> </i>', $result);
        $this->assertArrayNotHasKey('icon', $options);
        $this->assertArrayNotHasKey('icon-align', $options);

        //Using `icon-align` option
        $options = ['icon' => 'home', 'icon-align' => 'right'];
        [$result, $options] = $this->Helper->addIconToText($text, $options);
        $this->assertEquals($text . ' <i class="fa fa-home"> </i>', $result);
        $this->assertArrayNotHasKey('icon', $options);
        $this->assertArrayNotHasKey('icon-align', $options);

        //Invalid `icon-align` option
        $options = ['icon' => 'home', 'icon-align' => 'left'];
        [$result, $options] = $this->Helper->addIconToText($text, $options);
        $this->assertEquals('<i class="fa fa-home"> </i> ' . $text, $result);
        $this->assertArrayNotHasKey('icon', $options);
        $this->assertArrayNotHasKey('icon-align', $options);
    }

    /**
     * @test
     * @uses \MeTools\View\Helper\IconHelper::icon()
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

        $expected = '<i class="fa-brands fa-linux"> </i>';
        $this->assertSame($expected, $this->Helper->icon('brands', 'linux'));

        $expected = '<i class="fa fa-hand-o-right fa-2x"> </i>';
        foreach (['hand-o-right 2x', ['hand-o-right', '2x']] as $icons) {
            $this->assertSame($expected, $this->Helper->icon($icons));
        }
        $this->assertSame($expected, $this->Helper->icon('hand-o-right', '2x'));
    }
}
