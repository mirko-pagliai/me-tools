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
namespace MeTools\Test\TestCase;

use Cake\TestSuite\TestCase;
use MeTools\Core\Plugin;

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
        Plugin::unload();
    }
    
    /**
     * Tests for `all()` method
     * @return void
     * @test
     */
    public function testAll()
    {
        $result = Plugin::all();
        $this->assertEmpty($result);
        
        $result = Plugin::load(
            'TestPlugin',
            ['path' => 'tests/test_app/Plugin/TestPlugin/src']
        );
        $this->assertNull($result);
        
        $result = Plugin::all();
        $expected = ['TestPlugin'];
        $this->assertEquals($expected, $result);
        
        $result = Plugin::all(['exclude' => 'TestPlugin']);
        $this->assertEmpty($result);
        
        $result = Plugin::load('MeTools', ['path' => ROOT]);
        $this->assertNull($result);
        
        $result = Plugin::load(
            'AnotherTestPlugin',
            ['path' => 'tests/test_app/Plugin/AnotherTestPlugin/src']
        );
        $this->assertNull($result);
        
        $result = Plugin::all();
        $expected = ['MeTools', 'AnotherTestPlugin', 'TestPlugin'];
        $this->assertEquals($expected, $result);
        
        $result = Plugin::all(['order' => false]);
        $expected = ['AnotherTestPlugin', 'MeTools', 'TestPlugin'];
        $this->assertEquals($expected, $result);
    }
    
    /**
     * Tests for `path()` method
     * @return void
     * @test
     */
    public function testPath()
    {
        $result = Plugin::load('MeTools', ['path' => ROOT]);
        $this->assertNull($result);
        
        $result = Plugin::path('MeTools');
        $this->assertEquals(ROOT, $result);
        
        $expected = ROOT . 'config' . DS . 'bootstrap.php';
        
        $result = Plugin::path('MeTools', 'config' . DS . 'bootstrap.php');
        $this->assertEquals($expected, $result);
        
        $result = Plugin::path('MeTools', 'config' . DS . 'bootstrap.php', true);
        $this->assertEquals($expected, $result);
        
        //No existing file
        $result = Plugin::path('MeTools', 'config' . DS . 'no_existing.php', true);
        $this->assertFalse($result);
        
        $result = Plugin::path('MeTools', [
            'config' . DS . 'bootstrap.php',
            'config' . DS . 'no_existing.php',
        ]);
        $expected = [
            ROOT . 'config' . DS . 'bootstrap.php',
            ROOT . 'config' . DS . 'no_existing.php',
        ];
        $this->assertEquals($expected, $result);
        
        //Only the first file exists
        $result = Plugin::path('MeTools', [
            'config' . DS . 'bootstrap.php',
            'config' . DS . 'no_existing.php',
        ], true);
        $expected = [ROOT . 'config' . DS . 'bootstrap.php'];
        $this->assertEquals($expected, $result);
        
        //No one exists
        $result = Plugin::path('MeTools', [
            'config' . DS . 'no_existing.php',
            'config' . DS . 'no_existing2.php',
        ], true);
        $this->assertEmpty($result);
    }
}
