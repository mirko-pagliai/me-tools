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
use Cake\View\Helper as CakeHelper;
use Cake\View\View;
use MeTools\Utility\OptionsParserTrait;

/**
 * Makes public some protected methods/properties from `OptionsParserTrait`
 */
class OptionsParserHelper extends CakeHelper
{
    use OptionsParserTrait;

    public function setValue($key, $value, array $options)
    {
        return $this->_setValue($key, $value, $options);
    }

    public function turnToArray($value)
    {
        return $this->_toArray($value);
    }

    public function turnToString($value)
    {
        return $this->_toString($value);
    }
}

/**
 * MeTools\Utility\OptionsParserTrait Test Case
 */
class OptionsParserTraitTest extends TestCase
{
    /**
     * @var MeTools\Utility\OptionsParserTrait
     */
    protected $OptionsParser;

    /**
     * setUp method
     * @return void
     */
    public function setUp()
    {
        parent::setUp();

        $this->View = new View();
        $this->OptionsParser = new OptionsParserHelper($this->View);
    }

    /**
     * tearDown method
     * @return void
     */
    public function tearDown()
    {
        parent::tearDown();

        unset($this->OptionsParser, $this->View);
    }

    /**
     * Tests for `_setValue()` method
     * @test
     */
    public function testSetValue()
    {
        $options = ['key' => 'value'];

        $options = $this->OptionsParser->setValue('newKey', 'newValue', $options);
        $this->assertEquals(['key' => 'value', 'newKey' => 'newValue'], $options);

        $options = $this->OptionsParser->setValue('key', 'anotherValue', $options);
        $this->assertEquals(['key' => 'anotherValue', 'newKey' => 'newValue'], $options);
    }

    /**
     * Tests for `_toArray()` method
     * @test
     */
    public function testTurnToArray()
    {
        $this->assertEquals([], $this->OptionsParser->turnToArray(''));
        $this->assertEquals([], $this->OptionsParser->turnToArray('  '));
        $this->assertEquals(['a', 'b', 'c'], $this->OptionsParser->turnToArray('a b c'));
        $this->assertEquals(['a', 'b', 'c'], $this->OptionsParser->turnToArray('a b   c'));
        $this->assertEquals(['b', 'a', 'c'], $this->OptionsParser->turnToArray('b a c b c'));

        //Array
        $this->assertEquals(['an', 'array'], $this->OptionsParser->turnToArray(['an', 'array']));
    }

    /**
     * Tests for `_toString()` method
     * @test
     */
    public function testTurnToString()
    {
        $this->assertEquals('', $this->OptionsParser->turnToString([]));
        $this->assertEquals('a', $this->OptionsParser->turnToString(['a']));
        $this->assertEquals('a b', $this->OptionsParser->turnToString(['a', 'b']));
        $this->assertEquals('a b', $this->OptionsParser->turnToString(['a', 'a', 'b']));
        $this->assertEquals('b a', $this->OptionsParser->turnToString(['b', 'a', 'b']));

        //String
        $this->assertEquals('thisIsAString', $this->OptionsParser->turnToString('thisIsAString'));
    }

    /**
     * Tests for `addButtonClasses()` method
     * @test
     */
    public function testAddButtonClasses()
    {
        $options = $this->OptionsParser->addButtonClasses([]);
        $this->assertEquals('btn btn-default', $options['class']);

        $options = $this->OptionsParser->addButtonClasses([], 'primary');
        $this->assertEquals('btn btn-primary', $options['class']);

        $options = $this->OptionsParser->addButtonClasses([], 'btn primary lg');
        $this->assertEquals('btn btn-primary btn-lg', $options['class']);

        $options = $this->OptionsParser->addButtonClasses([], ['btn', 'primary', 'lg']);
        $this->assertEquals('btn btn-primary btn-lg', $options['class']);

        $options = $this->OptionsParser->addButtonClasses([], ['btn', 'btn-primary', 'lg']);
        $this->assertEquals('btn btn-primary btn-lg', $options['class']);

        $options = $this->OptionsParser->addButtonClasses([], 'primary invalidClass btn-invalid');
        $this->assertEquals('btn btn-primary', $options['class']);

        $options = ['class' => 'existingValue'];
        $options = $this->OptionsParser->addButtonClasses($options, 'btn primary');
        $this->assertEquals('existingValue btn btn-primary', $options['class']);

        $options = ['class' => 'btn-default'];
        $options = $this->OptionsParser->addButtonClasses($options, 'btn primary');
        $this->assertEquals('btn-default btn', $options['class']);

        $options = ['class' => 'btn'];
        $options = $this->OptionsParser->addButtonClasses($options, 'btn primary');
        $this->assertEquals('btn btn-primary', $options['class']);
    }

    /**
     * Tests for `addIconToText()` method
     * @test
     */
    public function testAddIconToText()
    {
        $text = 'My text';

        $result = $this->OptionsParser->addIconToText($text, ['icon' => 'home']);
        $this->assertEquals(['<i class="fa fa-home"> </i> ' . $text, []], $result);

        //Missing `icon` option
        $result = $this->OptionsParser->addIconToText($text, ['class' => 'my-class', 'icon-align' => 'right']);
        $this->assertEquals([$text, ['class' => 'my-class']], $result);

        //Empty text
        $result = $this->OptionsParser->addIconToText(null, ['icon' => 'home']);
        $this->assertEquals(['<i class="fa fa-home"> </i>', []], $result);

        //Using `icon-align` option
        $result = $this->OptionsParser->addIconToText($text, ['icon' => 'home', 'icon-align' => 'right']);
        $this->assertEquals([$text . ' <i class="fa fa-home"> </i>', []], $result);

        //Invalid `icon-align` option
        $result = $this->OptionsParser->addIconToText($text, ['icon' => 'home', 'icon-align' => 'left']);
        $this->assertEquals(['<i class="fa fa-home"> </i> ' . $text, []], $result);
    }

    /**
     * Tests for `icon()` method
     * @test
     */
    public function testIcon()
    {
        $expected = '<i class="fa fa-home"> </i>';

        $result = $this->OptionsParser->icon('home');
        $this->assertEquals($expected, $result);

        $result = $this->OptionsParser->icon('fa-home');
        $this->assertEquals($expected, $result);

        $result = $this->OptionsParser->icon('home fa-home');
        $this->assertEquals($expected, $result);

        $result = $this->OptionsParser->icon('fa fa-home');
        $this->assertEquals($expected, $result);

        $result = $this->OptionsParser->icon('fa-home fa');
        $this->assertEquals($expected, $result);

        $result = $this->OptionsParser->icon('fa home');
        $this->assertEquals($expected, $result);

        $expected = '<i class="fa fa-home fa-2x"> </i>';

        $result = $this->OptionsParser->icon('home 2x');
        $this->assertEquals($expected, $result);

        //As array
        $result = $this->OptionsParser->icon(['fa', 'fa-home', 'fa-2x']);
        $this->assertEquals($expected, $result);

        $result = $this->OptionsParser->icon(['fa', 'home', 'fa-home', 'fa-2x']);
        $this->assertEquals($expected, $result);

        //Multiple arguments
        $result = $this->OptionsParser->icon('fa', 'fa-home', 'fa-2x');
        $this->assertEquals($expected, $result);

        $result = $this->OptionsParser->icon('fa', 'home', 'fa-home', 'fa-2x');
        $this->assertEquals($expected, $result);
    }

    /**
     * Tests for `optionsDefaults()` method
     * @test
     */
    public function testOptionsDefaults()
    {
        $options = ['first' => 'alfa'];

        $options = $this->OptionsParser->optionsDefaults(['second' => 'beta'], $options);
        $this->assertEquals(['first' => 'alfa', 'second' => 'beta'], $options);

        $options = $this->OptionsParser->optionsDefaults([
            'third' => 'gamma',
            'first' => 'newAlfa',
        ], $options);
        $this->assertEquals(['first' => 'alfa', 'second' => 'beta', 'third' => 'gamma'], $options);

        //Called with 3 arguments
        $options = ['first' => 'alfa'];
        $options = $this->OptionsParser->optionsDefaults('second', 'beta', $options);
        $this->assertEquals(['first' => 'alfa', 'second' => 'beta'], $options);

        $options = ['first' => 'alfa'];
        $options = $this->OptionsParser->optionsDefaults('second', ['beta', 'gamma'], $options);
        $this->assertEquals(['first' => 'alfa', 'second' => 'beta gamma'], $options);
    }

    /**
     * Tests for `optionsValues()` method
     * @test
     */
    public function testOptionsValues()
    {
        $options = ['first' => 'alfa'];

        $options = $this->OptionsParser->optionsValues(['first' => 'newAlfa', 'second' => 'beta'], $options);
        $this->assertEquals(['first' => 'alfa newAlfa', 'second' => 'beta'], $options);

        $options = $this->OptionsParser->optionsValues(['first' => 'alfa', 'third' => 'gamma delta'], $options);
        $this->assertEquals(['first' => 'alfa newAlfa', 'second' => 'beta', 'third' => 'gamma delta'], $options);

        //Called with 3 arguments
        $options = ['first' => 'alfa'];

        $options = $this->OptionsParser->optionsValues('first', 'newAlfa', $options);
        $options = $this->OptionsParser->optionsValues('second', 'beta', $options);
        $this->assertEquals(['first' => 'alfa newAlfa', 'second' => 'beta'], $options);

        $options = ['first' => 'alfa'];

        $options = $this->OptionsParser->optionsValues('first', ['beta', 'gamma'], $options);
        $options = $this->OptionsParser->optionsValues('second', 'delta', $options);
        $this->assertEquals(['first' => 'alfa beta gamma', 'second' => 'delta'], $options);
    }
}
