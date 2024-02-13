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
namespace MeTools\Test\TestCase\Core;

use Cake\Http\ServerRequest;
use MeTools\TestSuite\TestCase;

/**
 * RequestDetectorsTest class
 */
class RequestDetectorsTest extends TestCase
{
    /**
     * @var \Cake\Http\ServerRequest
     */
    public ServerRequest $Request;

    /**
     * @inheritDoc
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->Request ??= new ServerRequest(['params' => [
            'controller' => 'myController',
            'action' => 'myAction',
        ]]);
        $this->Request->clearDetectorCache();
    }

    /**
     * Tests for `is('action')` detector
     * @test
     */
    public function testIsAction(): void
    {
        $this->assertTrue($this->Request->is('action', 'myAction'));
        $this->assertFalse($this->Request->is('action', 'notMyAction'));

        //Multiple actions
        $this->assertTrue($this->Request->is('action', ['myAction', 'notMyAction']));
        $this->assertFalse($this->Request->is('action', ['notMyAction', 'againNotMyAction']));

        //Action + Controller
        $this->assertTrue($this->Request->is('action', 'myAction', 'myController'));
        $this->assertFalse($this->Request->is('action', 'myAction', 'notMyController'));

        //Multiple actions + controller
        $this->assertTrue($this->Request->is('action', ['myAction', 'notMyAction'], 'myController'));
        $this->assertTrue($this->Request->is('action', ['myAction', 'notMyAction'], ['myController', 'notMyController']));
        $this->assertFalse($this->Request->is('action', ['notMyAction', 'againNotMyAction'], 'myController'));
        $this->assertFalse($this->Request->is('action', ['myAction', 'notMyAction'], 'notMyController'));
        $this->assertFalse($this->Request->is('action', ['notMyAction', 'againNotMyAction'], 'notMyController'));
    }

    /**
     * Tests for other "action detectors": `is('add')`, `is('edit')`, `is('view')`, `is('index')`, `is('delete')`
     * @test
     */
    public function testOtherActionDetectors(): void
    {
        $actions = ['add', 'edit', 'view', 'index', 'delete'];
        foreach ($actions as $currentAction) {
            $this->Request = $this->Request->withParam('action', $currentAction);
            $this->Request->clearDetectorCache();
            $this->assertTrue($this->Request->is($currentAction));

            //With right controller
            $this->Request->clearDetectorCache();
            $this->assertTrue($this->Request->is($currentAction, 'myController'));

            //With bad controller
            $this->Request->clearDetectorCache();
            $this->assertFalse($this->Request->is($currentAction, 'notMyController'));

            $otherActions = array_diff($actions, [$currentAction]);
            foreach ($otherActions as $otherAction) {
                $this->Request->clearDetectorCache();
                $this->Request = $this->Request->withParam('action', $otherAction);
                $this->assertFalse($this->Request->is($currentAction));
            }
        }
    }

    /**
     * Tests for `is('controller')` detector
     * @test
     */
    public function testIsController(): void
    {
        $this->assertTrue($this->Request->is('controller', 'myController'));
        $this->assertFalse($this->Request->is('controller', 'notMyController'));

        //Multiple controllers
        $this->assertTrue($this->Request->is('controller', ['myController', 'notMyController']));
        $this->assertFalse($this->Request->is('controller', ['notMyController', 'againNotMyController']));
    }
}
