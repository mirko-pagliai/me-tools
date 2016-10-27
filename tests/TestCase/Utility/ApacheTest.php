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
use MeTools\Utility\Apache;

/**
 * ApacheTest class.
 */
class ApacheTest extends TestCase
{
    /**
     * Tests for `module()` method
     * @return void
     * @test
     */
    public function testModule()
    {
        $result = Apache::module('mod_rewrite');
        $this->assertTrue($result);

        $result = Apache::module('mod_noExisting');
        $this->assertFalse($result);
    }

    /**
     * Tests for `version()` method
     * @return void
     * @test
     */
    public function testVersion()
    {
        $result = Apache::version();
        $this->assertEquals('1.3.29', $result);
    }
}
