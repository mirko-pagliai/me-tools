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

use MeTools\TestSuite\TestCase;
use PHPUnit\Framework\AssertionFailedError;
use PHPUnit\Framework\MockObject\MockObject;
use ReflectionException;

/**
 * MockTraitTest class
 */
class MockTraitTest extends TestCase
{
    /**
     * Tests for `getControllerAlias()` method
     * @test
     */
    public function testGetControllerAlias()
    {
        foreach ([
            'App\Controller\PagesController',
            'App\Controller\Admin\PagesController',
            'TestPlugin\Controller\PagesController',
            'TestPlugin\Controller\Admin\PagesController',
        ] as $class) {
            $this->assertEquals('Pages', $this->getControllerAlias($class));
        }

        $this->expectException(ReflectionException::class);
        $this->getControllerAlias('App\NoExisting\Class');
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
        $Mock = $this->getMockForController('App\Controller\PagesController', null);
        $this->assertInstanceOf(MockObject::class, $Mock);
        $this->assertEquals('Pages', $Mock->getName());

        $Mock = $this->getMockForController('App\Controller\PagesController', null, 'MyController');
        $this->assertInstanceOf(MockObject::class, $Mock);
        $this->assertEquals('MyController', $Mock->getName());

        //With a no existing class
        $this->expectException(AssertionFailedError::class);
        $this->expectExceptionMessage('Class `App\Controller\NoExistingController` does not exist');
        $this->getMockForController('App\Controller\NoExistingController');
    }

    /**
     * Tests for `getOriginClassName()` method
     * @test
     */
    public function testGetOriginClassName()
    {
        $this->assertSame(TestCase::class, $this->getOriginClassName('MeTools\Test\TestCase\TestSuite\TestCaseTest'));
        $this->assertSame(TestCase::class, $this->getOriginClassName('\MeTools\Test\TestCase\TestSuite\TestCaseTest'));
    }

    /**
     * Tests for `getOriginClassNameOrFail()` method
     * @test
     */
    public function testGetOriginClassNameOrFail()
    {
        $this->assertSame(TestCase::class, $this->getOriginClassNameOrFail('MeTools\Test\TestCase\TestSuite\TestCaseTest'));

        $this->expectException(AssertionFailedError::class);
        $this->expectExceptionMessage('Class `\MeTools\TestSuite\NoExistingClass` does not exist');
        $this->getOriginClassNameOrFail('\MeTools\Test\TestCase\TestSuite\NoExistingClassTest');
    }
}
