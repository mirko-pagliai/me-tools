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
use PHPUnit\Framework\ExpectationFailedException;

/**
 * ConsoleIntegrationTestTraitTest class
 */
class ConsoleIntegrationTestTraitTest extends TestCase
{
    use ConsoleIntegrationTestTrait;

    /**
     * @var \Cake\TestSuite\Stub\ConsoleOutput
     */
    protected $_out;

    /**
     * Test for `assertOutputNotEmpty()` method
     * @test
     */
    public function testAssertOutputNotEmpty()
    {
        $this->_out = new ConsoleOutput();
        $this->_out->write('message');
        $this->assertOutputNotEmpty();

        //On failure
        $this->expectException(ExpectationFailedException::class);
        $this->expectExceptionMessage('stdout was empty');
        $this->_out = new ConsoleOutput();
        $this->assertOutputNotEmpty();
    }
}
