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
use App\Controller\PagesController;
use App\Model\Table\PostsTable;
use App\Model\Validation\PostValidator;
use App\View\Cell\MyExampleCell;
use Cake\Controller\Component;
use Cake\Controller\Controller;
use Cake\View\Helper;
use MeTools\TestSuite\TestCase;
use PHPUnit\Framework\AssertionFailedError;
use stdClass;

/**
 * MockTraitTest class
 */
class MockTraitTest extends TestCase
{
    /**
     * Tests for `getAlias()` method
     * @test
     */
    public function testGetAlias(): void
    {
        $this->assertSame('Pages', $this->getAlias(new PagesController()));
        $this->assertSame('Posts', $this->getAlias(PostsTable::class));
        $this->assertSame('Post', $this->getAlias(PostValidator::class));
        $this->assertSame('MyExample', $this->getAlias(MyExampleCell::class));
        $this->assertSame('MyExample', $this->getAlias(new MyExampleControllerTest()));

        //Class with no alias or no existing class
        foreach ([
            stdClass::class => 'Unable to get the alias for the `stdClass` class',
            'No\Existing\Class' => 'Class `No\Existing\Class` does not exist',
        ] as $className => $expectedMessage) {
            /** @phpstan-ignore-next-line */
            $this->assertException(fn() => $this->getAlias($className), AssertionFailedError::class, $expectedMessage);
        }
    }

    /**
     * Tests for `getMockForComponent()` method
     * @test
     * @uses \MeTools\TestSuite\MockTrait::getMockForComponent()
     */
    public function testGetMockForComponent(): void
    {
        $Component = $this->getMockForComponent('Cake\Controller\Component\FlashComponent');
        $this->assertIsMock($Component);
        $this->assertInstanceOf(Component::class, $Component);
    }

    /**
     * Tests for `getMockForComponent()` method
     * @test
     * @uses \MeTools\TestSuite\MockTrait::getMockForController()
     */
    public function testGetMockForController(): void
    {
        /** @var \App\Controller\PagesController $Controller * */
        $Controller = $this->getMockForController('App\Controller\PagesController', []);
        $this->assertIsMock($Controller);
        $this->assertInstanceOf(Controller::class, $Controller);
        $this->assertEquals('Pages', $Controller->getName());

        /** @var \App\Controller\PagesController $Controller * */
        $Controller = $this->getMockForController('App\Controller\PagesController', [], 'MyController');
        $this->assertIsMock($Controller);
        $this->assertInstanceOf(Controller::class, $Controller);
        $this->assertEquals('MyController', $Controller->getName());

        //With a no existing class
        $this->expectAssertionFailed('Class `App\Controller\NoExistingController` does not exist');
        /** @phpstan-ignore-next-line */
        $this->getMockForController('App\Controller\NoExistingController');
    }

    /**
     * Tests for `getMockForHelper()` method
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
     * Tests for `getOriginClassName()` method
     * @test
     */
    public function testGetOriginClassName(): void
    {
        $this->assertSame(TestCase::class, $this->getOriginClassName(new TestCaseTest()));
    }

    /**
     * Tests for `getOriginClassNameOrFail()` method
     * @test
     */
    public function testGetOriginClassNameOrFail(): void
    {
        $this->assertSame(TestCase::class, $this->getOriginClassNameOrFail(new TestCaseTest()));

        $this->expectAssertionFailed('Class `AnotherTestPlugin\Controller\MyExampleController` does not exist');
        $this->getOriginClassNameOrFail(new MyExampleControllerTest());
    }

    /**
     * Tests for `getPluginName()` method
     * @test
     */
    public function testGetPluginName(): void
    {
        $this->assertSame('MeTools', $this->getPluginName(new TestCaseTest()));
        $this->assertSame('AnotherTestPlugin', $this->getPluginName(new MyExampleControllerTest()));
    }

    /**
     * Tests for `getTableClassNameFromAlias()` method
     * @test
     */
    public function testGetTableClassNameFromAlias(): void
    {
        $this->assertSame('MeTools\Model\Table\PostsTable', $this->getTableClassNameFromAlias('Posts'));
        $this->assertSame('MyPlugin\SubNamespace\Model\Table\PostsTable', $this->getTableClassNameFromAlias('Posts', 'MyPlugin/SubNamespace'));
    }
}
