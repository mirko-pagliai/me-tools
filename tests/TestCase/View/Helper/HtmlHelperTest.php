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
     * Tests for `__call()` method
     * @return void
     * @test
     */
    public function testMagicCall()
    {
        //The `h3()` method should not exist, otherwise the `__call()` method
        //  will not be called
        $this->assertFalse(method_exists($this->Html, 'h3'));
        
        $result = $this->Html->h3('my h3 text', ['class' => 'myclass']);
        $expected = [
            'h3' => ['class' => 'myclass'],
            'my h3 text',
            '/h3'
        ];
        $this->assertHtml($expected, $result);
        
        
        $result = $this->Html->h3('my h3 text', ['class' => 'myclass', 'icon' => 'home']);
        $expected = [
            'h3' => ['class' => 'myclass'],
            'i' => ['class' => 'fa fa-home'],
            ' ',
            '/i',
            ' ',
            'my h3 text',
            '/h3'
        ];
        $this->assertHtml($expected, $result);
    }
}
