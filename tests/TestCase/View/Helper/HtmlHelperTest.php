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
use MeTools\View\Helper\HtmlHelper;

/**
 * HtmlHelperTest class
 */
class HtmlHelperTest extends TestCase
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
        $View = new View();
        $this->Html = new HtmlHelper($View);
    }
    
    /**
     * Tests for `badge()` method
     * @return void
     * @test
     */
    public function testBadge()
    {
        $result = $this->Html->badge('my text');
        $expected = [
            'span' => ['class' => 'badge'],
            'my text',
            '/span',
        ];
        $this->assertHtml($expected, $result);
        
        $result = $this->Html->badge('my text', ['class' => 'my-class']);
        $expected = [
            'span' => ['class' => 'my-class badge'],
            'my text',
            '/span',
        ];
        $this->assertHtml($expected, $result);
    }
    
    /**
     * Tests for `heading()` method
     * @return void
     * @test
     */
    public function testHeading()
    {
        $text = 'my header';
        
        $expected = ['h2' => true, $text, '/h2'];
        
        $result = $this->Html->heading($text);
        $this->assertHtml($expected, $result);
        
        //It still creates a h2 tag
        $result = $this->Html->heading('my header', ['type' => 'strong']);
        $this->assertHtml($expected, $result);
        
        $result = $this->Html->heading('my header', ['type' => 'h4']);
        $expected = ['h4' => true, $text, '/h4'];
        $this->assertHtml($expected, $result);
        
        $result = $this->Html->heading('my header', [], 'my small text');
        $expected = [
            'h2' => true,
            $text,
            ' ',
            'small' => true,
            'my small text',
            '/small',
            '/h2',
        ];
        $this->assertHtml($expected, $result);
        
        $result = $this->Html->heading('my header', ['type' => 'h4'], 'my small text');
        $expected = [
            'h4' => true,
            $text,
            ' ',
            'small' => true,
            'my small text',
            '/small',
            '/h4',
        ];
        $this->assertHtml($expected, $result);
        
        $result = $this->Html->heading(
            'my header',
            ['class' => 'header-class'],
            'my small text',
            ['class' => 'small-class']
        );
        $expected = [
            'h2' => ['class' => 'header-class'],
            $text,
            ' ',
            'small' => ['class' => 'small-class'],
            'my small text',
            '/small',
            '/h2',
        ];
        $this->assertHtml($expected, $result);
    }
    
    
    /**
     * Tests for `iframe()` method
     * @return void
     * @test
     */
    public function testIframe()
    {
        $url = 'http://frame';
        
        $expected = ['iframe' => ['src' => $url], '/iframe'];
        
        //Simple iframe (no ratio)
        $result = $this->Html->iframe($url);
        $this->assertHtml($expected, $result);
        
        //No existing ratio
        $result = $this->Html->iframe($url, ['ratio' => 'noExisting']);
        $this->assertHtml($expected, $result);
        
        $result = $this->Html->iframe($url, ['ratio' => '16by9']);
        $expected = [
            'div' => ['class' => 'embed-responsive embed-responsive-16by9'],
            'iframe' => ['class' => 'embed-responsive-item', 'src' => $url],
            '/iframe',
            '/div',
        ];
        $this->assertHtml($expected, $result);
        
        $result = $this->Html->iframe($url, ['ratio' => '4by3']);
        $expected = [
            'div' => ['class' => 'embed-responsive embed-responsive-4by3'],
            'iframe' => ['class' => 'embed-responsive-item', 'src' => $url],
            '/iframe',
            '/div',
        ];
        $this->assertHtml($expected, $result);
        
        $result = $this->Html->iframe($url, ['class' => 'my-class', 'ratio' => '16by9']);
        $expected = [
            'div' => ['class' => 'embed-responsive embed-responsive-16by9'],
            'iframe' => ['class' => 'my-class embed-responsive-item', 'src' => $url],
            '/iframe',
            '/div',
        ];
        $this->assertHtml($expected, $result);
    }
    
    /**
     * Tests for `label()` method
     * @return void
     * @test
     */
    public function testLabel()
    {
        $result = $this->Html->label('my text');
        $expected = [
            'span' => ['class' => 'label label-default'],
            'my text',
            '/span',
        ];
        $this->assertHtml($expected, $result);
        
        $result = $this->Html->label('my text', ['class' => 'my-class']);
        $expected = [
            'span' => ['class' => 'my-class label label-default'],
            'my text',
            '/span',
        ];
        $this->assertHtml($expected, $result);
        
        $result = $this->Html->label('my text', ['type' => 'success']);
        $expected = [
            'span' => ['class' => 'label label-success'],
            'my text',
            '/span',
        ];
        $this->assertHtml($expected, $result);
        
        $result = $this->Html->label(
            'my text',
            ['class' => 'my-class', 'type' => 'success']
        );
        $expected = [
            'span' => ['class' => 'my-class label label-success'],
            'my text',
            '/span',
        ];
        $this->assertHtml($expected, $result);
    }
    
    /**
     * Tests for `shareaholic()` method
     * @return void
     * @test
     */
    public function testShareaholic()
    {
        $result = $this->Html->shareaholic('my-app-id');
        $expected = [
            'div' => [
                'data-app' => 'share_buttons',
                'data-app-id' => 'my-app-id',
                'class' => 'shareaholic-canvas',
            ],
        ];
        $this->assertHtml($expected, $result);
    }
    
    /**
     * Tests for `viewport()` method
     * @return void
     * @test
     */
    public function testViewport()
    {
        //By default, `block` is `true`
        $result = $this->Html->viewport();
        $this->assertNull($result);
        
        $result = $this->Html->viewport(['block' => true]);
        $this->assertNull($result);
        
        $result = $this->Html->viewport(['block' => false]);
        $expected = [
            'meta' => [
                'name' => 'viewport',
                'content' => 'initial-scale=1, maximum-scale=1, user-scalable=no, width=device-width',
            ],
        ];
        $this->assertHtml($expected, $result);
       
        $result = $this->Html->viewport([
            'block' => false,
            'custom-option' => 'custom-value',
        ]);
        $expected = [
            'meta' => [
                'custom-option' => 'custom-value',
                'name' => 'viewport',
                'content' => 'initial-scale=1, maximum-scale=1, user-scalable=no, width=device-width',
            ],
        ];
        $this->assertHtml($expected, $result);
    }
    
    public function testYoutube()
    {
        $id = 'my-id';
        $url = sprintf('https://www.youtube.com/embed/%s', $id);
        
        $result = $this->Html->youtube($id);
        $expected = [
            'div' => ['class' => 'embed-responsive embed-responsive-16by9'],
            'iframe' => [
                'allowfullscreen' => 'allowfullscreen',
                'height' => '480',
                'width' => '640',
                'class' => 'embed-responsive-item',
                'src' => $url,
            ],
            '/iframe',
            '/div',
        ];
        $this->assertHtml($expected, $result);
        
        $result = $this->Html->youtube($id, ['ratio' => '4by3']);
        $expected = [
            'div' => ['class' => 'embed-responsive embed-responsive-4by3'],
            'iframe' => [
                'allowfullscreen' => 'allowfullscreen',
                'height' => '480',
                'width' => '640',
                'class' => 'embed-responsive-item',
                'src' => $url,
            ],
            '/iframe',
            '/div',
        ];
        $this->assertHtml($expected, $result);
        
        $result = $this->Html->youtube($id, ['ratio' => false]);
        $expected = [
            'iframe' => [
                'allowfullscreen' => 'allowfullscreen',
                'height' => '480',
                'width' => '640',
                'src' => $url,
            ],
        ];
        $this->assertHtml($expected, $result);
        
        $result = $this->Html->youtube($id, ['height' => 100, 'width' => 200]);
        $expected = [
            'div' => ['class' => 'embed-responsive embed-responsive-16by9'],
            'iframe' => [
                'allowfullscreen' => 'allowfullscreen',
                'height' => '100',
                'width' => '200',
                'class' => 'embed-responsive-item',
                'src' => $url,
            ],
            '/iframe',
            '/div',
        ];
        $this->assertHtml($expected, $result);
        
        $result = $this->Html->youtube($id, ['class' => 'my-class']);
        $expected = [
            'div' => ['class' => 'embed-responsive embed-responsive-16by9'],
            'iframe' => [
                'allowfullscreen' => 'allowfullscreen',
                'height' => '480',
                'width' => '640',
                'class' => 'my-class embed-responsive-item',
                'src' => $url,
            ],
            '/iframe',
            '/div',
        ];
        $this->assertHtml($expected, $result);
    }
}
