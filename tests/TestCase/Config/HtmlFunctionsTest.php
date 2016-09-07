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

/**
 * HtmlFunctionsTest class.
 *
 * It tests global functions declared in `config/functions/html.php`.
 */
class HtmlFunctionsTest extends TestCase
{
    /**
     * Test for `buttonClass()` global function
     * @return void
     * @test
     */
    public function testButtonClass()
    {
        $result = buttonClass();
        $expected = ['class' => 'btn btn-default'];
        $this->assertEquals($expected, $result);

        $result = buttonClass(['class' => 'my-class']);
        $expected = ['class' => 'my-class btn btn-default'];
        $this->assertEquals($expected, $result);

        $result = buttonClass(['class' => 'btn']);
        $expected = ['class' => 'btn btn-default'];
        $this->assertEquals($expected, $result);

        $result = buttonClass(['class' => 'btn-primary']);
        $expected = ['class' => 'btn-primary btn'];
        $this->assertEquals($expected, $result);

        $result = buttonClass(['class' => 'btn btn-primary']);
        $expected = ['class' => 'btn btn-primary'];
        $this->assertEquals($expected, $result);

        $result = buttonClass(['class' => 'my-class btn']);
        $expected = ['class' => 'my-class btn btn-default'];
        $this->assertEquals($expected, $result);

        $result = buttonClass(['class' => 'my-class btn'], 'primary');
        $expected = ['class' => 'my-class btn btn-primary'];
        $this->assertEquals($expected, $result);

        $result = buttonClass(['class' => 'my-class btn-primary']);
        $expected = ['class' => 'my-class btn-primary btn'];
        $this->assertEquals($expected, $result);

        $result = buttonClass(['class' => 'my-class btn btn-primary']);
        $expected = ['class' => 'my-class btn btn-primary'];
        $this->assertEquals($expected, $result);

        $result = buttonClass([], 'primary');
        $expected = ['class' => 'btn btn-primary'];
        $this->assertEquals($expected, $result);
    }

    /**
     * Test for `optionDefaults()` global function
     * @return void
     * @test
     */
    public function testOptionDefault()
    {
        $options = ['value1' => 'val-1'];

        $result = optionDefaults(['class' => 'my-class'], $options);
        $expected = [
            'value1' => 'val-1',
            'class' => 'my-class',
        ];
        $this->assertEquals($expected, $result);

        $result = optionDefaults([
            'class' => 'first-class second-class',
        ], $options);
        $expected = [
            'value1' => 'val-1',
            'class' => 'first-class second-class',
        ];
        $this->assertEquals($expected, $result);

        $result = optionDefaults([
            'class' => ['first-class', 'second-class'],
        ], $options);
        $expected = [
            'value1' => 'val-1',
            'class' => 'first-class second-class',
        ];
        $this->assertEquals($expected, $result);

        $result = optionDefaults([
            'class' => ['first-class', 'second-class', 'first-class'],
        ], $options);
        $expected = [
            'value1' => 'val-1',
            'class' => 'first-class second-class',
        ];
        $this->assertEquals($expected, $result);

        $result = optionDefaults([
            'class' => ['first-class', ['second-class']],
        ], $options);
        $expected = [
            'value1' => 'val-1',
            'class' => 'first-class second-class',
        ];
        $this->assertEquals($expected, $result);

        $result = optionDefaults([
            'class' => ['first-class', ['second-class', ['third-class']]],
        ], $options);
        $expected = [
            'value1' => 'val-1',
            'class' => 'first-class second-class third-class',
        ];
        $this->assertEquals($expected, $result);

        //This doesn't change the value
        $result = optionDefaults(['value1' => 'new-val-1'], $options);
        $expected = ['value1' => 'val-1'];
        $this->assertEquals($expected, $result);

        //Backward compatibility with three arguments
        $result = optionDefaults('class', 'my-class', $options);
        $expected = [
            'value1' => 'val-1',
            'class' => 'my-class',
        ];
        $this->assertEquals($expected, $result);

        //Backward compatibility with three arguments
        $result = optionDefaults(
            'class',
            'first-class second-class',
            $options
        );
        $expected = [
            'value1' => 'val-1',
            'class' => 'first-class second-class',
        ];
        $this->assertEquals($expected, $result);
    }

    /**
     * Test for `optionValues()` global function
     * @return void
     * @test
     */
    public function testOptionValue()
    {
        $options = ['value1' => 'alfa beta'];

        $result = optionValues(['class' => 'my-class'], $options);
        $expected = ['value1' => 'alfa beta', 'class' => 'my-class'];
        $this->assertEquals($expected, $result);

        $result = optionValues(['class' => ['my-class']], $options);
        $expected = ['value1' => 'alfa beta', 'class' => 'my-class'];
        $this->assertEquals($expected, $result);

        $result = optionValues(['value1' => 'beta'], $options);
        $expected = ['value1' => 'alfa beta'];
        $this->assertEquals($expected, $result);

        $result = optionValues(['value1' => 'gamma'], $options);
        $expected = ['value1' => 'alfa beta gamma'];
        $this->assertEquals($expected, $result);

        $result = optionValues(['value1' => 'delta gamma'], $options);
        $expected = ['value1' => 'alfa beta delta gamma'];
        $this->assertEquals($expected, $result);

        $result = optionValues(['value1' => 'delta gamma delta'], $options);
        $expected = ['value1' => 'alfa beta delta gamma'];
        $this->assertEquals($expected, $result);

        $result = optionValues(['value1' => ['delta', 'gamma']], $options);
        $expected = ['value1' => 'alfa beta delta gamma'];
        $this->assertEquals($expected, $result);

        $result = optionValues(['value1' => ['delta', ['gamma']]], $options);
        $expected = ['value1' => 'alfa beta delta gamma'];
        $this->assertEquals($expected, $result);

        $result = optionValues([
            'value1' => ['delta', ['gamma', ['ypsilon']]],
        ], $options);
        $expected = ['value1' => 'alfa beta delta gamma ypsilon'];
        $this->assertEquals($expected, $result);

        $result = optionValues([
            'class' => 'my-class',
            'value1' => 'gamma'
        ], $options);
        $expected = [
            'class' => 'my-class',
            'value1' => 'alfa beta gamma',
        ];
        $this->assertEquals($expected, $result);

        $result = optionValues([
            'class' => 'my-class',
            'value1' => ['delta', 'gamma']
        ], $options);
        $expected = [
            'class' => 'my-class', 'value1' => 'alfa beta delta gamma',
        ];
        $this->assertEquals($expected, $result);

        //Backward compatibility with three arguments
        $result = optionValues('value1', 'gamma', $options);
        $expected = ['value1' => 'alfa beta gamma'];
        $this->assertEquals($expected, $result);
    }
}
