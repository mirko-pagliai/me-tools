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
namespace MeTools\Test\TestCase\TestSuite;

use App\Shell\ChildExampleShell;
use App\Shell\ExampleShell;
use Cake\TestSuite\Stub\ConsoleOutput;
use MeTools\TestSuite\ConsoleIntegrationTestCase;

/**
 * ConsoleIntegrationTestCaseTest class
 */
class ConsoleIntegrationTestCaseTest extends ConsoleIntegrationTestCase
{
    /**
     * @var Cake\TestSuite\Stub\ConsoleOutput
     */
    protected $_out;

    /**
     * Called before every test method
     * @return void
     */
    public function setUp()
    {
        $this->Shell = $this->getMockForShell(ExampleShell::class);
        $this->_out = new ConsoleOutput;

        parent::setUp();
    }

    /**
     * Test for `getShellMethods()` method
     * @test
     */
    public function testGetShellMethods()
    {
        $this->assertEquals(['aSimpleMethod', 'doNothing'], $this->getShellMethods());
        $this->assertEquals(['aSimpleMethod'], $this->getShellMethods(['doNothing']));

        $this->Shell = $this->getMockForShell(ChildExampleShell::class);
        $this->assertEquals(['aSimpleMethod', 'childMethod', 'doNothing'], $this->getShellMethods());
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
