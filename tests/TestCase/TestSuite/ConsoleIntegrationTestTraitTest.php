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
namespace MeTools\Test\TestCase\TestSuite;

use Cake\TestSuite\Stub\ConsoleOutput;
use MeTools\TestSuite\ConsoleIntegrationTestTrait;
use MeTools\TestSuite\TestCase;

/**
 * ConsoleIntegrationTestTraitTest class
 */
class ConsoleIntegrationTestTraitTest extends TestCase
{
    use ConsoleIntegrationTestTrait;

    /**
     * @var Cake\TestSuite\Stub\ConsoleOutput
     */
    protected $_out;

    /**
     * Called before every test method
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->_out = new ConsoleOutput();
    }

    /**
     * Test for `assertOutputNotEmpty()` method
     * @test
     */
    public function testAssertOutputNotEmpty()
    {
        $this->_out->write('message');
        $this->assertOutputNotEmpty();
    }

    /**
     * Test for `assertOutputNotEmpty()` method, on failure
     * @expectedException PHPUnit\Framework\ExpectationFailedException
     * @expectedExceptionMessage stdout was empty
     * @test
     */
    public function testAssertOutputNotEmptyOnFailure()
    {
        $this->assertOutputNotEmpty();
    }
}
