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
namespace MeTools\Test\TestCase\View;

use MeTools\TestSuite\TestCase;
use MeTools\View\OptionsParser;

/**
 * MeTools\View\OptionsParser Test Case
 */
class OptionsParserTest extends TestCase
{
    /**
     * @var \MeTools\View\OptionsParser
     */
    public OptionsParser $OptionsParser;

    /**
     * Called before every test method
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        if (empty($this->OptionsParser)) {
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
    }

    /**
     * Tests for `$Default` property
     * @test
     */
    public function testDefaultProperty(): void
    {
        $this->assertInstanceOf(OptionsParser::class, $this->OptionsParser->Default);
        $this->assertSameMethods($this->OptionsParser, $this->OptionsParser->Default);

        $this->assertEquals('this value will not be used', $this->OptionsParser->Default->get('alt'));
        $this->assertNull($this->OptionsParser->Default->delete('alt')->get('alt'));
    }

    /**
     * Tests for `buildValue()` method
     * @test
     */
    public function testBuildValue(): void
    {
        $buildValueMethod = fn($value, $key) => $this->invokeMethod($this->OptionsParser, 'buildValue', [&$value, $key]);

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
    public function testAdd(): void
    {
        $result = $this->OptionsParser->add('newKey', 'newValue');
        $this->assertInstanceOf(OptionsParser::class, $result);
        $this->assertEquals('newValue', $result->get('newKey'));

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
        $this->assertInstanceOf(OptionsParser::class, $result);
        $this->assertEquals('firstValue', $result->get('firstKey'));
        $this->assertEquals('secondValue', $result->get('secondKey'));
    }

    /**
     * Tests for `addButtonClasses()` method
     * @test
     */
    public function testAddButtonClasses(): void
    {
        $OptionsParser = new OptionsParser();
        $result = $OptionsParser->addButtonClasses();
        $this->assertInstanceOf(OptionsParser::class, $result);
        $this->assertEquals('btn btn-light', $OptionsParser->get('class'));

        $OptionsParser = new OptionsParser();
        $OptionsParser->addButtonClasses('primary');
        $this->assertEquals('btn btn-primary', $OptionsParser->get('class'));

        foreach ([
            'primary lg',
            'primary lg lg',
            'btn btn primary primary lg',
        ] as $classes) {
            $OptionsParser = new OptionsParser();
            $OptionsParser->addButtonClasses($classes);
            $this->assertEquals('btn btn-lg btn-primary', $OptionsParser->get('class'));
        }

        //As multiple arguments
        $expected = 'btn btn-lg btn-primary';
        $OptionsParser = new OptionsParser();
        $OptionsParser->addButtonClasses('btn', 'btn-primary', 'lg');
        $this->assertEquals($expected, $OptionsParser->get('class'));

        $OptionsParser = new OptionsParser();
        $OptionsParser->addButtonClasses('btn', 'btn-primary', 'btn-primary', 'lg');
        $this->assertEquals($expected, $OptionsParser->get('class'));

        $OptionsParser = new OptionsParser();
        $OptionsParser->addButtonClasses('btn-primary', 'lg');
        $this->assertEquals($expected, $OptionsParser->get('class'));

        $OptionsParser = new OptionsParser();
        $OptionsParser->addButtonClasses('lg');
        $this->assertEquals('btn btn-lg', $OptionsParser->get('class'));

        $OptionsParser = new OptionsParser();
        $OptionsParser->addButtonClasses('secondary lg');
        $this->assertEquals('btn btn-lg btn-secondary', $OptionsParser->get('class'));

        $OptionsParser = new OptionsParser();
        $OptionsParser->addButtonClasses('invalidClass btn-invalid');
        $this->assertEquals('btn', $OptionsParser->get('class'));

        $OptionsParser = new OptionsParser();
        $OptionsParser->addButtonClasses('primary invalidClass btn-invalid');
        $this->assertEquals('btn btn-primary', $OptionsParser->get('class'));

        $OptionsParser = new OptionsParser(['class' => 'existingValue']);
        $OptionsParser->addButtonClasses('btn primary');
        $this->assertEquals('btn btn-primary existingValue', $OptionsParser->get('class'));

        $OptionsParser = new OptionsParser(['class' => 'btn-secondary']);
        $OptionsParser->addButtonClasses('btn primary');
        $this->assertEquals('btn btn-secondary', $OptionsParser->get('class'));

        $OptionsParser = new OptionsParser(['class' => 'btn']);
        $OptionsParser->addButtonClasses('btn primary');
        $this->assertEquals('btn btn-primary', $OptionsParser->get('class'));

        $OptionsParser = new OptionsParser(['class' => 'btn-lg']);
        $OptionsParser->addButtonClasses('btn lg');
        $this->assertEquals('btn btn-lg', $OptionsParser->get('class'));
    }

    /**
     * Tests for `append()` method
     * @test
     */
    public function testAppend(): void
    {
        $result = $this->OptionsParser->append('newKey', 'newValue');
        $this->assertInstanceOf(OptionsParser::class, $result);
        $this->assertEquals('newValue', $result->get('newKey'));

        //The value and the existing value are both strings
        $result->append('alt', '  with append ');
        $this->assertEquals('this is a string with append', $result->get('alt'));

        //The value and the existing value are both arrays
        $result->append('array', ['third', 'fourth']);
        $this->assertEquals(['second', 'first', 'third', 'fourth'], $result->get('array'));

        //Mixed values, boolean and string
        $result->append('true', 'a string');
        $this->assertEquals([true, 'a string'], $result->get('true'));

        //Mixed values, boolean and array
        $result->append('false', ['an array']);
        $this->assertEquals([false, 'an array'], $result->get('false'));

        $result->append('class', ['six', ['five']]);
        $this->assertEquals('first five fourth second six third', $result->get('class'));

        //Array of arguments
        $result = $result->append(['zeroAsString' => ' with string', 'zeroDotOne' => 2]);
        $this->assertInstanceOf(OptionsParser::class, $result);
        $this->assertEquals('0 with string', $result->get('zeroAsString'));
        $this->assertEquals([0.1, 2], $result->get('zeroDotOne'));
    }

    /**
     * Tests for `consume()` method
     * @test
     */
    public function testConsume(): void
    {
        $this->assertEquals('first fourth second third', $this->OptionsParser->consume('class'));
        $this->assertNull($this->OptionsParser->get('class'));
        $this->assertFalse($this->OptionsParser->exists('class'));

        //Default value
        $expected = 'defaultValue';
        $this->assertEquals($expected, $this->OptionsParser->consume('defaultKey'));
        $this->assertEquals($expected, $this->OptionsParser->get('defaultKey'));
        $this->assertTrue($this->OptionsParser->exists('defaultKey'));

        $this->assertNull($this->OptionsParser->consume('noExistingKey'));
    }

    /**
     * Tests for `contains()` method
     * @test
     */
    public function testContains(): void
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
    public function testDelete(): void
    {
        $result = $this->OptionsParser->delete('class');
        $this->assertInstanceOf(OptionsParser::class, $result);
        $this->assertFalse($result->exists('class'));

        //This returns `true,` because it exists as the default value
        $result->delete('alt');
        $this->assertTrue($result->exists('alt'));
        $this->assertEquals('this value will not be used', $result->get('alt'));

        //Multiple arguments
        $result->delete('true', 'false');
        $this->assertFalse($result->exists('true'));
        $this->assertFalse($result->exists('false'));
    }

    /**
     * Tests for `exists()` method
     * @test
     */
    public function testExists(): void
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
    public function testGet(): void
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
    public function testToArray(): void
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
    public function testToString(): void
    {
        $this->assertEquals(
            'alt="this is a string" array="second first" ' .
            'class="first fourth second third" defaultKey="defaultValue" ' .
            'false="false" negative="-1" null="null" true="true" zero="0" ' .
            'zeroAsString="0" zeroDotOne="0.1"',
            $this->OptionsParser->toString()
        );
    }

    /**
     * Tests for `tooltip()` method
     * @test
     */
    public function testTooltip(): void
    {
        $this->OptionsParser->add('title', 'a title');
        $this->assertEquals('a title', $this->OptionsParser->get('title'));

        $result = $this->OptionsParser->add('tooltip', 'a tooltip')->tooltip();
        $this->assertInstanceOf(OptionsParser::class, $result);
        $this->assertEquals('a tooltip', $result->get('title'));
        $this->assertFalse($result->exists('data-placement'));

        $result->add(['tooltip' => 'a tooltip', 'tooltip-align' => 'right'])->tooltip();
        $this->assertEquals('a tooltip', $result->get('title'));
        $this->assertEquals('right', $result->get('data-placement'));
    }
}
