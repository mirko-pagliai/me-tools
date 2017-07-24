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
 * @since       2.13.1
 */
namespace MeTools\TestSuite\Traits;

/**
 * This trait contains a method to load all fixtures
 */
trait LoadAllFixturesTrait
{
    /**
     * Loads all fixtures declared in the `$fixtures` property
     * @return void
     */
    public function loadAllFixtures()
    {
        $fixtures = $this->getProperty($this->fixtureManager, '_fixtureMap');

        call_user_func_array([$this, 'loadFixtures'], array_keys($fixtures));
    }
}
