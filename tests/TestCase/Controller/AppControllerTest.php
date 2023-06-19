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

namespace MeTools\Test\TestCase\Controller;

use MeTools\Controller\AppController;
use MeTools\Controller\Component\FlashComponent;
use MeTools\TestSuite\TestCase;

/**
 * AppControllerTest class
 */
class AppControllerTest extends TestCase
{
    /**
     * @test
     * @uses \MeTools\Controller\AppController::initialize()
     */
    public function testInitialize(): void
    {
        $AppController = $this->getMockForAbstractClass(AppController::class);
        $AppController->initialize();
        $this->assertInstanceOf(FlashComponent::class, $AppController->components()->get('Flash'));
        $this->assertTrue($AppController->components()->has('RequestHandler'));
    }
}
