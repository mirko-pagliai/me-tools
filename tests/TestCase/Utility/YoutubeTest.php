<?php
/** @noinspection HttpUrlsUsage */
declare(strict_types=1);

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
namespace MeTools\Test\TestCase\Utility;

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
    public function testGetId(): void
    {
        foreach ([
            'http://youtube.com/watch?v=bL_CJKq9rIw',
            'http://www.youtube.com/watch?v=bL_CJKq9rIw',
            'https://www.youtube.com/watch?v=bL_CJKq9rIw',
            'http://youtu.be/bL_CJKq9rIw',
            'https://youtu.be/bL_CJKq9rIw',
            'http://youtu.be/bL_CJKq9rIw?t=5s',
            'http://youtu.be/bL_CJKq9rIw?t=1m16s',
        ] as $url) {
            $this->assertEquals('bL_CJKq9rIw', Youtube::getId($url));
        }

        //With invalid url
        $this->assertEmpty(Youtube::getId('http://example.com'));
        $this->assertEmpty(Youtube::getId('http://youtube.com'));
        $this->assertEmpty(Youtube::getId('http://youtube.com?param=value'));
    }

    /**
     * Tests for `getPreview()` method
     * @test
     */
    public function testGetPreview(): void
    {
        foreach ([
            'bL_CJKq9rIw',
            'https://www.youtube.com/watch?v=bL_CJKq9rIw',
            'http://youtu.be/bL_CJKq9rIw',
        ] as $value) {
            $this->assertEquals('https://img.youtube.com/vi/bL_CJKq9rIw/0.jpg', Youtube::getPreview($value));
        }
    }

    /**
     * Tests for `getUrl()` method
     * @test
     */
    public function testGetUrl(): void
    {
        $this->assertEquals('https://youtu.be/bL_CJKq9rIw', Youtube::getUrl('bL_CJKq9rIw'));
    }
}
