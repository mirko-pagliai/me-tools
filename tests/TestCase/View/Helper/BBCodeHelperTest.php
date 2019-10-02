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

use Cake\View\View;
use MeTools\Test\TestCase\Utility\BBCodeTest;
use MeTools\View\Helper\BBCodeHelper;
use PHPUnit\Framework\Error\Warning;

/**
 * BBCodeHelperTest class
 */
class BBCodeHelperTest extends BBCodeTest
{
    /**
     * Called before every test method
     * @return void
     */
    public function setUp()
    {
        parent::setUp();

        $this->BBCode = new BBCodeHelper(new View());
    }

    /**
     * Tests for `__call()` method, with a no existing method
     * @test
     */
    public function testCallNoExistingMethod()
    {
        $this->expectException(Warning::class);
        $this->BBCode->noExisting();
    }
}
