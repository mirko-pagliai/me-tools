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
use MeTools\Utility\OptionsParserTrait;

/**
 * Makes public some protected methods/properties from `OptionsParserTrait`
 */
class OptionsParser
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
    protected $Trait;

    /**
     * setUp method
     * @return void
     */
    public function setUp()
    {
        parent::setUp();

        $this->Trait = new OptionsParser;
    }

    /**
     * tearDown method
     * @return void
     */
    public function tearDown()
    {
        parent::tearDown();

        unset($this->Trait);
    }

    /**
     * Tests for `_setValue()` method
     * @test
     */
    public function testSetValue()
    {
        $options = ['key' => 'value'];

        $options = $this->Trait->setValue('newKey', 'newValue', $options);
        $this->assertEquals(['key' => 'value', 'newKey' => 'newValue'], $options);

        $options = $this->Trait->setValue('key', 'anotherValue', $options);
        $this->assertEquals(['key' => 'anotherValue', 'newKey' => 'newValue'], $options);
    }

    /**
     * Tests for `_toArray()` method
     * @test
     */
    public function testTurnToArray()
    {
        $this->assertEquals([], $this->Trait->turnToArray(''));
        $this->assertEquals([], $this->Trait->turnToArray('  '));
        $this->assertEquals(['a', 'b', 'c'], $this->Trait->turnToArray('a b c'));
        $this->assertEquals(['a', 'b', 'c'], $this->Trait->turnToArray('a b   c'));
        $this->assertEquals(['b', 'a', 'c'], $this->Trait->turnToArray('b a c b c'));

        //Array
        $this->assertEquals(['an', 'array'], $this->Trait->turnToArray(['an', 'array']));
    }

    /**
     * Tests for `_toString()` method
     * @test
     */
    public function testTurnToString()
    {
        $this->assertEquals('', $this->Trait->turnToString([]));
        $this->assertEquals('a', $this->Trait->turnToString(['a']));
        $this->assertEquals('a b', $this->Trait->turnToString(['a', 'b']));
        $this->assertEquals('a b', $this->Trait->turnToString(['a', 'a', 'b']));
        $this->assertEquals('b a', $this->Trait->turnToString(['b', 'a', 'b']));

        //String
        $this->assertEquals('thisIsAString', $this->Trait->turnToString('thisIsAString'));
    }

    /**
     * Tests for `addButtonClasses()` method
     * @test
     */
    public function testAddButtonClasses()
    {
        $options = $this->Trait->addButtonClasses([]);
        $this->assertEquals('btn btn-default', $options['class']);

        $options = $this->Trait->addButtonClasses([], 'primary');
        $this->assertEquals('btn btn-primary', $options['class']);

        $options = $this->Trait->addButtonClasses([], 'btn primary lg');
        $this->assertEquals('btn btn-primary btn-lg', $options['class']);

        $options = $this->Trait->addButtonClasses([], ['btn', 'primary', 'lg']);
        $this->assertEquals('btn btn-primary btn-lg', $options['class']);

        $options = $this->Trait->addButtonClasses([], ['btn', 'btn-primary', 'lg']);
        $this->assertEquals('btn btn-primary btn-lg', $options['class']);

        $options = $this->Trait->addButtonClasses([], 'primary invalidClass btn-invalid');
        $this->assertEquals('btn btn-primary', $options['class']);

        $options = ['class' => 'existingValue'];
        $options = $this->Trait->addButtonClasses($options, 'btn primary');
        $this->assertEquals('existingValue btn btn-primary', $options['class']);

        $options = ['class' => 'btn-default'];
        $options = $this->Trait->addButtonClasses($options, 'btn primary');
        $this->assertEquals('btn-default btn', $options['class']);

        $options = ['class' => 'btn'];
        $options = $this->Trait->addButtonClasses($options, 'btn primary');
        $this->assertEquals('btn btn-primary', $options['class']);
    }

    /**
     * Tests for `icon()` method
     * @test
     */
    public function testIcon()
    {
        $expected = '<i class="fa fa-home"> </i>';

        $result = $this->Trait->icon('home');
        $this->assertEquals($expected, $result);

        $result = $this->Trait->icon('fa-home');
        $this->assertEquals($expected, $result);

        $result = $this->Trait->icon('home fa-home');
        $this->assertEquals($expected, $result);

        $result = $this->Trait->icon('fa fa-home');
        $this->assertEquals($expected, $result);

        $result = $this->Trait->icon('fa-home fa');
        $this->assertEquals($expected, $result);

        $result = $this->Trait->icon('fa home');
        $this->assertEquals($expected, $result);

        $expected = '<i class="fa fa-home fa-2x"> </i>';

        $result = $this->Trait->icon('home 2x');
        $this->assertEquals($expected, $result);

        //As array
        $result = $this->Trait->icon(['fa', 'fa-home', 'fa-2x']);
        $this->assertEquals($expected, $result);

        $result = $this->Trait->icon(['fa', 'home', 'fa-home', 'fa-2x']);
        $this->assertEquals($expected, $result);

        //Multiple arguments
        $result = $this->Trait->icon('fa', 'fa-home', 'fa-2x');
        $this->assertEquals($expected, $result);

        $result = $this->Trait->icon('fa', 'home', 'fa-home', 'fa-2x');
        $this->assertEquals($expected, $result);
    }

    /**
     * Tests for `optionsDefaults()` method
     * @test
     */
    public function testOptionsDefaults()
    {
        $options = ['first' => 'alfa'];

        $options = $this->Trait->optionsDefaults(['second' => 'beta'], $options);
        $this->assertEquals(['first' => 'alfa', 'second' => 'beta'], $options);

        $options = $this->Trait->optionsDefaults([
            'third' => 'gamma',
            'first' => 'newAlfa',
        ], $options);
        $this->assertEquals(['first' => 'alfa', 'second' => 'beta', 'third' => 'gamma'], $options);

        //Called with 3 arguments
        $options = ['first' => 'alfa'];
        $options = $this->Trait->optionsDefaults('second', 'beta', $options);
        $this->assertEquals(['first' => 'alfa', 'second' => 'beta'], $options);

        $options = ['first' => 'alfa'];
        $options = $this->Trait->optionsDefaults('second', ['beta', 'gamma'], $options);
        $this->assertEquals(['first' => 'alfa', 'second' => 'beta gamma'], $options);
    }

    /**
     * Tests for `optionsValues()` method
     * @test
     */
    public function testOptionsValues()
    {
        $options = ['first' => 'alfa'];

        $options = $this->Trait->optionsValues(['first' => 'newAlfa', 'second' => 'beta'], $options);
        $this->assertEquals(['first' => 'alfa newAlfa', 'second' => 'beta'], $options);

        $options = $this->Trait->optionsValues(['first' => 'alfa', 'third' => 'gamma delta'], $options);
        $this->assertEquals(['first' => 'alfa newAlfa', 'second' => 'beta', 'third' => 'gamma delta'], $options);

        //Called with 3 arguments
        $options = ['first' => 'alfa'];

        $options = $this->Trait->optionsValues('first', 'newAlfa', $options);
        $options = $this->Trait->optionsValues('second', 'beta', $options);
        $this->assertEquals(['first' => 'alfa newAlfa', 'second' => 'beta'], $options);

        $options = ['first' => 'alfa'];

        $options = $this->Trait->optionsValues('first', ['beta', 'gamma'], $options);
        $options = $this->Trait->optionsValues('second', 'delta', $options);
        $this->assertEquals(['first' => 'alfa beta gamma', 'second' => 'delta'], $options);
    }
}
