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

use MeTools\Model\Table\AppTable;
use MeTools\Model\Validation\AppValidator;
use MeTools\TestSuite\TestCase;

/**
 * AppTableTest class
 */
class AppTableTest extends TestCase
{
    /**
     * @var \MeTools\Model\Table\AppTable|\PHPUnit\Framework\MockObject\MockObject
     */
    protected AppTable $AppTable;

    /**
     * Called before every test method
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->AppTable = $this->createPartialMockForAbstractClass(AppTable::class);
    }

    /**
     * @test
     * @uses \MeTools\Model\Table\AppTable::initialize()
     */
    public function testInitialize(): void
    {
        $this->AppTable->initialize([]);
        $this->assertInstanceOf(AppValidator::class, $this->AppTable->getValidator());
    }
}
