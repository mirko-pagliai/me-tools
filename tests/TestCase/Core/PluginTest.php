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
namespace MeTools\Test\TestCase\Core;

use MeTools\Core\Plugin;
use MeTools\TestSuite\TestCase;

/**
 * PluginTest class.
 */
class PluginTest extends TestCase
{
    /**
     * Tests for `all()` method
     * @test
     */
    public function testAll()
    {
        $expected = ['MeTools', 'Assets'];
        $this->assertEquals($expected, Plugin::all());

        $this->app->addPlugin('TestPlugin');

        $expected = ['MeTools', 'Assets', 'TestPlugin'];
        $this->assertEquals($expected, Plugin::all());

        $expected = ['MeTools', 'Assets'];
        $this->assertEquals($expected, Plugin::all(['exclude' => 'TestPlugin']));

        $this->app->addPlugin('AnotherTestPlugin');

        $expected = ['MeTools', 'AnotherTestPlugin', 'Assets', 'TestPlugin'];
        $this->assertEquals($expected, Plugin::all());

        $expected = ['AnotherTestPlugin', 'Assets', 'MeTools', 'TestPlugin'];
        $this->assertEquals($expected, Plugin::all(['order' => false]));
    }

    /**
     * Tests for `path()` method
     * @test
     */
    public function testPath()
    {
        $this->assertEquals(ROOT, Plugin::path('MeTools'));
        $this->assertFalse(Plugin::path('MeTools', 'no_existing.php', true));

        $file = 'src' . DS . 'Console' . DS . 'Shell.php';

        $this->assertEquals(ROOT . $file, Plugin::path('MeTools', $file));
        $this->assertEquals(ROOT . $file, Plugin::path('MeTools', $file, true));

        $expected = [ROOT . $file, ROOT . 'no_existing.php'];
        $result = Plugin::path('MeTools', [$file, 'no_existing.php']);
        $this->assertEquals($expected, $result);

        //Only the first file exists
        $result = Plugin::path('MeTools', [$file, 'no_existing.php'], true);
        $this->assertEquals([ROOT . $file], $result);

        //No existing files
        $result = Plugin::path('MeTools', ['no_existing.php', 'no_existing2.php'], true);
        $this->assertEmpty($result);
    }
}
