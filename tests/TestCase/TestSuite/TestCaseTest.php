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

use App\Test\TestCase\View\AppViewTest;
use MeTools\TestSuite\TestCase;
use PHPUnit\Framework\AssertionFailedError;
use Tools\Filesystem;

/**
 * TestCaseTest class
 */
class TestCaseTest extends TestCase
{
    /**
     * @var \MeTools\TestSuite\TestCase&\PHPUnit\Framework\MockObject\MockObject
     */
    protected TestCase $TestCase;

    /**
     * Called before every test method
     * @return void
     */
    public function setUp(): void
    {
        $this->TestCase ??= $this->getMockForAbstractClass(TestCase::class);
    }

    /**
     * @test
     * @uses \MeTools\TestSuite\TestCase::__get()
     */
    public function testGetMagicMethod(): void
    {
        $AppViewTest = new AppViewTest();
        $this->assertSame('App\View\AppView', $AppViewTest->originClassName);
        $this->assertSame('App', $AppViewTest->alias);

        //With a no existing property
        $this->expectException(AssertionFailedError::class);
        $this->expectExceptionMessage('Property `noExistingProperty` does not exist');
        /** @noinspection PhpUndefinedFieldInspection */
        $AppViewTest->noExistingProperty;
    }

    /**
     * @test
     * @uses \MeTools\TestSuite\TestCase::assertLogContains()
     */
    public function testAssertLogContains(): void
    {
        $string = 'cat dog bird';
        $file = LOGS . 'debug.log';
        Filesystem::createFile($file, $string);

        foreach (explode(' ', $string) as $word) {
            $this->TestCase->assertLogContains($word, $file);
        }

        //With a no existing log
        $this->expectAssertionFailed('File or directory `' . $this->TestCase->getLogFullPath('noExisting') . '` is not readable');
        $this->assertLogContains('content', 'noExisting');
    }

    /**
     * @test
     * @uses \MeTools\TestSuite\TestCase::assertSqlEndsNotWith()
     */
    public function testAssertSqlEndsNotWith(): void
    {
        $sql = 'SELECT Posts.id AS Posts__id FROM posts Posts';
        $this->TestCase->assertSqlEndsNotWith('FROM `posts` `Posts` ORDER BY rand() LIMIT 1', $sql);
        $this->TestCase->assertSqlEndsNotWith('FROM posts Posts ORDER BY rand() LIMIT 1', $sql);

        $this->expectAssertionFailed();
        $this->TestCase->assertSqlEndsNotWith('FROM `posts` `Posts`', $sql);
    }

    /**
     * @test
     * @uses \MeTools\TestSuite\TestCase::assertSqlEndsWith()
     */
    public function testAssertSqlEndsWith(): void
    {
        $sql = 'SELECT Posts.id AS Posts__id FROM posts Posts ORDER BY rand() LIMIT 1';
        $this->TestCase->assertSqlEndsWith('FROM `posts` `Posts` ORDER BY rand() LIMIT 1', $sql);
        $this->TestCase->assertSqlEndsWith('FROM posts Posts ORDER BY rand() LIMIT 1', $sql);

        $sql = 'SELECT `Posts`.`id` AS `Posts__id` FROM `posts` `Posts` ORDER BY rand() LIMIT 1';
        $this->TestCase->assertSqlEndsWith('FROM `posts` `Posts` ORDER BY rand() LIMIT 1', $sql);
        $this->TestCase->assertSqlEndsWith('FROM posts Posts ORDER BY rand() LIMIT 1', $sql);

        $this->expectAssertionFailed();
        $this->TestCase->assertSqlEndsWith('FROM `posts` `Posts`', $sql);
    }

    /**
     * @test
     * @uses \MeTools\TestSuite\TestCase::deleteLog()
     */
    public function testDeleteLog(): void
    {
        foreach ([LOGS . 'first.log', LOGS . 'second.log'] as $log) {
            Filesystem::createFile($log);
            $this->TestCase->deleteLog($log);
            $this->assertFileDoesNotExist($log);
        }
    }

    /**
     * @test
     * @uses \MeTools\TestSuite\TestCase::getTable()
     */
    public function testGetTable(): void
    {
        $Table = $this->TestCase->getTable('Articles');
        $this->assertSame('Articles', $Table->getAlias());
    }
}
