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
namespace MeTools\Test\TestCase\Core;

use MeTools\TestSuite\TestCase;
use MeTools\View\OptionsParser;

/**
 * GlobalFunctionsTest class
 */
class GlobalFunctionsTest extends TestCase
{
    /**
     * Test for `optionsParser()` global function
     * @test
     */
    public function testOptionsParser()
    {
        $result = optionsParser();
        $expected = new OptionsParser;
        $this->assertInstanceOf(OptionsParser::class, $result);
        $this->assertEquals($expected, $result);
        $this->assertEquals(get_object_vars($expected), get_object_vars($result));

        $a = ['f' => 'first', 's' => 'second'];
        $b = ['t' => 'third'];

        $result = optionsParser($a, $b);
        $expected = new OptionsParser($a, $b);
        $this->assertInstanceOf(OptionsParser::class, $result);
        $this->assertEquals($expected, $result);
        $this->assertEquals(get_object_vars($expected), get_object_vars($result));
    }
}
