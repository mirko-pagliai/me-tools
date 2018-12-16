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

use Cake\ORM\TableRegistry;
use MeTools\TestSuite\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

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
        $expected = 'Pages';

        $this->assertEquals($expected, $this->getControllerAlias('App\Controller\PagesController'));
        $this->assertEquals($expected, $this->getControllerAlias('App\Controller\Admin\PagesController'));
        $this->assertEquals($expected, $this->getControllerAlias('Plugin\Controller\PagesController'));
        $this->assertEquals($expected, $this->getControllerAlias('Plugin\Controller\Admin\PagesController'));
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
    }

    /**
     * Tests for `getMockForComponent()` method, with a no existing class
     * @expectedException \PHPUnit\Framework\AssertionFailedError
     * @test
     */
    public function testGetMockForControllerNoExistingClass()
    {
        $this->getMockForController('App\Controller\NoExistingController');
    }
}
