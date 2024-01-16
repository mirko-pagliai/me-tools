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

use AnotherTestPlugin\Test\TestCase\Controller\MyExampleControllerTest;
use App\Test\TestCase\BadTestClass;
use App\Test\TestCase\Controller\PagesControllerTest;
use App\Test\TestCase\Model\Table\PostsTableTest;
use App\Test\TestCase\Model\Validation\PostValidatorTest;
use App\Test\TestCase\View\AppViewTest;
use MeTools\Test\TestCase\View\Helper\HtmlHelperTest;
use MeTools\TestSuite\TestCase;
use PHPUnit\Framework\AssertionFailedError;
use PHPUnit\Framework\ExpectationFailedException;
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
     * @inheritDoc
     */
    public function setUp(): void
    {
        $this->TestCase ??= new class ('MyTest') extends TestCase
        {
        };
    }

    /**
     * @test
     * @uses \MeTools\TestSuite\TestCase::__get()
     */
    public function testGetMagicMethod(): void
    {
        $AppViewTest = new AppViewTest('AppViewTest');
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
        Filesystem::createFile(LOGS . 'debug.log', $string);
        foreach (explode(' ', $string) as $word) {
            $this->TestCase->assertLogContains($word, LOGS . 'debug.log');
        }

        $this->expectException(ExpectationFailedException::class);
        $this->expectExceptionMessageMatches('/^Failed asserting that \'cat dog bird\'( \[ASCII\]\(length: 12\))? contains "bad word"( \[ASCII\]\(length: 8\))?\.$/');
        $this->TestCase->assertLogContains('bad word', LOGS . 'debug.log');
    }

    /**
     * @test
     * @uses \MeTools\TestSuite\TestCase::assertLogContains()
     */
    public function testAssertLogContainsWithNoExistingLog(): void
    {
        $this->expectException(AssertionFailedError::class);
        $this->expectExceptionMessage('Failed asserting that file "' . LOGS . 'noExisting.log" exists');
        $this->assertLogContains('content', LOGS . 'noExisting.log');
    }

    /**
     * @test
     * @uses \MeTools\TestSuite\TestCase::getAlias()
     */
    public function testGetAlias(): void
    {
        $this->assertSame('Pages', $this->TestCase->getAlias(new PagesControllerTest('PagesControllerTest')));
        $this->assertSame('Html', $this->TestCase->getAlias(new HtmlHelperTest('HtmlHelperTest')));
        $this->assertSame('Posts', $this->TestCase->getAlias(new PostsTableTest('PostsTableTest')));
        $this->assertSame('Post', $this->TestCase->getAlias(new PostValidatorTest('PostValidatorTest')));

        $this->expectException(AssertionFailedError::class);
        $this->expectExceptionMessage('Unable to get the alias for `App\Test\TestCase\BadTestClass`');
        $this->TestCase->getAlias(new BadTestClass('BadTest'));
    }

    /**
     * @test
     * @uses \MeTools\TestSuite\TestCase::getOriginClassName()
     */
    public function testGetOriginClassName(): void
    {
        $this->assertSame(TestCase::class, $this->TestCase->getOriginClassName(new TestCaseTest('MyTest')));

        $this->expectException(AssertionFailedError::class);
        $this->expectExceptionMessage('Unable to determine the origin class for `App\Test\TestCase\BadTestClass`');
        $this->TestCase->getOriginClassName(new BadTestClass('BadTest'));
    }

    /**
     * @test
     * @uses \MeTools\TestSuite\TestCase::getOriginClassName()
     */
    public function testGetOriginClassNameWithNoExistingClass(): void
    {
        $this->expectException(AssertionFailedError::class);
        $this->expectExceptionMessage('Class `AnotherTestPlugin\Controller\MyExampleController` does not exist');
        $this->TestCase->getOriginClassName(new MyExampleControllerTest('MyExampleControllerTest'));
    }
}
