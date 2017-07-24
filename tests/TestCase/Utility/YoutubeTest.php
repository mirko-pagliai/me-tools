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

use Cake\TestSuite\TestCase;
use MeTools\Utility\Youtube;

/**
 * YoutubeTest class.
 */
class YoutubeTest extends TestCase
{
    /**
     * Tests for `getId()` method
     * @return void
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
     * Tests for `getId()` method, with invalid parameters
     * @return void
     * @test
     */
    public function testGetIdInvalidParameters()
    {
        $result = Youtube::getId('http://youtube.com');
        $this->assertFalse($result);

        $result = Youtube::getId('http://youtube.com?param=value');
        $this->assertFalse($result);
    }

    /**
     * Tests for `getId()` method, with invalid url
     * @return void
     * @test
     */
    public function testGetIdInvalidUrl()
    {
        $result = Youtube::getId('http://example.com');
        $this->assertFalse($result);
    }

    /**
     * Tests for `getPreview()` method
     * @return void
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
     * @return void
     * @test
     */
    public function testGetUrl()
    {
        $result = Youtube::getUrl('bL_CJKq9rIw');
        $expected = 'http://youtu.be/bL_CJKq9rIw';
        $this->assertEquals($expected, $result);
    }
}
