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
