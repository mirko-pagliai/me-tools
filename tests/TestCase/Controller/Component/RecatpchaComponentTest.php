<?php
/**
 * This file is part of MeTools.
 *
 * MeTools is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * MeTools is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with MeTools.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @author      Mirko Pagliai <mirko.pagliai@gmail.com>
 * @copyright   Copyright (c) 2016, Mirko Pagliai for Nova Atlantis Ltd
 * @license     http://www.gnu.org/licenses/agpl.txt AGPL License
 * @link        http://git.novatlantis.it Nova Atlantis Ltd
 */
namespace MeTools\Test\TestCase\Controller\Component;

use Cake\Controller\ComponentRegistry;
use Cake\Controller\Controller;
use Cake\Core\Configure;
use Cake\Network\Request;
use Cake\TestSuite\TestCase;
use MeTools\Controller\Component\RecaptchaComponent;
use Reflection\ReflectionTrait;

/**
 * RecatpchaComponentTest class
 */
class RecatpchaComponentTest extends TestCase
{
    use ReflectionTrait;

    /**
     * @var \Cake\Controller\ComponentRegistry
     */
    protected $ComponentRegistry;

    /**
     * @var \Cake\Controller\Controller
     */
    protected $Controller;

    /**
     * @var \MeTools\Controller\Component\RecaptchaComponent
     */
    protected $Recaptcha;

    /**
     * Setup the test case, backup the static object values so they can be
     * restored. Specifically backs up the contents of Configure and paths in
     *  App if they have not already been backed up
     * @return void
     */
    public function setUp()
    {
        parent::setUp();

        $this->Controller = new Controller(new Request());
        $this->ComponentRegistry = new ComponentRegistry($this->Controller);
        $this->Recaptcha = new RecaptchaComponent($this->ComponentRegistry);
    }

    /**
     * Teardown any static object changes and restore them
     * @return void
     */
    public function tearDown()
    {
        parent::tearDown();

        unset($this->Recaptcha, $this->ComponentRegistry, $this->Controller);
    }

    /**
     * Tests for `check()` method
     * @test
     */
    public function testCheck()
    {
        $this->assertFalse($this->Recaptcha->check());
        $this->assertEquals('You have not filled out the reCAPTCHA control', $this->Recaptcha->getError());

        $this->Controller->request->data['g-recaptcha-response'] = true;
        $this->assertFalse($this->Recaptcha->check());
        $this->assertEquals('It was not possible to verify the reCAPTCHA control', $this->Recaptcha->getError());

        $this->Recaptcha = $this->getMockBuilder(RecaptchaComponent::class)
            ->setConstructorArgs([$this->ComponentRegistry])
            ->setMethods(['_getResult'])
            ->getMock();

        $this->Recaptcha->method('_getResult')
            ->will($this->returnCallback(function () {
                return (object)['json' => ['success' => true]];
            }));

        $this->assertTrue($this->Recaptcha->check());
    }

    /**
     * Tests for `check()` method, with no private key
     * @expectedException Cake\Network\Exception\InternalErrorException
     * @expectedExceptionMessage Form keys are not configured
     * @test
     */
    public function testCheckNoPrivateKey()
    {
        //Deletes keys
        Configure::delete('Recaptcha.Form.private');

        $this->Recaptcha->check();
    }

    /**
     * Tests for `check()` method, with no public key
     * @expectedException Cake\Network\Exception\InternalErrorException
     * @expectedExceptionMessage Form keys are not configured
     * @test
     */
    public function testCheckNoPublicKey()
    {
        //Deletes keys
        Configure::delete('Recaptcha.Form.public');

        $this->Recaptcha->check();
    }

    /**
     * Tests for `getError()` method
     * @test
     */
    public function testGetError()
    {
        $this->assertFalse($this->Recaptcha->getError());

        $error = 'this is an error';

        //Sets the `error` property
        $this->setProperty($this->Recaptcha, 'error', $error);

        $this->assertEquals($error, $this->Recaptcha->getError());
    }
}
