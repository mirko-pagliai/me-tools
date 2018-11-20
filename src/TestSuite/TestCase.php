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
 * @since       2.14.0
 */
namespace MeTools\TestSuite;

use Cake\Http\BaseApplication;
use Cake\TestSuite\TestCase as CakeTestCase;
use MeTools\TestSuite\Traits\TestCaseTrait;

/**
 * TestCase class
 */
abstract class TestCase extends CakeTestCase
{
    use TestCaseTrait;

    /**
     * Application instance
     * @since 2.18.0
     * @var \Cake\Http\BaseApplication
     */
    protected $app;

    /**
     * Called before every test method
     * @return void
     * @uses $app
     */
    public function setUp()
    {
        parent::setUp();

        $this->app = $this->getMockForAbstractClass(BaseApplication::class, ['']);
        $this->app->addPlugin('MeTools')->pluginBootstrap();
    }

    /**
     * Called after every test method
     * @return void
     */
    public function tearDown()
    {
        parent::tearDown();

        safe_unlink_recursive(LOGS);
    }
}
