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

namespace MeTools\Test\TestCase\Model\Table;

use App\Model\Table\UsersTable;
use Cake\ORM\Table;
use MeTools\Model\Validation\AppValidator;
use MeTools\TestSuite\TestCase;

/**
 * AppTableTest class
 */
class AppTableTest extends TestCase
{
    /**
     * @var string[]
     */
    protected array $fixtures = ['app.Users'];

    /**
     * @var \App\Model\Table\UsersTable|\Cake\ORM\Table
     */
    protected UsersTable|Table $Table;

    /**
     * @inheritDoc
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->Table ??= $this->getTableLocator()->get('Users');
    }

    /**
     * @test
     * @uses \MeTools\Model\Table\AppTable::initialize()
     */
    public function testInitialize(): void
    {
        $this->Table->initialize([]);
        $this->assertInstanceOf(AppValidator::class, $this->Table->getValidator());
    }

    /**
     * @test
     * @uses \MeTools\Model\Table\AppTable::findActive()
     */
    public function testFindActive(): void
    {
        $Query = $this->Table->find('active');

        $this->assertGreaterThan(0, $Query->all()->count());
        $this->assertStringEndsWith('FROM users Users WHERE Users.active = :c0', $Query->sql());
        $this->assertTrue($Query->getValueBinder()->bindings()[':c0']['value']);
    }
}
