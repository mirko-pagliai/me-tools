<?php
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
use MeTools\Utility\BBCode;
use MeTools\View\Helper\HtmlHelper;

/**
 * BBCodeTest class
 */
class BBCodeTest extends TestCase
{
    /**
     * @var \MeTools\Utility\BBCode
     */
    public $BBCode;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject
     */
    protected $Html;

    /**
     * Called before every test method
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->Html = $this->getMockForHelper(HtmlHelper::class, null);
        $this->BBCode = new BBCode($this->Html);
    }

    /**
     * Tests for `parser()` method
     * @test
     */
    public function testParser()
    {
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
        ob_start();
        echo '<p>Some para text</p>' . PHP_EOL;
        echo '[readmore /]' . PHP_EOL;
        echo '<span>Some span text</span>' . PHP_EOL;
        echo '[youtube]bL_CJKq9rIw[/youtube]' . PHP_EOL;
        echo '<div>Some div text</div>' . PHP_EOL;
        $this->assertHtml($expected, $this->BBCode->parser(ob_get_clean()));
    }

    /**
     * Tests for `remove()` method
     * @test
     */
    public function testRemove()
    {
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
        ob_start();
        echo '<p>Some para text</p>' . PHP_EOL;
        echo '[readmore /]' . PHP_EOL;
        echo '<span>Some span text</span>' . PHP_EOL;
        echo '[youtube]bL_CJKq9rIw[/youtube]' . PHP_EOL;
        echo '<div>Some div text</div>' . PHP_EOL;
        $this->assertHtml($expected, $this->BBCode->remove(ob_get_clean()));
    }

    /**
     * Tests for `image()` method
     * @test
     */
    public function testImage()
    {
        $this->assertEquals($this->Html->image('mypic.gif'), $this->BBCode->image('[img]mypic.gif[/img]'));
    }

    /**
     * Tests for `readMore()` method
     * @test
     */
    public function testReadMore()
    {
        foreach ([
            '[readmore]',
            '[readmore/]',
            '[readmore /]',
            '[read-more /]',
            '[readmore    /]',
            '[readmore / ]',
            '<p>[readmore /]</p>',
            '<p class="my-class">[readmore /]</p>',
        ] as $text) {
            $this->assertEquals('<!-- read-more -->', $this->BBCode->readmore($text));
        }
    }

    /**
     * Tests for `url()` method
     * @test
     */
    public function testUrl()
    {
        $expected = $this->Html->link('my link', 'http://example');
        $this->assertEquals($expected, $this->BBCode->url('[url="http://example"]my link[/url]'));
    }

    /**
     * Tests for `youtube()` method
     * @test
     */
    public function testYoutube()
    {
        $expected = $this->Html->youtube('bL_CJKq9rIw');
        foreach ([
            '[youtube]bL_CJKq9rIw[/youtube]',
            '[youtube]http://youtube.com/watch?v=bL_CJKq9rIw[/youtube]',
            '[youtube]https://www.youtube.com/watch?v=bL_CJKq9rIw[/youtube]',
            '[youtube]https://youtu.be/bL_CJKq9rIw[/youtube]',
        ] as $text) {
            $this->assertEquals($expected, $this->BBCode->youtube($text));
        }
    }
}
