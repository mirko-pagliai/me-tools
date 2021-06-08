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

use Cake\ORM\Table;
use MeTools\TestSuite\TestCase;
use PHPUnit\Framework\AssertionFailedError;
use Tools\Filesystem;
use Tools\ReflectionTrait;

/**
 * TestCaseTest class
 */
class TestCaseTest extends TestCase
{
    use ReflectionTrait;

    /**
     * Tests for `getLogFullPath()` method
     * @test
     */
    public function testGetLogFullPath(): void
    {
        $expected = LOGS . 'debug.log';

        foreach (['debug', 'debug.log', LOGS . 'debug', $expected] as $filename) {
            $this->assertEquals($expected, $this->invokeMethod($this, 'getLogFullPath', [$filename]));
        }
    }

    /**
     * Tests for `getTable()` method
     * @test
     */
    public function testGetTable(): void
    {
        $table = $this->getTable('Articles');
        $this->assertSame('Articles', $table->getAlias());
        $this->assertInstanceOf(Table::class, $table);

        //With a no-existing table
        $this->assertNull($this->getTable('NoExistingTable', ['className' => '\Cake\ORM\NoExistingTable']));
    }

    /**
     * Tests for `assertLogContains()` method
     * @test
     */
    public function testAssertLogContains(): void
    {
        $string = 'cat dog bird';
        $file = LOGS . 'debug.log';
        Filesystem::instance()->createFile($file, $string);

        foreach (explode(' ', $string) as $word) {
            $this->assertLogContains($word, $file);
        }

        //With a no existing log
        $this->expectException(AssertionFailedError::class);
        $this->expectExceptionMessage('File or directory `' . $this->getLogFullPath('noExisting') . '` does not exist');
        $this->assertLogContains('content', 'noExisting');
    }

    /**
     * Tests for `deleteLog()` method
     * @test
     */
    public function testDeleteLog(): void
    {
        $logs = [LOGS . 'first.log', LOGS . 'second.log'];
        array_map([Filesystem::instance(), 'createFile'], $logs);
        array_map([$this, 'deleteLog'], ['first', 'second']);
        array_map([$this, 'assertFileDoesNotExist'], $logs);
    }
}
