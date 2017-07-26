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
namespace MeTools\Test\TestCase;

use MeTools\TestSuite\TestCase;
use MeTools\Utility\Youtube;

/**
 * YoutubeTest class
 */
class YoutubeTest extends TestCase
{
    /**
     * Tests for `getId()` method
     * @test
     */
    public function testGetId()
    {
        $expected = 'bL_CJKq9rIw';

        $result = Youtube::getId('http://youtube.com/watch?v=bL_CJKq9rIw');
        $this->assertEquals($expected, $result);

        $result = Youtube::getId('http://www.youtube.com/watch?v=bL_CJKq9rIw');
        $this->assertEquals($expected, $result);

        $result = Youtube::getId('https://www.youtube.com/watch?v=bL_CJKq9rIw');
        $this->assertEquals($expected, $result);

        $result = Youtube::getId('http://youtu.be/bL_CJKq9rIw');
        $this->assertEquals($expected, $result);

        $result = Youtube::getId('https://youtu.be/bL_CJKq9rIw');
        $this->assertEquals($expected, $result);

        $result = Youtube::getId('http://youtu.be/bL_CJKq9rIw?t=5s');
        $this->assertEquals($expected, $result);

        $result = Youtube::getId('http://youtu.be/bL_CJKq9rIw?t=1m16s');
        $this->assertEquals($expected, $result);
    }

    /**
     * Tests for `getId()` method, with invalid url or parameters
     * @test
     */
    public function testGetIdInvalidUrlOrParameters()
    {
        $this->assertFalse(Youtube::getId('http://example.com'));
        $this->assertFalse(Youtube::getId('http://youtube.com'));
        $this->assertFalse(Youtube::getId('http://youtube.com?param=value'));
    }

    /**
     * Tests for `getPreview()` method
     * @test
     */
    public function testGetPreview()
    {
        $expected = 'http://img.youtube.com/vi/bL_CJKq9rIw/0.jpg';

        $result = Youtube::getPreview('bL_CJKq9rIw');
        $this->assertEquals($expected, $result);

        $result = Youtube::getPreview('https://www.youtube.com/watch?v=bL_CJKq9rIw');
        $this->assertEquals($expected, $result);

        $result = Youtube::getPreview('http://youtu.be/bL_CJKq9rIw');
        $this->assertEquals($expected, $result);
    }

    /**
     * Tests for `getUrl()` method
     * @test
     */
    public function testGetUrl()
    {
        $result = Youtube::getUrl('bL_CJKq9rIw');
        $expected = 'http://youtu.be/bL_CJKq9rIw';
        $this->assertEquals($expected, $result);
    }
}
