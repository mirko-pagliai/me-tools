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
namespace MeTools\Test\TestCase\View\Helper;

use Cake\Core\Configure;
use Cake\TestSuite\TestCase;
use Cake\View\View;
use MeTools\View\Helper\RecaptchaHelper;

/**
 * RecaptchaHelperTest class
 */
class RecaptchaHelperTest extends TestCase
{
    /**
     * Setup the test case, backup the static object values so they can be
     * restored. Specifically backs up the contents of Configure and paths in
     *  App if they have not already been backed up
     * @return void
     */
    public function setUp()
    {
        parent::setUp();

        $this->View = new View();
        $this->Recaptcha = new RecaptchaHelper($this->View);
    }

    /**
     * Teardown any static object changes and restore them
     * @return void
     */
    public function tearDown()
    {
        parent::tearDown();

        unset($this->Recaptcha, $this->View);
    }

    /**
     * Test for `display()` method
     * @return void
     * @test
     */
    public function testDisplay()
    {
        $expected = [
            'div' => [
                'data-sitekey' => '0000000000000000000000000000000',
                'class' => 'g-recaptcha',
            ],
            '/div',
        ];

        $result = $this->Recaptcha->display();
        $this->assertHtml($expected, $result);

        //Alias
        $result = $this->Recaptcha->recaptcha();
        $this->assertHtml($expected, $result);
    }

    /**
     * Test for `display()` method, with no keys
     * @expectedException Cake\Network\Exception\InternalErrorException
     * @expectedExceptionMessage Form keys are not configured
     */
    public function testDisplayNoKeys()
    {
        //Deletes keys
        Configure::delete('Recaptcha.Form');

        $this->Recaptcha->display();
    }

    /**
     * Test for `mail()` method
     * @return void
     * @test
     */
    public function testMail()
    {
        $title = 'This is a title';

        $result = $this->Recaptcha->mail('myname@mymail.com');
        $expected = [
            'a' => [
                'href',
                'target' => '_blank',
                'class' => 'recaptcha-mail',
                'title' => 'myn***@mymail.com',
            ],
            'myn***@mymail.com',
            '/a',
        ];
        $this->assertHtml($expected, $result);

        $result = $this->Recaptcha->mail($title, 'myname@mymail.com');
        $expected = [
            'a' => [
                'href',
                'target' => '_blank',
                'class' => 'recaptcha-mail',
                'title' => $title,
            ],
            $title,
            '/a',
        ];
        $this->assertHtml($expected, $result);
    }

    /**
     * Test for `mailUrl()` method
     * @return void
     * @test
     */
    public function testMailUrl()
    {
        $result = $this->Recaptcha->mailUrl('myname@mymail.com');
        $expected = (bool)preg_match(
            sprintf(
                '/^%s/',
                preg_quote('http://www.google.com/recaptcha/mailhide/', '/')
            ),
            $result
        );
        $this->assertTrue($expected);
    }

    /**
     * Test for `mailUrl()` method, with no keys
     * @expectedException Cake\Network\Exception\InternalErrorException
     * @expectedExceptionMessage Mail keys are not configured
     */
    public function testMailUrlNoKeys()
    {
        //Deletes keys
        Configure::delete('Recaptcha.Mail');

        $this->Recaptcha->mailUrl('myname@mymail.com');
    }
}
