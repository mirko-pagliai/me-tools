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

use Cake\TestSuite\TestCase;

/**
 * GlobalFunctionsTest class
 */
class GlobalFunctionsTest extends TestCase
{
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
     * Tests for `clearDir()` and `folderIsWriteable()`  global functions.
     *
     * It creates some directories, so it tests if they are writeable.
     * Then creates some files, cleans directories, so it tests if all files
     *  have been deleted.
     * @return void
     * @test
     */
    public function testClearDirAndFolderIsWriteabled()
    {
        $path = TMP . 'tests';

        //Creates some folder
        //@codingStandardsIgnoreLine
        @mkdir($path . DS . 'folder' . DS . 'subfolder', 0777, true);

        //Test for `folderIsWriteable()`
        $this->assertTrue(folderIsWriteable($path));

        $files = [
            'first',
            'folder' . DS . 'second',
            'folder' . DS . 'subfolder' . DS . 'third',
        ];

        //Creates some files
        foreach ($files as $file) {
            file_put_contents($path . DS . $file, null);
        }

        //Test for `clearDir()`
        $this->assertTrue(clearDir($path));

        //Now checks that the files no longer exist
        foreach ($files as $file) {
            $this->assertTrue(!file_exists($file));
        }

        //Delete folders
        //@codingStandardsIgnoreStart
        @rmdir($path . DS . 'folder' . DS . 'subfolder');
        @rmdir($path . DS . 'folder');
        //@codingStandardsIgnoreEnd

        //No existing folder
        $this->assertFalse(folderIsWriteable('/no/Existing'));
    }

    /**
     * Test for `getChildMethods()` global function
     * @return void
     * @test
     */
    public function testGetChildMethods()
    {
        $result = getChildMethods('\TestPlugin\Utility\ParentTestClass');
        $expected = ['firstParentTestMethod', 'secondParentTestMethod'];
        $this->assertEquals($expected, $result);

        $result = getChildMethods('\TestPlugin\Utility\ChildTestClass');
        $expected = ['firstChildTestMethod', 'secondChildTestMethod'];
        $this->assertEquals($expected, $result);

        $result = getChildMethods('\TestPlugin\Utility\ChildTestClass', 'firstChildTestMethod');
        $expected = ['secondChildTestMethod'];
        $this->assertEquals($expected, $result);

        //With no existing class
        $result = getChildMethods('\MeCms\Utility\NoExistingClass');
        $this->assertNull($result);
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
        //Http(s)
        $this->assertTrue(isUrl('https://www.example.com'));
        $this->assertTrue(isUrl('http://www.example.com'));
        $this->assertTrue(isUrl('www.example.com'));
        $this->assertTrue(isUrl('http://example.com'));
        $this->assertTrue(isUrl('http://example.com/file'));
        $this->assertTrue(isUrl('http://example.com/file.html'));
        $this->assertTrue(isUrl('www.example.com/file.html'));
        $this->assertTrue(isUrl('http://example.com/subdir/file'));

        //Ftp
        $this->assertTrue(isUrl('ftp://www.example.com'));
        $this->assertTrue(isUrl('ftp://example.com'));
        $this->assertTrue(isUrl('ftp://example.com/file.html'));

        //Missing "http" and/or "www"
        $this->assertFalse(isUrl('example.com'));

        //Files and dirs
        $this->assertFalse(isUrl('folder'));
        $this->assertFalse(isUrl(DS . 'folder'));
        $this->assertFalse(isUrl(DS . 'folder' . DS));
        $this->assertFalse(isUrl(DS . 'folder' . DS . 'file.txt'));
    }

    /**
     * Test for `rtr()` global function
     * @return void
     * @test
     */
    public function testRtr()
    {
        $result = rtr(ROOT . 'my' . DS . 'folder');
        $expected = 'my' . DS . 'folder';
        $this->assertEquals($expected, $result);

        $result = rtr('my' . DS . 'folder');
        $expected = 'my' . DS . 'folder';

        $this->assertEquals($expected, $result);
        $result = rtr(DS . 'my' . DS . 'folder');
        $expected = DS . 'my' . DS . 'folder';
        $this->assertEquals($expected, $result);
    }

    /**
     * Test for `which()` global function
     * @return void
     * @test
     */
    public function testWhich()
    {
        $result = which('phpunit');
        $this->assertNotEmpty($result);
        $this->assertContains('phpunit', $result);
    }
}
