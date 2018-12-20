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
namespace MeTools\Test\TestCase\Command\Install;

use Cake\Core\Configure;
use MeTools\TestSuite\ConsoleIntegrationTestTrait;
use MeTools\TestSuite\TestCase;

/**
 * CreateVendorsLinksCommandTest class
 */
class CreateVendorsLinksCommandTest extends TestCase
{
    use ConsoleIntegrationTestTrait;

    /**
     * Called after every test method
     * @return void
     */
    public function tearDown()
    {
        parent::tearDown();

        safe_unlink_recursive(WWW_ROOT . 'vendor', 'empty');
    }

    /**
     * Tests for `execute()` method
     * @test
     */
    public function testExecute()
    {
        foreach (array_keys(Configure::read('VENDOR_LINKS')) as $link) {
            safe_create_file(ROOT . 'vendor' . DS . $link . DS . 'empty');
        }

        $this->exec('me_tools.create_vendors_links -v');
        $this->assertExitWithSuccess();

        foreach (Configure::read('VENDOR_LINKS') as $link) {
            $this->assertOutputContains('Link `' . rtr(WWW_ROOT) . 'vendor' . DS . $link . '` has been created');
        }

        $this->assertErrorEmpty();
    }
}
