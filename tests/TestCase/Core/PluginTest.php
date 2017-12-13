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
     * Teardown any static object changes and restore them
     * @return void
     */
    public function tearDown()
    {
        parent::tearDown();

        Plugin::unload('TestPlugin');
        Plugin::unload('AnotherTestPlugin');
    }

    /**
     * Tests for `all()` method
     * @return void
     * @test
     */
    public function testAll()
    {
        $result = Plugin::all();
        $expected = [ME_TOOLS, ASSETS];
        $this->assertEquals($expected, $result);

        $result = Plugin::load('TestPlugin');
        $this->assertNull($result);

        $result = Plugin::all();
        $expected = [ME_TOOLS, ASSETS, 'TestPlugin'];
        $this->assertEquals($expected, $result);

        $result = Plugin::all(['exclude' => 'TestPlugin']);
        $expected = [ME_TOOLS, ASSETS];
        $this->assertEquals($expected, $result);

        $result = Plugin::load('AnotherTestPlugin');
        $this->assertNull($result);

        $result = Plugin::all();
        $expected = [ME_TOOLS, 'AnotherTestPlugin', ASSETS, 'TestPlugin'];
        $this->assertEquals($expected, $result);

        $result = Plugin::all(['order' => false]);
        $expected = ['AnotherTestPlugin', ASSETS, ME_TOOLS, 'TestPlugin'];
        $this->assertEquals($expected, $result);
    }

    /**
     * Tests for `path()` method
     * @return void
     * @test
     */
    public function testPath()
    {
        $result = Plugin::path(ME_TOOLS);
        $this->assertEquals(ROOT, $result);

        $expected = ROOT . 'config' . DS . 'bootstrap.php';

        $result = Plugin::path(ME_TOOLS, 'config' . DS . 'bootstrap.php');
        $this->assertEquals($expected, $result);

        $result = Plugin::path(ME_TOOLS, 'config' . DS . 'bootstrap.php', true);
        $this->assertEquals($expected, $result);

        //No existing file
        $result = Plugin::path(ME_TOOLS, 'config' . DS . 'no_existing.php', true);
        $this->assertFalse($result);

        $result = Plugin::path(ME_TOOLS, [
            'config' . DS . 'bootstrap.php',
            'config' . DS . 'no_existing.php',
        ]);
        $this->assertEquals([
            ROOT . 'config' . DS . 'bootstrap.php',
            ROOT . 'config' . DS . 'no_existing.php',
        ], $result);

        //Only the first file exists
        $result = Plugin::path(ME_TOOLS, [
            'config' . DS . 'bootstrap.php',
            'config' . DS . 'no_existing.php',
        ], true);
        $this->assertEquals([ROOT . 'config' . DS . 'bootstrap.php'], $result);

        //No existing files
        $result = Plugin::path(ME_TOOLS, [
            'config' . DS . 'no_existing.php',
            'config' . DS . 'no_existing2.php',
        ], true);
        $this->assertEmpty($result);
    }
}
