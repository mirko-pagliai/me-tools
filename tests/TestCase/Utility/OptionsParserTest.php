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
use MeTools\Utility\OptionsParser as BaseOptionsParser;

/**
 * Makes public some protected methods/properties from `OptionsParser`
 */
class OptionsParser extends BaseOptionsParser
{
    public function setValue($key, $value)
    {
        return $this->_setValue($key, $value);
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
 * MeTools\Utility\OptionsParser Test Case
 */
class OptionsParserTest extends TestCase
{
    /**
     * Tests for `__debugInfo()` method
     * @test
     */
    public function testDebugInfo()
    {
        ob_start();
        var_dump(new OptionsParser(['key' => 'value']));
        $result = ob_get_clean();

        $expected = 'object(MeTools\Test\TestCase\OptionsParser)#50 (1) {' . PHP_EOL . '  ["key"]=>' . PHP_EOL . '  string(5) "value"' . PHP_EOL . '}' . PHP_EOL;
        $this->assertEquals($expected, $result);
    }

    /**
     * Tests for `_setValue()` method
     * @test
     */
    public function testSetValue()
    {
        $parser = new OptionsParser(['key' => 'value']);

        $parser->setValue('newKey', 'newValue');
        $this->assertEquals(['key' => 'value', 'newKey' => 'newValue'], $parser->toArray());

        $parser->setValue('key', 'anotherValue');
        $this->assertEquals(['key' => 'anotherValue', 'newKey' => 'newValue'], $parser->toArray());
    }

    /**
     * Tests for `_toArray()` method
     * @test
     */
    public function testTurnToArray()
    {
        $parser = new OptionsParser;

        $this->assertEquals([], $parser->turnToArray(''));
        $this->assertEquals([], $parser->turnToArray('  '));
        $this->assertEquals(['a', 'b', 'c'], $parser->turnToArray('a b c'));
        $this->assertEquals(['a', 'b', 'c'], $parser->turnToArray('a b   c'));
        $this->assertEquals(['b', 'a', 'c'], $parser->turnToArray('b a c b c'));

        //Array
        $this->assertEquals(['an', 'array'], $parser->turnToArray(['an', 'array']));
    }

    /**
     * Tests for `_toString()` method
     * @test
     */
    public function testTurnToString()
    {
        $parser = new OptionsParser;

        $this->assertEquals('', $parser->turnToString([]));
        $this->assertEquals('a', $parser->turnToString(['a']));
        $this->assertEquals('a b', $parser->turnToString(['a', 'b']));
        $this->assertEquals('a b', $parser->turnToString(['a', 'a', 'b']));
        $this->assertEquals('b a', $parser->turnToString(['b', 'a', 'b']));

        //String
        $this->assertEquals('thisIsAString', $parser->turnToString('thisIsAString'));
    }

    /**
     * Tests for `addDefaults()` method
     * @test
     */
    public function testAddDefaults()
    {
        $parser = new OptionsParser(['first' => 'alfa']);

        $parser->addDefaults(['second' => 'beta']);
        $parser->addDefaults(['first' => 'newAlfa']);
        $this->assertEquals(['first' => 'alfa', 'second' => 'beta'], $parser->toArray());

        $parser->addDefaults([
            'third' => 'gamma',
            'first' => 'newAlfa',
        ]);
        $this->assertEquals(['first' => 'alfa', 'second' => 'beta', 'third' => 'gamma'], $parser->toArray());

        //With 2 arguments
        $parser = new OptionsParser(['first' => 'alfa']);
        $parser->addDefaults('second', 'beta');
        $this->assertEquals(['first' => 'alfa', 'second' => 'beta'], $parser->toArray());
    }

    /**
     * Tests for `get()` method
     * @test
     */
    public function testGet()
    {
        $parser = new OptionsParser(['key' => 'value']);

        $this->assertEquals('value', $parser->get('key'));
        $this->assertEquals(null, $parser->get('noExistingKey'));
    }

    /**
     * Tests for `toArray()` method
     * @test
     */
    public function testToArray()
    {
        $parser = new OptionsParser;
        $this->assertEquals([], $parser->toArray());

        $parser = new OptionsParser(['key' => 'value']);
        $this->assertEquals(['key' => 'value'], $parser->toArray());
    }
}
