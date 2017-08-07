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

use Cake\View\Helper as CakeHelper;
use Cake\View\StringTemplateTrait;
use Cake\View\View;
use MeTools\TestSuite\TestCase;
use MeTools\Utility\OptionsParserTrait;

/**
 * Allow to use `StringTemplateTrait`
 */
class OptionsParserHelper extends CakeHelper
{
    use OptionsParserTrait;
    use StringTemplateTrait;
}

/**
 * MeTools\Utility\OptionsParserTrait Test Case
 */
class OptionsParserTraitTest extends TestCase
{
    /**
     * @var \OptionsParserHelper
     */
    protected $OptionsParser;

    /**
     * setUp method
     * @return void
     */
    public function setUp()
    {
        parent::setUp();

        $this->OptionsParser = new OptionsParserHelper(new View);
    }

    /**
     * Tests for `setValue()` method
     * @test
     */
    public function testSetValue()
    {
        $options = ['key' => 'value'];

        $options = $this->invokeMethod($this->OptionsParser, 'setValue', ['newKey', 'newValue', $options]);
        $this->assertEquals(['key' => 'value', 'newKey' => 'newValue'], $options);

        $options = $this->invokeMethod($this->OptionsParser, 'setValue', ['key', 'anotherValue', $options]);
        $this->assertEquals(['key' => 'anotherValue', 'newKey' => 'newValue'], $options);
    }

    /**
     * Tests for `toArray()` method
     * @test
     */
    public function testToArray()
    {
        $toArrayMethod = function($value) {
            return $this->invokeMethod($this->OptionsParser, 'toArray', [$value]);
        };

        $this->assertEquals([], $toArrayMethod(''));
        $this->assertEquals([], $toArrayMethod('  '));
        $this->assertEquals(['a', 'b', 'c'], $toArrayMethod('a b c'));
        $this->assertEquals(['a', 'b', 'c'], $toArrayMethod('a b   c'));
        $this->assertEquals(['b', 'a', 'c'], $toArrayMethod('b a c b c'));

        //Array
        $this->assertEquals(['an', 'array'], $toArrayMethod(['an', 'array']));
    }

    /**
     * Tests for `toString()` method
     * @test
     */
    public function testToString()
    {
        $toStringMethod = function($value) {
            return $this->invokeMethod($this->OptionsParser, 'toString', [$value]);
        };

        $this->assertEquals('', $toStringMethod([]));
        $this->assertEquals('a', $toStringMethod(['a']));
        $this->assertEquals('a b', $toStringMethod(['a', 'b']));
        $this->assertEquals('a b', $toStringMethod(['a', 'a', 'b']));
        $this->assertEquals('b a', $toStringMethod(['b', 'a', 'b']));

        //String
        $this->assertEquals('thisIsAString', $toStringMethod('thisIsAString'));
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
     * Tests for `addTooltip()` method
     * @test
     */
    public function testAddTooltip()
    {
        $tooltip = 'My tooltip';
        $expected = ['data-toggle' => 'tooltip', 'title' => $tooltip];

        $result = $this->OptionsParser->addTooltip(['tooltip' => $tooltip]);
        $this->assertEquals($expected, $result);

        // `tooltip` rewrites `title`
        $result = $this->OptionsParser->addTooltip([
            'title' => 'my title',
            'tooltip' => $tooltip,
        ]);
        $this->assertEquals($expected, $result);

        $result = $this->OptionsParser->addTooltip([
            'data-toggle' => 'some-data-here',
            'tooltip' => $tooltip,
        ]);
        $expected = [
            'data-toggle' => 'some-data-here tooltip',
            'title' => $tooltip,
        ];
        $this->assertEquals($expected, $result);

        $result = $this->OptionsParser->addTooltip([
            'tooltip' => $tooltip,
            'tooltip-align' => 'bottom',
        ]);
        $expected = [
            'data-toggle' => 'tooltip',
            'title' => $tooltip,
            'data-placement' => 'bottom',
        ];
        $this->assertEquals($expected, $result);
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
