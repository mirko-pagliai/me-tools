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
     * @var \MeTools\TestSuite\TestCase
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
        foreach ([
            'Pages' => PagesControllerTest::class,
            'Html' => HtmlHelperTest::class,
            'Posts' => PostsTableTest::class,
            'Post' => PostValidatorTest::class,
         ] as $expectedAlias => $testClassName) {
            /** @var \MeTools\TestSuite\TestCase $TestClass */
            $TestClass = new $testClassName($expectedAlias . 'Test');
            $this->assertSame($expectedAlias, $TestClass->getAlias());
        }

        $this->expectException(AssertionFailedError::class);
        $this->expectExceptionMessage('Unable to get the alias for `App\Test\TestCase\BadTestClass`');
        $TestClass = new BadTestClass('BadTest');
        $TestClass->getAlias();
    }

    /**
     * @test
     * @uses \MeTools\TestSuite\TestCase::getOriginClassName()
     */
    public function testGetOriginClassName(): void
    {
        $TestClass = new TestCaseTest('MyTest');
        $this->assertSame(TestCase::class, $TestClass->getOriginClassName());

        $this->expectException(AssertionFailedError::class);
        $this->expectExceptionMessage('Unable to determine the origin class for `App\Test\TestCase\BadTestClass`');
        $TestClass = new BadTestClass('BadTest');
        $TestClass->getOriginClassName();
    }

    /**
     * @test
     * @uses \MeTools\TestSuite\TestCase::getOriginClassName()
     */
    public function testGetOriginClassNameWithNoExistingClass(): void
    {
        $this->expectException(AssertionFailedError::class);
        $this->expectExceptionMessage('Class `AnotherTestPlugin\Controller\MyExampleController` does not exist');
        $TestClass = new MyExampleControllerTest('MyExampleControllerTest');
        $TestClass->getOriginClassName();
    }
}
