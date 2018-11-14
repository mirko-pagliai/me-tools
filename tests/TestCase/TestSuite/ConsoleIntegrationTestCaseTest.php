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
use MeTools\TestSuite\ConsoleIntegrationTestCase;

/**
 * ConsoleIntegrationTestCaseTest class
 */
class ConsoleIntegrationTestCaseTest extends ConsoleIntegrationTestCase
{
    /**
     * Called before every test method
     * @return void
     */
    public function setUp()
    {
        $this->Shell = $this->getMockForShell(ExampleShell::class);

        parent::setUp();
    }

    /**
     * Test for `assertTableHeadersEquals()` method
     * @test
     */
    public function testAssertTableHeadersEquals()
    {
        $this->exec('example print_table');
        $this->assertTableHeadersEquals($this->Shell::$tableHeaders);
    }

    /**
     * Test for `assertTableRowsEquals()` method
     * @test
     */
    public function testAssertTableRowsEquals()
    {
        $this->exec('example print_table');
        $this->assertTableRowsEquals($this->Shell::$tableRows);
    }

    /**
     * Test for `getShellMethods()` method
     * @test
     */
    public function testGetShellMethods()
    {
        $this->assertEquals(['doNothing', 'printTable'], $this->getShellMethods());
        $this->assertEquals(['printTable'], $this->getShellMethods(['doNothing']));

        $this->Shell = $this->getMockForShell(ChildExampleShell::class);
        $this->assertEquals(['childMethod', 'doNothing', 'printTable'], $this->getShellMethods());
    }
}
