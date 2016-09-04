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

        $result = Youtube::getId('https://youtu.be/bL_CJKq9rIw');
        $this->assertEquals($expected, $result);

        $result = Youtube::getId('http://youtu.be/bL_CJKq9rIw');
        $this->assertEquals($expected, $result);
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
