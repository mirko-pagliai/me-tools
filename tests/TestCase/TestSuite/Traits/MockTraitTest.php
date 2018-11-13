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
namespace MeTools\Test\TestCase\TestSuite\Traits;

use Cake\ORM\TableRegistry;
use MeTools\TestSuite\TestCase;
use MeTools\TestSuite\Traits\MockTrait;

/**
 * MockTraitTest class
 */
class MockTraitTest extends TestCase
{
    use MockTrait;

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
        $this->assertIsMock($Mock);
    }

    /**
     * Tests for `getMockForComponent()` method
     * @test
     */
    public function testGetMockForController()
    {
        $Mock = $this->getMockForController('App\Controller\PagesController', null);
        $this->assertIsMock($Mock);
        $this->assertEquals('Pages', $Mock->getName());

        $Mock = $this->getMockForController('App\Controller\PagesController', null, 'MyController');
        $this->assertIsMock($Mock);
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

    /**
     * Tests for `getMockForTable()` method
     * @test
     */
    public function testGetMockForTable()
    {
        $Mock = $this->getMockForTable('App\Model\Table\PagesTable', null);
        $this->assertIsMock($Mock);
        $this->assertEquals('Pages', $Mock->getAlias());
        $this->assertEquals('Cake\ORM\Entity', $Mock->getEntityClass());
        $this->assertNotEquals('App\Model\Entity\Page', $Mock->getEntityClass());
        $this->assertTrue(TableRegistry::getTableLocator()->exists($Mock->getAlias()));

        $Mock = $this->getMockForTable('App\Model\Table\PostsTable', null);
        $this->assertIsMock($Mock);
        $this->assertEquals('Posts', $Mock->getAlias());
        $this->assertEquals('App\Model\Entity\Post', $Mock->getEntityClass());
        $this->assertTrue(TableRegistry::getTableLocator()->exists($Mock->getAlias()));
    }
}
