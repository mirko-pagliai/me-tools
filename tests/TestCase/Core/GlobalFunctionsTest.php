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

use MeTools\TestSuite\TestCase;
use MeTools\View\OptionsParser;

/**
 * GlobalFunctionsTest class
 */
class GlobalFunctionsTest extends TestCase
{
    /**
     * Tests for `clearDir()` and `folderIsWriteable()`  global functions.
     *
     * It creates some directories, so it tests if they are writeable.
     * Then creates some files, cleans directories, so it tests if all files
     *  have been deleted.
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
     * @test
     */
    public function testIsUrl()
    {
        foreach ([
            'https://www.example.com',
            'http://www.example.com',
            'www.example.com',
            'http://example.com',
            'http://example.com/file',
            'http://example.com/file.html',
            'http://example.com/subdir/file',
            'ftp://www.example.com',
            'ftp://example.com',
            'ftp://example.com/file.html',
        ] as $url) {
            $this->assertTrue(isUrl($url));
        }

        foreach ([
            'example.com',
            'folder',
            DS . 'folder',
            DS . 'folder' . DS,
            DS . 'folder' . DS . 'file.txt',
        ] as $badUrl) {
            $this->assertFalse(isUrl($badUrl));
        }
    }

    /**
     * Test for `optionsParser()` global function
     * @test
     */
    public function testOptionsParser()
    {
        $result = optionsParser();
        $expected = new OptionsParser;
        $this->assertInstanceOf('MeTools\View\OptionsParser', $result);
        $this->assertEquals($expected, $result);
        $this->assertEquals(get_object_vars($expected), get_object_vars($result));

        $a = ['f' => 'first', 's' => 'second'];
        $b = ['t' => 'third'];

        $result = optionsParser($a, $b);
        $expected = new OptionsParser($a, $b);
        $this->assertInstanceOf('MeTools\View\OptionsParser', $result);
        $this->assertEquals($expected, $result);
        $this->assertEquals(get_object_vars($expected), get_object_vars($result));
    }

    /**
     * Test for `rtr()` global function
     * @test
     */
    public function testRtr()
    {
        $result = rtr(ROOT . 'my' . DS . 'folder');
        $this->assertEquals('my' . DS . 'folder', $result);

        $result = rtr('my' . DS . 'folder');
        $this->assertEquals('my' . DS . 'folder', $result);

        $result = rtr(DS . 'my' . DS . 'folder');
        $this->assertEquals(DS . 'my' . DS . 'folder', $result);
    }

    /**
     * Test for `which()` global function
     * @test
     */
    public function testWhich()
    {
        $result = which('phpunit');
        $this->assertNotEmpty($result);
        $this->assertContains('phpunit', $result);
    }
}
