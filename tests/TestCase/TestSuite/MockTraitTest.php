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

use AnotherTestPlugin\MyPlugin\Test\TestCase\Controller\MyExampleControllerTest;
use App\Controller\PagesController;
use App\Model\Table\PostsTable;
use App\Model\Validation\PostValidator;
use App\View\Cell\MyExampleCell;
use MeTools\TestSuite\TestCase;
use PHPUnit\Framework\AssertionFailedError;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * MockTraitTest class
 */
class MockTraitTest extends TestCase
{
    /**
     * Tests for `getAlias()` method
     * @test
     */
    public function testGetAlias()
    {
        $this->assertSame('Pages', $this->getAlias(new PagesController()));
        $this->assertSame('Posts', $this->getAlias(PostsTable::class));
        $this->assertSame('Post', $this->getAlias(PostValidator::class));
        $this->assertSame('MyExample', $this->getAlias(MyExampleCell::class));
        $this->assertSame('MyExample', $this->getAlias(new MyExampleControllerTest()));

        //Class with no alias
        $this->assertException(function () {
            $this->getAlias(\stdClass::class);
        }, AssertionFailedError::class, 'Unable to get the alias for the `stdClass` class');

        //No existing class
        $this->expectException(AssertionFailedError::class);
        $this->expectExceptionMessage('Class `No\Existing\Class` does not exist');
        /** @phpstan-ignore-next-line */
        $this->getAlias('No\Existing\Class');
    }

    /**
     * Tests for `getControllerAlias()` method
     * @test
     */
    public function testGetControllerAlias()
    {
        $current = error_reporting(E_ALL & ~E_USER_DEPRECATED);
        foreach ([
            'App\Controller\PagesController',
            'App\Controller\Admin\PagesController',
            'TestPlugin\Controller\PagesController',
            'TestPlugin\Controller\Admin\PagesController',
        ] as $class) {
            $this->assertEquals('Pages', $this->getControllerAlias($class));
        }
        error_reporting($current);

        $this->expectDeprecation();
        $this->expectExceptionMessageMatches('/^Deprecated\. Use instead `getAlias\(\)`/');
        $this->getControllerAlias('App\Controller\PagesController');
    }

    /**
     * Tests for `getMockForComponent()` method
     * @test
     */
    public function testGetMockForComponent()
    {
        $Mock = $this->getMockForComponent('Cake\Controller\Component\FlashComponent', null);
        $this->assertInstanceOf(MockObject::class, $Mock);
    }

    /**
     * Tests for `getMockForComponent()` method
     * @test
     */
    public function testGetMockForController()
    {
        /** @var \App\Controller\PagesController $Mock **/
        $Mock = $this->getMockForController('App\Controller\PagesController', null);
        $this->assertInstanceOf(MockObject::class, $Mock);
        $this->assertEquals('Pages', $Mock->getName());

        /** @var \App\Controller\PagesController $Mock **/
        $Mock = $this->getMockForController('App\Controller\PagesController', null, 'MyController');
        $this->assertInstanceOf(MockObject::class, $Mock);
        $this->assertEquals('MyController', $Mock->getName());

        //With a no existing class
        $this->expectException(AssertionFailedError::class);
        $this->expectExceptionMessage('Class `App\Controller\NoExistingController` does not exist');
        /** @phpstan-ignore-next-line */
        $this->getMockForController('App\Controller\NoExistingController');
    }

    /**
     * Tests for `getOriginClassName()` method
     * @test
     */
    public function testGetOriginClassName()
    {
        $this->assertSame(TestCase::class, $this->getOriginClassName(new TestCaseTest()));
    }

    /**
     * Tests for `getOriginClassNameOrFail()` method
     * @test
     */
    public function testGetOriginClassNameOrFail()
    {
        $this->assertSame(TestCase::class, $this->getOriginClassNameOrFail(new TestCaseTest()));

        $this->expectException(AssertionFailedError::class);
        $this->expectExceptionMessage('Class `AnotherTestPlugin\MyPlugin\Controller\MyExampleController` does not exist');
        $this->getOriginClassNameOrFail(new MyExampleControllerTest());
    }

    /**
     * Tests for `getPluginName()` method
     * @test
     */
    public function testGetPluginName()
    {
        $this->assertSame('MeTools', $this->getPluginName(new TestCaseTest()));
        $this->assertSame('AnotherTestPlugin/MyPlugin', $this->getPluginName(new MyExampleControllerTest()));
    }

    /**
     * Tests for `getTableClassNameFromAlias()` method
     * @test
     */
    public function testGetTableClassNameFromAlias()
    {
        $this->assertSame('MeTools\Model\Table\PostsTable', $this->getTableClassNameFromAlias('Posts'));
        $this->assertSame('MyPlugin\SubNamespace\Model\Table\PostsTable', $this->getTableClassNameFromAlias('Posts', 'MyPlugin/SubNamespace'));
    }
}
