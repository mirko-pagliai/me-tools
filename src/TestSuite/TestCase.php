ve  <?php
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
     * Setup the test case, backup the static object values so they can be
     * restored. Specifically backs up the contents of Configure and paths in
     *  App if they have not already been backed up
     * @return void
     */
    public function setUp()
    {
        parent::setUp();

        $app = $this->getMockForAbstractClass(BaseApplication::class, ['']);
        $app->addPlugin('MeTools')->pluginBootstrap();
    }

    /**
     * Teardown any static object changes and restore them
     * @return void
     */
    public function tearDown()
    {
        parent::tearDown();

        safe_unlink_recursive(LOGS);
    }
}
