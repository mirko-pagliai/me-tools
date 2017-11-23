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
namespace MeTools\Test\TestCase;

use MeTools\TestSuite\TestCase;
use MeTools\View\OptionsParser;
use Reflection\ReflectionTrait;

/**
 * MeTools\View\OptionsParser Test Case
 */
class OptionsParserTest extends TestCase
{
    use ReflectionTrait;

    /**
     * @var \MeTools\View\OptionsParser
     */
    public $OptionsParser;

    /**
     * setUp method
     * @return void
     */
    public function setUp()
    {
        parent::setUp();

        $options = [
            'array' => ['second', 'first'],
            'alt' => 'this is a string',
            'false' => false,
            'class' => 'first second third fourth',
            'true' => true,
            'null' => null,
            'zero' => 0,
            'zeroAsString' => '0',
            'zeroDotOne' => 0.1,
            'negative' => -1,
        ];

        $defaults = [
            'alt' => 'this value will not be used',
            'defaultKey' => 'defaultValue',
        ];

        $this->OptionsParser = new OptionsParser($options, $defaults);
    }

    /**
     * Tests for `$Default` property
     * @test
     */
    public function testDefaultProperty()
    {
        $this->assertInstanceOf(get_class($this->OptionsParser), $this->OptionsParser->Default);
        $this->assertEquals(get_class_methods($this->OptionsParser), get_class_methods($this->OptionsParser->Default));
        $this->assertNull($this->OptionsParser->Default->Default);

        $this->assertEquals('this value will not be used', $this->OptionsParser->Default->get('alt'));
        $this->assertNull($this->OptionsParser->Default->delete('alt')->get('alt'));
    }

    /**
     * Tests for `buildValue()` method
     * @test
     */
    public function testBuildValue()
    {
        $buildValueMethod = function ($value, $key) {
            return $this->invokeMethod($this->OptionsParser, 'buildValue', [&$value, $key]);
        };

        //Always returns the same string
        foreach (['aaa', ' aaa', ' aaa  '] as $value) {
            $this->assertEquals('aaa', $buildValueMethod($value, 'someKey'));
        }

        //Returns the original value
        foreach ([true, false, 0, '0', 0.1, -1, null, [], [[]]] as $value) {
            $this->assertEquals($value, $buildValueMethod($value, 'someKey'));
        }

        foreach (['class', 'data-toggle'] as $key) {
            //Returns a string with the ordered values
            foreach ([
                'first  second third fourth first  ',
                ['first', 'second', 'third', '', ' ', 'fourth', 'first', null, false],
                ['first', ['second', ['third']], ['fourth']],
            ] as $value) {
                $this->assertEquals('first fourth second third', $buildValueMethod($value, $key));
            }
        }
    }

    /**
     * Tests for `add()` method
     * @test
     */
    public function testAdd()
    {
        $result = $this->OptionsParser->add('newKey', 'newValue');
        $this->assertInstanceOf('MeTools\View\OptionsParser', $result);
        $this->assertEquals('newValue', $this->OptionsParser->get('newKey'));

        foreach ([
            'a c  b d ',
            ['a', 'c', '', ' ', 'b', 'd'],
            ['a', 'c', [''], [' ', ['b'], 'd']],
        ] as $value) {
            $this->OptionsParser->add('class', $value);
            $this->assertEquals('a b c d', $this->OptionsParser->get('class'));
        }

        //This rewrites the existing value
        $this->OptionsParser->add('alt', 'a new alt value');
        $this->assertEquals('a new alt value', $this->OptionsParser->get('alt'));

        //Array of arguments
        $result = $this->OptionsParser->add(['firstKey' => 'firstValue', 'secondKey' => 'secondValue']);
        $this->assertInstanceOf('MeTools\View\OptionsParser', $result);
        $this->assertEquals('firstValue', $this->OptionsParser->get('firstKey'));
        $this->assertEquals('secondValue', $this->OptionsParser->get('secondKey'));
    }

    /**
     * Tests for `append()` method
     * @test
     */
    public function testAppend()
    {
        $result = $this->OptionsParser->append('newKey', 'newValue');
        $this->assertInstanceOf('MeTools\View\OptionsParser', $result);
        $this->assertEquals('newValue', $this->OptionsParser->get('newKey'));

        //The value and the existing value are both strings
        $this->OptionsParser->append('alt', '  with append ');
        $this->assertEquals('this is a string with append', $this->OptionsParser->get('alt'));

        //The value and the existing value are both arrays
        $this->OptionsParser->append('array', ['third', 'fourth']);
        $this->assertEquals(['second', 'first', 'third', 'fourth'], $this->OptionsParser->get('array'));

        //Mixed values, boolean and string
        $this->OptionsParser->append('true', 'a string');
        $this->assertEquals([true, 'a string'], $this->OptionsParser->get('true'));

        //Mixed values, boolean and array
        $this->OptionsParser->append('false', ['an array']);
        $this->assertEquals([false, 'an array'], $this->OptionsParser->get('false'));

        $this->OptionsParser->append('class', ['six', ['five']]);
        $this->assertEquals('first five fourth second six third', $this->OptionsParser->get('class'));

        //Array of arguments
        $result = $this->OptionsParser->append(['zeroAsString' => ' with string', 'zeroDotOne' => 2]);
        $this->assertInstanceOf('MeTools\View\OptionsParser', $result);
        $this->assertEquals('0 with string', $this->OptionsParser->get('zeroAsString'));
        $this->assertEquals([0.1, 2], $this->OptionsParser->get('zeroDotOne'));
    }

    /**
     * Tests for `contains()` method
     * @test
     */
    public function testContains()
    {
        $this->assertFalse($this->OptionsParser->contains('alt', 'a string'));
        $this->assertTrue($this->OptionsParser->contains('alt', 'this is a string'));
        $this->assertFalse($this->OptionsParser->contains('array', 'third'));
        $this->assertTrue($this->OptionsParser->contains('array', 'first'));
        $this->assertFalse($this->OptionsParser->contains('noExistingKey', null));

        //On a value that must be exploded
        $this->assertFalse($this->OptionsParser->contains('class', 'five'));
        $this->assertTrue($this->OptionsParser->contains('class', 'fourth'));

        //Arrays
        $this->assertFalse($this->OptionsParser->contains('array', ['second', 'first', 'third']));
        $this->assertTrue($this->OptionsParser->contains('array', ['second', 'first']));
        $this->assertTrue($this->OptionsParser->contains('array', ['first', 'second']));
    }

    /**
     * Tests for `delete()` method
     * @test
     */
    public function testDelete()
    {
        $result = $this->OptionsParser->delete('class');
        $this->assertInstanceOf('MeTools\View\OptionsParser', $result);
        $this->assertFalse($this->OptionsParser->exists('class'));

        //This returns `true,` because it exists as the default value
        $this->OptionsParser->delete('alt');
        $this->assertTrue($this->OptionsParser->exists('alt'));
        $this->assertEquals('this value will not be used', $this->OptionsParser->get('alt'));

        //Array of arguments
        $result = $this->OptionsParser->delete(['zero', 'zeroAsString']);
        $this->assertInstanceOf('MeTools\View\OptionsParser', $result);
        $this->assertFalse($this->OptionsParser->exists('zero'));
        $this->assertFalse($this->OptionsParser->exists('zeroAsString'));

        //Multiple arguments
        $result = $this->OptionsParser->delete('true', 'false');
        $this->assertInstanceOf('MeTools\View\OptionsParser', $result);
        $this->assertFalse($this->OptionsParser->exists('true'));
        $this->assertFalse($this->OptionsParser->exists('false'));
    }

    /**
     * Tests for `exists()` method
     * @test
     */
    public function testExists()
    {
        $this->assertTrue($this->OptionsParser->exists('alt'));

        //This key exists in each case, because it is present as the default value
        $this->assertTrue($this->OptionsParser->exists('defaultKey'));

        //The value is empty (`false`), but the key exists anyway
        $this->assertTrue($this->OptionsParser->exists('false'));

        $this->assertFalse($this->OptionsParser->exists('noExistingKey'));
    }

    /**
     * Tests for `get()` method
     * @test
     */
    public function testGet()
    {
        $this->assertEquals('this is a string', $this->OptionsParser->get('alt'));
        $this->assertEquals('first fourth second third', $this->OptionsParser->get('class'));

        //Default value
        $this->assertEquals('defaultValue', $this->OptionsParser->get('defaultKey'));

        $this->assertNull($this->OptionsParser->get('noExistingKey'));
    }

    /**
     * Tests for `toArray()` method
     * @test
     */
    public function testToArray()
    {
        $this->assertEquals([
            'alt' => 'this is a string',
            'array' => ['second', 'first'],
            'class' => 'first fourth second third',
            'defaultKey' => 'defaultValue',
            'false' => false,
            'negative' => -1,
            'null' => null,
            'true' => true,
            'zero' => 0,
            'zeroAsString' => '0',
            'zeroDotOne' => 0.1,
        ], $this->OptionsParser->toArray());
    }

    /**
     * Tests for `toArray()` method
     * @test
     */
    public function testToString()
    {
        $this->assertEquals(
            'alt="this is a string" array="second first" ' .
            'class="first fourth second third" defaultKey="defaultValue" ' .
            'false="false" negative="-1" null="null" true="true" zero="0" ' .
            'zeroAsString="0" zeroDotOne="0.1"',
            $this->OptionsParser->toString()
        );
    }
}
