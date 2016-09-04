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
namespace MeTools\Test\TestCase\View\Helper;

use Cake\TestSuite\TestCase;
use Cake\View\View;
use MeTools\View\Helper\BBCodeHelper;
use MeTools\View\Helper\HtmlHelper;

/**
 * BBCodeHelperTest class
 */
class BBCodeHelperTest extends TestCase
{
    /**
     * Setup the test case, backup the static object values so they can be
     * restored. Specifically backs up the contents of Configure and paths in
     *  App if they have not already been backed up
     * @return void
     */
    public function setUp()
    {
        parent::setUp();

        $this->View = new View();
        $this->BBCode = new BBCodeHelper($this->View);
        $this->Html = new HtmlHelper($this->View);
    }

    /**
     * Teardown any static object changes and restore them
     * @return void
     */
    public function tearDown()
    {
        parent::tearDown();
        unset($this->BBCode, $this->Html, $this->View);
    }

    /**
     * Tests for `parser()` method
     * @return void
     * @test
     */
    public function testParser()
    {
        ob_start();
        echo '<p>Some text</p>' . PHP_EOL;
        echo '[readmore /]' . PHP_EOL;
        echo '<span>Some text</span>' . PHP_EOL;
        echo '[youtube]bL_CJKq9rIw[/youtube]' . PHP_EOL;
        echo '<div>Some text</div>' . PHP_EOL;
        $buffer = ob_get_clean();

        $result = $this->BBCode->parser($buffer);
        $expected = [
            'p' => true,
            'Some text',
            '/p',
            '<!-- read-more --',
            'span' => true,
            'Some text',
            '/span',
            ['div' => ['class' => 'embed-responsive embed-responsive-16by9']],
            'iframe' => [
                'allowfullscreen' => 'allowfullscreen',
                'height' => '480',
                'width' => '640',
                'class' => 'embed-responsive-item',
                'src' => 'https://www.youtube.com/embed/bL_CJKq9rIw',
            ],
            '/iframe',
            '/div',
            ['div' => true],
            'Some text',
            '/div',
        ];
        $this->assertHtml($expected, $result);
    }

    /**
     * Tests for `readMore()` method
     * @return void
     * @test
     */
    public function testReadMore()
    {
        $expected = '<!-- read-more -->';

        $result = $this->BBCode->readmore('[readmore]');
        $this->assertEquals($expected, $result);

        $result = $this->BBCode->readmore('[readmore/]');
        $this->assertEquals($expected, $result);

        $result = $this->BBCode->readmore('[readmore /]');
        $this->assertEquals($expected, $result);

        $result = $this->BBCode->readmore('[read-more /]');
        $this->assertEquals($expected, $result);

        $result = $this->BBCode->readmore('[readmore    /]');
        $this->assertEquals($expected, $result);

        $result = $this->BBCode->readmore('[readmore / ]');
        $this->assertEquals($expected, $result);

        $result = $this->BBCode->readmore('<p>[readmore /]</p>');
        $this->assertEquals($expected, $result);

        $result = $this->BBCode->readmore('<p class="my-class">[readmore /]</p>');
        $this->assertEquals($expected, $result);
    }

    /**
     * Tests for `youtube()` method
     * @return void
     * @test
     */
    public function testYoutube()
    {
        $expected = $this->Html->youtube('bL_CJKq9rIw');

        $result = $this->BBCode->youtube('[youtube]bL_CJKq9rIw[/youtube]');
        $this->assertEquals($expected, $result);

        $result = $this->BBCode->youtube('[youtube]http://youtube.com/watch?v=bL_CJKq9rIw[/youtube]');
        $this->assertEquals($expected, $result);

        $result = $this->BBCode->youtube('[youtube]https://www.youtube.com/watch?v=bL_CJKq9rIw[/youtube]');
        $this->assertEquals($expected, $result);

        $result = $this->BBCode->youtube('[youtube]https://youtu.be/bL_CJKq9rIw[/youtube]');
        $this->assertEquals($expected, $result);
    }
}
