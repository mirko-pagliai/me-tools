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
use Tools\Filesystem;
use Tools\TestSuite\ReflectionTrait;

/**
 * TestCaseTest class
 */
class TestCaseTest extends TestCase
{
    use ReflectionTrait;

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
        $this->expectAssertionFailed('File or directory `' . $this->getLogFullPath('noExisting') . '` is not readable');
        $this->assertLogContains('content', 'noExisting');
    }

    /**
     * Tests for `assertSqlEndsNotWith()` method
     * @test
     */
    public function testAssertSqlEndsNotWith(): void
    {
        $sql = 'SELECT Posts.id AS Posts__id FROM posts Posts';
        $this->assertSqlEndsNotWith('FROM `posts` `Posts` ORDER BY rand() LIMIT 1', $sql);
        $this->assertSqlEndsNotWith('FROM posts Posts ORDER BY rand() LIMIT 1', $sql);

        $this->expectAssertionFailed();
        $this->assertSqlEndsNotWith('FROM `posts` `Posts`', $sql);
    }

    /**
     * Tests for `assertSqlEndsWith()` method
     * @test
     */
    public function testAssertSqlEndsWith(): void
    {
        $sql = 'SELECT Posts.id AS Posts__id FROM posts Posts ORDER BY rand() LIMIT 1';
        $this->assertSqlEndsWith('FROM `posts` `Posts` ORDER BY rand() LIMIT 1', $sql);
        $this->assertSqlEndsWith('FROM posts Posts ORDER BY rand() LIMIT 1', $sql);

        $sql = 'SELECT `Posts`.`id` AS `Posts__id` FROM `posts` `Posts` ORDER BY rand() LIMIT 1';
        $this->assertSqlEndsWith('FROM `posts` `Posts` ORDER BY rand() LIMIT 1', $sql);
        $this->assertSqlEndsWith('FROM posts Posts ORDER BY rand() LIMIT 1', $sql);

        $this->expectAssertionFailed();
        $this->assertSqlEndsWith('FROM `posts` `Posts`', $sql);
    }

    /**
     * Tests for `deleteLog()` method
     * @test
     */
    public function testDeleteLog(): void
    {
        foreach ([LOGS . 'first.log', LOGS . 'second.log'] as $log) {
            Filesystem::instance()->createFile($log);
            $this->deleteLog($log);
            $this->assertFileDoesNotExist($log);
        }
    }

    /**
     * Tests for `getTable()` method
     * @test
     */
    public function testGetTable(): void
    {
        /** @var \Cake\ORM\Table $Table */
        $Table = $this->getTable('Articles');
        $this->assertSame('Articles', $Table->getAlias());
        $this->assertInstanceOf(Table::class, $Table);

        //With a no-existing table
        $this->assertNull($this->getTable('NoExistingTable', ['className' => '\Cake\ORM\NoExistingTable']));
    }
}
