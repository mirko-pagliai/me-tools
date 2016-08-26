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

use Cake\Filesystem\File;
use Cake\Filesystem\Folder;
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
     * Test for `buttonClass()` global function
     * @return void
     * @test
     */
    public function testButtonClass()
    {
        $result = buttonClass();
        $expected = ['class' => 'btn btn-default'];
        $this->assertEquals($expected, $result);
        
        $result = buttonClass(['class' => 'my-class']);
        $expected = ['class' => 'my-class btn btn-default'];
        $this->assertEquals($expected, $result);
        
        $result = buttonClass(['class' => 'btn']);
        $expected = ['class' => 'btn btn-default'];
        $this->assertEquals($expected, $result);
        
        $result = buttonClass(['class' => 'btn-primary']);
        $expected = ['class' => 'btn-primary btn'];
        $this->assertEquals($expected, $result);
        
        $result = buttonClass(['class' => 'btn btn-primary']);
        $expected = ['class' => 'btn btn-primary'];
        $this->assertEquals($expected, $result);
        
        $result = buttonClass(['class' => 'my-class btn']);
        $expected = ['class' => 'my-class btn btn-default'];
        $this->assertEquals($expected, $result);
        
        $result = buttonClass(['class' => 'my-class btn'], 'primary');
        $expected = ['class' => 'my-class btn btn-primary'];
        $this->assertEquals($expected, $result);
        
        $result = buttonClass(['class' => 'my-class btn-primary']);
        $expected = ['class' => 'my-class btn-primary btn'];
        $this->assertEquals($expected, $result);
        
        $result = buttonClass(['class' => 'my-class btn btn-primary']);
        $expected = ['class' => 'my-class btn btn-primary'];
        $this->assertEquals($expected, $result);
        
        $result = buttonClass([], 'primary');
        $expected = ['class' => 'btn btn-primary'];
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
     * Tests for `folderIsWriteable()` and `clearDir()` global functions.
     *
     * It creates some directories, so it tests if they are writeable.
     * Then creates some files, cleans directories, so it tests if all files
     *  have been deleted.
     * @return void
     * @test
     */
    public function testFolders()
    {
        $path = TMP . 'tests';
        
        //Creates some folder
        $folder = new Folder($path);
        $result = $folder->create($path . DS . 'folder' . DS . 'subfolder');
        $this->assertTrue($result);
        
        //Test for `folderIsWriteable()`
        $this->assertTrue(folderIsWriteable($path));
        
        
        $files = [
            $path . DS . 'first.tmp',
            $path . DS . 'folder' . DS . 'second.tmp',
            $path . DS . 'folder' . DS . 'subfolder' . DS . 'third.tmp',
        ];
        
        //Creates some files
        foreach ($files as $file) {
            new File($file, true, 0777);
            $this->assertTrue(is_readable($file) && is_writable($file));
        }
        
        //Test for `clearDir()`
        $this->assertTrue(clearDir($path));
        
        //Now checks that the files no longer exist
        foreach ($files as $file) {
            $this->assertTrue(!file_exists($file));
        }
        
        //Delete folders
        rmdir($path . DS . 'folder' . DS . 'subfolder');
        rmdir($path . DS . 'folder');
    }
    
    /**
     * Test for `implodeRecursive()` global function
     * @return void
     * @test
     */
    public function testImplodeRecursive()
    {
        $result = implodeRecursive(' ', ['value']);
        $expected = 'value';
        $this->assertEquals($expected, $result);
        
        $result = implodeRecursive(' ', ['value1', 'value2']);
        $expected = 'value1 value2';
        $this->assertEquals($expected, $result);
        
        $result = implodeRecursive(' ', ['value1', 'value2', ['value3']]);
        $expected = 'value1 value2 value3';
        $this->assertEquals($expected, $result);
        
        $result = implodeRecursive(' ', ['value1', 'value2', ['value3', ['value4']]]);
        $expected = 'value1 value2 value3 value4';
        $this->assertEquals($expected, $result);
        
        $result = implodeRecursive(' ', am(['value1', 'value2'], ['value3', ['value4']], ['value5']));
        $expected = 'value1 value2 value3 value4 value5';
        $this->assertEquals($expected, $result);
    }
    
    /**
     * Test for `isJson()` global function
     * @return void
     * @test
     */
    public function testIsJson()
    {
        $testArray = ['alfa' => 'first', 'beta' => 'second'];
        $testJson = '{"a":1,"b":2,"c":3,"d":4,"e":5}';
        $testObject = (object)$testArray;
        $testString = 'this is a string';
        
        $this->assertFalse(isJson($testArray));
        $this->assertTrue(isJson($testJson));
        $this->assertFalse(isJson($testObject));
        $this->assertFalse(isJson($testString));
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
     * Test for `isUrl()` global function
     * @return void
     * @test
     */
    public function testIsUrl()
    {
        $this->assertTrue(isUrl('https://www.example.com'));
        $this->assertTrue(isUrl('http://www.example.com'));
        $this->assertTrue(isUrl('http://example.com'));
        $this->assertTrue(isUrl('http://example.com/noexistingfile'));
        $this->assertTrue(isUrl('http://example.com/noexistingfile.html'));
        $this->assertTrue(isUrl('http://example.com/subdir/noexistingfile'));
        
        //Files and dirs
        $this->assertFalse(isUrl('folder'));
        $this->assertFalse(isUrl(DS . 'folder'));
        $this->assertFalse(isUrl(DS . 'folder' . DS));
        $this->assertFalse(isUrl(DS . 'folder' . DS . 'file.txt'));
    }
    
    /**
     * Test for `optionDefaults()` global function
     * @return void
     * @test
     */
    public function testOptionDefault()
    {
        $options = ['value1' => 'val-1'];
        
        $result = optionDefaults(['class' => 'my-class'], $options);
        $expected = [
            'value1' => 'val-1',
            'class' => 'my-class',
        ];
        $this->assertEquals($expected, $result);
        
        $result = optionDefaults(['class' => 'first-class second-class'], $options);
        $expected = [
            'value1' => 'val-1',
            'class' => 'first-class second-class',
        ];
        $this->assertEquals($expected, $result);
        
        $result = optionDefaults(['class' => ['first-class', 'second-class']], $options);
        $expected = [
            'value1' => 'val-1',
            'class' => 'first-class second-class',
        ];
        $this->assertEquals($expected, $result);
        
        $result = optionDefaults(['class' => ['first-class', 'second-class', 'first-class']], $options);
        $expected = [
            'value1' => 'val-1',
            'class' => 'first-class second-class',
        ];
        $this->assertEquals($expected, $result);
        
        $result = optionDefaults(['class' => ['first-class', ['second-class']]], $options);
        $expected = [
            'value1' => 'val-1',
            'class' => 'first-class second-class',
        ];
        $this->assertEquals($expected, $result);
        
        $result = optionDefaults(['class' => ['first-class', ['second-class', ['third-class']]]], $options);
        $expected = [
            'value1' => 'val-1',
            'class' => 'first-class second-class third-class',
        ];
        $this->assertEquals($expected, $result);
        
        //This doesn't change the value
        $result = optionDefaults(['value1' => 'new-val-1'], $options);
        $expected = ['value1' => 'val-1'];
        $this->assertEquals($expected, $result);
        
        //Backward compatibility with three arguments
        $result = optionDefaults('class', 'my-class', $options);
        $expected = [
            'value1' => 'val-1',
            'class' => 'my-class',
        ];
        $this->assertEquals($expected, $result);
        
        //Backward compatibility with three arguments
        $result = optionDefaults('class', 'first-class second-class', $options);
        $expected = [
            'value1' => 'val-1',
            'class' => 'first-class second-class',
        ];
        $this->assertEquals($expected, $result);
    }
    
    /**
     * Test for `optionValues()` global function
     * @return void
     * @test
     */
    public function testOptionValue()
    {
        $options = ['value1' => 'alfa beta'];
        
        $result = optionValues(['class' => 'my-class'], $options);
        $expected = ['value1' => 'alfa beta', 'class' => 'my-class'];
        $this->assertEquals($expected, $result);
        
        $result = optionValues(['class' => ['my-class']], $options);
        $expected = ['value1' => 'alfa beta', 'class' => 'my-class'];
        $this->assertEquals($expected, $result);
        
        $result = optionValues(['value1' => 'beta'], $options);
        $expected = ['value1' => 'alfa beta'];
        $this->assertEquals($expected, $result);
        
        $result = optionValues(['value1' => 'gamma'], $options);
        $expected = ['value1' => 'alfa beta gamma'];
        $this->assertEquals($expected, $result);
        
        $result = optionValues(['value1' => 'delta gamma'], $options);
        $expected = ['value1' => 'alfa beta delta gamma'];
        $this->assertEquals($expected, $result);
        
        $result = optionValues(['value1' => 'delta gamma delta'], $options);
        $expected = ['value1' => 'alfa beta delta gamma'];
        $this->assertEquals($expected, $result);
        
        $result = optionValues(['value1' => ['delta', 'gamma']], $options);
        $expected = ['value1' => 'alfa beta delta gamma'];
        $this->assertEquals($expected, $result);
        
        $result = optionValues(['value1' => ['delta', ['gamma']]], $options);
        $expected = ['value1' => 'alfa beta delta gamma'];
        $this->assertEquals($expected, $result);
        
        $result = optionValues(['value1' => ['delta', ['gamma', ['ypsilon']]]], $options);
        $expected = ['value1' => 'alfa beta delta gamma ypsilon'];
        $this->assertEquals($expected, $result);
        
        $result = optionValues([
            'class' => 'my-class',
            'value1' => 'gamma'
        ], $options);
        $expected = [
            'class' => 'my-class',
            'value1' => 'alfa beta gamma',
        ];
        $this->assertEquals($expected, $result);
        
        $result = optionValues([
            'class' => 'my-class',
            'value1' => ['delta', 'gamma']
        ], $options);
        $expected = ['class' => 'my-class', 'value1' => 'alfa beta delta gamma'];
        $this->assertEquals($expected, $result);

        //Backward compatibility with three arguments
        $result = optionValues('value1', 'gamma', $options);
        $expected = ['value1' => 'alfa beta gamma'];
        $this->assertEquals($expected, $result);
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
