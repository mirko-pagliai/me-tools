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

/**
 * GlobalFunctionsTest class.
 *
 * It tests global functions declared in `config/global_functions.php`.
 */
class GlobalFunctionsTest extends TestCase
{
    /**
     * Test for `af()` global function, alias for `array_filter()`
     * @return void
     * @test
     */
    public function testAf()
    {
        $testArray = ['first', null, 'third', false, 'fifth'];
        $testCallback = function ($value) {
            return $value !== 'third';
        };
        
        //Removes empty values
        $result = af($testArray);
        $expected = array_filter($testArray);
        $this->assertEquals($expected, $result);
        
        //Removes empty values and re-order keys
        $result = array_values(af($testArray));
        $expected = array_values(array_filter($testArray));
        $this->assertEquals($expected, $result);
        
        //Using callback
        $result = af($testArray, $testCallback);
        $expected = array_filter($testArray, $testCallback);
        $this->assertEquals($expected, $result);
    }
    
    /**
     * Test for `am()` global function, alias for `array_merge()`
     * @return void
     * @test
     */
    public function testAm()
    {
        $firstTestArray = ['alfa', 'beta', 'gamma'];
        $secondTestArray = ['first', 'second', 'third'];
        $testString = 'this is a string';
        
        $result = am($firstTestArray, $secondTestArray);
        $expected = array_merge($firstTestArray, $secondTestArray);
        $this->assertEquals($expected, $result);
        
        $result = am($firstTestArray, $secondTestArray, $testString);
        $expected = array_merge($firstTestArray, $secondTestArray, (array)$testString);
        $this->assertEquals($expected, $result);
    }
    
    /**
     * Test for `firstKey()` global function
     * @return void
     * @test
     */
    public function testFirstKey()
    {
        $testArray = ['alfa' => 'first', 'beta' => 'second'];
        
        $result = firstKey($testArray);
        $expected = 'alfa';
        $this->assertEquals($expected, $result);
    }
    
    /**
     * Test for `firstValue()` global function
     * @return void
     * @test
     */
    public function testFirstValue()
    {
        $testArray = ['alfa' => 'first', 'beta' => 'second'];
        
        $result = firstValue($testArray);
        $expected = 'first';
        $this->assertEquals($expected, $result);
        
        $expected = array_values($testArray)[0];
        $this->assertEquals($expected, $result);
    }
    
    /**
     * Test for `isPositive()` global function
     * @return void
     * @test
     */
    public function testIsPositive()
    {
        $this->assertTrue(isPositive(1));
        $this->assertFalse(isPositive(0));
        $this->assertFalse(isPositive(-1));
        $this->assertFalse(isPositive(1.1));
    }
    
    /**
     * Test for `rtr()` global function
     * @return void
     * @test
     */
    public function testRtr()
    {
        $result = rtr(ROOT . DS . 'my' . DS . 'folder');
        $expected = 'my' . DS . 'folder';
        $this->assertEquals($expected, $result);
        
        $result = rtr('my' . DS . 'folder');
        $expected = 'my' . DS . 'folder';
        
        $this->assertEquals($expected, $result);
        $result = rtr(DS . 'my' . DS . 'folder');
        $expected = DS . 'my' . DS . 'folder';
        $this->assertEquals($expected, $result);
    }
}
