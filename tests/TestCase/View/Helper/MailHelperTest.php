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
namespace MeTools\Test\TestCase\View\Helper;

use Cake\TestSuite\TestCase;
use Cake\View\View;
use MeTools\View\Helper\MailHelper;

/**
 * MailHelperTest class
 */
class MailHelperTest extends TestCase
{
    /**
     * @var \MeTools\View\Helper\MailHelper
     */
    protected $Mail;

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
        $this->Mail = new MailHelper($this->View);
    }

    /**
     * Teardown any static object changes and restore them
     * @return void
     */
    public function tearDown()
    {
        parent::tearDown();

        unset($this->Mail, $this->View);
    }

    /**
     * Test for `obfuscate()` method
     * @return void
     * @test
     */
    public function testObfuscate()
    {
        $result = $this->Mail->obfuscate('myname@mymail.com');
        $expected = 'myn***@mymail.com';
        $this->assertEquals($expected, $result);

        $result = $this->Mail->obfuscate('firstnameandlastname@example.it');
        $expected = 'firstnamea**********@example.it';
        $this->assertEquals($expected, $result);

        $result = $this->Mail->obfuscate('invalidmail');
        $expected = 'inval*****';
        $this->assertEquals($expected, $result);

        $result = $this->Mail->obfuscate('@invalidmail');
        $expected = '@invalidmail';
        $this->assertEquals($expected, $result);
    }
}
