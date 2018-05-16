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
     * Tests for `clearDir()` global function
     * @test
     */
    public function testClearDir()
    {
        $path = TMP . 'tests';

        //Creates some folder
        safe_mkdir($path . DS . 'folder' . DS . 'subfolder', 0777, true);

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
        safe_rmdir_recursive($path . DS . 'folder');
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
}
