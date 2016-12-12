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
use MeTools\Utility\ReflectionTrait;

class ExampleClass {
    protected $firstProperty;

    protected $secondProperty = 'a protected property';

    protected function protectedMethod($var = null)
    {
        if (empty($var)) {
            return 'a protected method';
        }

        return $var;
    }

    public function __get($name) {
        return $this->$name;
    }
}

/**
 * MeTools\Utility\ReflectionTrait Test Case
 */
class ReflectionTraitTest extends TestCase
{
    use ReflectionTrait;

    /**
     * Tests for `invokeMethod()` method
     * @test
     */
    public function testInvokeMethod()
    {
        $example = new ExampleClass();

        $this->assertEquals('a protected method', $this->invokeMethod($example, 'protectedMethod'));
        $this->assertEquals('example string', $this->invokeMethod($example, 'protectedMethod', ['example string']));
    }

    /**
     * Tests for `getProperty()` method
     * @test
     */
    public function testGetProperty()
    {
        $example = new ExampleClass();

        $this->assertNull($this->getProperty($example, 'firstProperty'));
        $this->assertEquals('a protected property', $this->getProperty($example, 'secondProperty'));
    }

    /**
     * Tests for `getProperty()` method
     * @test
     */
    public function testSetProperty()
    {
        $example = new ExampleClass();

        $this->setProperty($example, 'firstProperty', 'example string');
        $this->assertEquals('example string', $example->firstProperty);

        $this->setProperty($example, 'secondProperty', null);
        $this->assertNull($example->secondProperty);
    }
}
