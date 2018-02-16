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
namespace MeTools\Test\TestCase\View\Helper;

use Cake\View\View;
use MeTools\TestSuite\TestCase;
use MeTools\View\Helper\BBCodeHelper;
use MeTools\View\Helper\HtmlHelper;

/**
 * BBCodeHelperTest class
 */
class BBCodeHelperTest extends TestCase
{
    /**
     * @var \MeTools\View\Helper\BBCodeHelper
     */
    protected $BBCode;

    /**
     * @var \MeTools\View\Helper\HtmlHelper
     */
    protected $Html;

    /**
     * Setup the test case, backup the static object values so they can be
     * restored. Specifically backs up the contents of Configure and paths in
     *  App if they have not already been backed up
     * @return void
     */
    public function setUp()
    {
        parent::setUp();

        $this->BBCode = new BBCodeHelper(new View);
        $this->Html = new HtmlHelper(new View);
    }

    /**
     * Tests for `parser()` method
     * @test
     */
    public function testParser()
    {
        ob_start();
        echo '<p>Some para text</p>' . PHP_EOL;
        echo '[readmore /]' . PHP_EOL;
        echo '<span>Some span text</span>' . PHP_EOL;
        echo '[youtube]bL_CJKq9rIw[/youtube]' . PHP_EOL;
        echo '<div>Some div text</div>' . PHP_EOL;
        $buffer = ob_get_clean();

        $result = $this->BBCode->parser($buffer);
        $expected = [
            'p' => true,
            'Some para text',
            '/p',
            '<!-- read-more --',
            'span' => true,
            'Some span text',
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
            'Some div text',
            '/div',
        ];
        $this->assertHtml($expected, $result);
    }

    /**
     * Tests for `remove()` method
     * @test
     */
    public function testRemove()
    {
        ob_start();
        echo '<p>Some para text</p>' . PHP_EOL;
        echo '[readmore /]' . PHP_EOL;
        echo '<span>Some span text</span>' . PHP_EOL;
        echo '[youtube]bL_CJKq9rIw[/youtube]' . PHP_EOL;
        echo '<div>Some div text</div>' . PHP_EOL;
        $buffer = ob_get_clean();

        $result = $this->BBCode->remove($buffer);
        $expected = [
            'p' => true,
            'Some para text',
            '/p',
            'span' => true,
            'Some span text',
            '/span',
            'div' => true,
            'Some div text',
            '/div',
        ];
        $this->assertHtml($expected, $result);
    }

    /**
     * Tests for `image()` method
     * @test
     */
    public function testImage()
    {
        $result = $this->BBCode->image('[img]mypic.gif[/img]');
        $expected = $this->Html->image('mypic.gif');
        $this->assertEquals($expected, $result);
    }

    /**
     * Tests for `readMore()` method
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
     * Tests for `url()` method
     * @test
     */
    public function testUrl()
    {
        $result = $this->BBCode->url('[url="http://example"]my link[/url]');
        $expected = $this->Html->link('my link', 'http://example');
        $this->assertEquals($expected, $result);
    }

    /**
     * Tests for `youtube()` method
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
