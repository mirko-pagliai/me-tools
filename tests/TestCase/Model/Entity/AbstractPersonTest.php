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

namespace MeTools\Test\TestCase\Model\Entity;

use App\Model\Entity\User;
use MeTools\TestSuite\TestCase;

/**
 * AbstractPersonTest class
 */
class AbstractPersonTest extends TestCase
{
    /**
     * @var \App\Model\Entity\User
     */
    protected User $Person;

    /**
     * Called before every test method
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->Person = new User([
            'first_name' => 'John',
            'last_name' => 'Smith',
        ]);
    }

    /**
     * Tests that all fields are accessible, both those set by `AbstractPerson` (in the constructor), and those of the
     *  entity extending `AbstractPerson`
     * @uses \MeTools\Model\Entity\AbstractPerson::__construct()
     * @uses \MeTools\Model\Entity\AbstractPerson::$_accessible
     * @test
     */
    public function testAccessibileFields(): void
    {
        $this->assertEquals([
            'id' => false,
            'first_name' => true,
            'last_name' => true,
            'username' => true,
        ], $this->Person->getAccessible());
    }

    /**
     * Tests that all virtual fields exist, both those set by `AbstractPerson` (in the constructor), and those of the
     *  entity extending `AbstractPerson`
     * @uses \MeTools\Model\Entity\AbstractPerson::__construct()
     * @uses \MeTools\Model\Entity\AbstractPerson::$_virtual
     * @test
     */
    public function testVirtualFields(): void
    {
        $this->assertEquals([
            'full_name',
            'short_username',
        ], $this->Person->getVirtual());
    }

    /**
     * @uses \MeTools\Model\Entity\AbstractPerson::_getFullName()
     * @test
     */
    public function testGetFullName(): void
    {
        $this->assertSame('John Smith', $this->Person->full_name);
    }

    /**
     * @uses \MeTools\Model\Entity\AbstractPerson::_getFullName()
     * @test
     */
    public function testGetFullNameWithoutFirstName(): void
    {
        $this->Person->unset('first_name');
        $this->expectExceptionMessage('Missing `first_name` field value for `' . User::class . '`');
        $this->Person->full_name;
    }

    /**
     * @uses \MeTools\Model\Entity\AbstractPerson::_getFullName()
     * @test
     */
    public function testGetFullNameWithoutLastName(): void
    {
        $this->Person->unset('last_name');
        $this->expectExceptionMessage('Missing `last_name` field value for `' . User::class . '`');
        $this->Person->full_name;
    }
}
