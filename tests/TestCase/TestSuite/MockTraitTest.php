<?php
/** @noinspection PhpUnhandledExceptionInspection */
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
use Cake\View\Helper;
use MeTools\Test\TestCase\View\Helper\HtmlHelperTest;
use MeTools\TestSuite\TestCase;
use PHPUnit\Framework\AssertionFailedError;

/**
 * MockTraitTest class
 */
class MockTraitTest extends TestCase
{
    /**
     * @test
     * @uses \MeTools\TestSuite\MockTrait::getAlias()
     */
    public function testGetAlias(): void
    {
        $this->assertSame('Pages', $this->getAlias(new PagesControllerTest()));
        $this->assertSame('Html', $this->getAlias(new HtmlHelperTest()));
        $this->assertSame('Posts', $this->getAlias(new PostsTableTest()));
        $this->assertSame('Post', $this->getAlias(new PostValidatorTest()));

        $this->expectException(AssertionFailedError::class);
        $this->expectExceptionMessage('Unable to get the alias for `App\Test\TestCase\BadTestClass`');
        $this->getAlias(new BadTestClass());
    }

    /**
     * @test
     * @uses \MeTools\TestSuite\MockTrait::getMockForHelper()
     */
    public function testGetMockForHelper(): void
    {
        $result = $this->getMockForHelper('Cake\View\Helper\HtmlHelper');
        $this->assertIsMock($result);
        $this->assertInstanceOf(Helper::class, $result);
    }

    /**
     * @test
     * @uses \MeTools\TestSuite\MockTrait::getOriginClassName()
     */
    public function testGetOriginClassName(): void
    {
        $this->assertSame(TestCase::class, $this->getOriginClassName(new TestCaseTest()));

        $this->expectException(AssertionFailedError::class);
        $this->expectExceptionMessage('Unable to determine the origin class for `App\Test\TestCase\BadTestClass`');
        $this->getOriginClassName(new BadTestClass());
    }

    /**
     * @test
     * @uses \MeTools\TestSuite\MockTrait::getOriginClassName()
     */
    public function testGetOriginClassNameWithNoExistingClass(): void
    {
        $this->expectException(AssertionFailedError::class);
        $this->expectExceptionMessage('Class `AnotherTestPlugin\Controller\MyExampleController` does not exist');
        $this->getOriginClassName(new MyExampleControllerTest());
    }

    /**
     * @test
     * @uses \MeTools\TestSuite\MockTrait::getPluginName()
     */
    public function testGetPluginName(): void
    {
        $this->assertSame('MeTools', $this->getPluginName(new TestCaseTest()));
        $this->assertSame('AnotherTestPlugin', $this->getPluginName(new MyExampleControllerTest()));
    }

    /**
     * @test
     * @uses \MeTools\TestSuite\MockTrait::getTableClassNameFromAlias()
     */
    public function testGetTableClassNameFromAlias(): void
    {
        $this->assertSame('MeTools\Model\Table\PostsTable', $this->getTableClassNameFromAlias('Posts'));
        $this->assertSame('MyPlugin\SubNamespace\Model\Table\PostsTable', $this->getTableClassNameFromAlias('Posts', 'MyPlugin/SubNamespace'));
    }
}
