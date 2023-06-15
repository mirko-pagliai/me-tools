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

use Cake\View\View;
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
    public BBCode $BBCode;

    /**
     * @var \MeTools\View\Helper\HtmlHelper
     */
    protected HtmlHelper $Html;

    /**
     * Called before every test method
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->Html ??= new HtmlHelper(new View());
        $this->BBCode ??= new BBCode($this->Html);
    }

    /**
     * @test
     * @uses \MeTools\Utility\BBCode::parser()
     */
    public function testParser(): void
    {
        $expected = '<p>Some para text</p>' . PHP_EOL .
            '<!-- read-more -->' . PHP_EOL .
            '<hr />' . PHP_EOL .
            '<span>Some span text</span>' . PHP_EOL .
            '<div class="ratio ratio-16x9"><iframe allowfullscreen="allowfullscreen" height="480" width="640" src="https://www.youtube.com/embed/bL_CJKq9rIw"></iframe></div>' . PHP_EOL .
            '<div>Some div text</div>' . PHP_EOL;

        ob_start();
        echo '<p>Some para text</p>' . PHP_EOL;
        echo '[readmore /]' . PHP_EOL;
        echo '[hr /]' . PHP_EOL;
        echo '<span>Some span text</span>' . PHP_EOL;
        echo '[youtube]bL_CJKq9rIw[/youtube]' . PHP_EOL;
        echo '<div>Some div text</div>' . PHP_EOL;
        $result = $this->BBCode->parser(ob_get_clean() ?: '');
        $this->assertSame($expected, $result);
    }

    /**
     * @test
     * @uses \MeTools\Utility\BBCode::remove()
     */
    public function testRemove(): void
    {
        $expected = '<p>Some para text</p>' . PHP_EOL .
            PHP_EOL .
            '<span>Some span text</span>' . PHP_EOL .
            PHP_EOL .
            '<div>Some div text</div>';
        ob_start();
        echo '<p>Some para text</p>' . PHP_EOL;
        echo '[readmore /]' . PHP_EOL;
        echo '<span>Some span text</span>' . PHP_EOL;
        echo '[youtube]bL_CJKq9rIw[/youtube]' . PHP_EOL;
        echo '<div>Some div text</div>' . PHP_EOL;
        $result = $this->BBCode->remove(ob_get_clean() ?: '');
        $this->assertSame($expected, $result);
    }

    /**
     * @test
     * @uses \MeTools\Utility\BBCode::hr()
     */
    public function testHr(): void
    {
        $this->assertSame('<hr />', $this->BBCode->hr('[hr]'));
        $this->assertSame('<hr />', $this->BBCode->hr('[hr/]'));
        $this->assertSame('<hr />', $this->BBCode->hr('[hr /]'));
    }

    /**
     * @test
     * @uses \MeTools\Utility\BBCode::image()
     */
    public function testImage(): void
    {
        $this->assertSame($this->Html->image('my_pic.gif'), $this->BBCode->image('[img]my_pic.gif[/img]'));
    }

    /**
     * @test
     * @uses \MeTools\Utility\BBCode::readMore()
     */
    public function testReadMore(): void
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
            $this->assertSame('<!-- read-more -->', $this->BBCode->readMore($text));
        }
    }

    /**
     * @test
     * @uses \MeTools\Utility\BBCode::url()
     */
    public function testUrl(): void
    {
        $expected = $this->Html->link('my link', 'http://example');
        $this->assertSame($expected, $this->BBCode->url('[url="http://example"]my link[/url]'));
    }

    /**
     * @test
     * @uses \MeTools\Utility\BBCode::youtube()
     */
    public function testYoutube(): void
    {
        $expected = $this->Html->youtube('bL_CJKq9rIw');
        foreach ([
            '[youtube]bL_CJKq9rIw[/youtube]',
            '[youtube]https://youtube.com/watch?v=bL_CJKq9rIw[/youtube]',
            '[youtube]https://www.youtube.com/watch?v=bL_CJKq9rIw[/youtube]',
            '[youtube]https://youtu.be/bL_CJKq9rIw[/youtube]',
        ] as $text) {
            $this->assertSame($expected, $this->BBCode->youtube($text));
        }

        $expected = $this->Html->youtube('-YcwR89cfao?t=62');
        foreach ([
            '[youtube]https://youtu.be/-YcwR89cfao?t=62[/youtube]',
            '[youtube]-YcwR89cfao?t=62[/youtube]',
        ] as $text) {
            $this->assertSame($expected, $this->BBCode->youtube($text));
        }

        $expected = $this->Html->youtube('bL_CJKq9rIw');
        $this->assertSame($expected, $this->BBCode->youtube('<p>[youtube]bL_CJKq9rIw[/youtube]</p>'));
    }
}
