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
namespace MeTools\Test\TestCase\TestSuite\Traits;

use Cake\TestSuite\TestCase;
use MeTools\TestSuite\Traits\LoadAllFixturesTrait;
use Reflection\ReflectionTrait;

/**
 * LoadAllFixturesTraitTest class
 */
class LoadAllFixturesTraitTest extends TestCase
{
    use LoadAllFixturesTrait;
    use ReflectionTrait;

    /**
     * @var bool
     */
    public $autoFixtures = false;

    /**
     * @var array
     */
    public $fixtures = [
        'core.articles',
        'core.comments',
    ];

    /**
     * Test for `loadAllFixtures()` method
     * @test
     */
    public function testLoadAllFixtures()
    {
        $fixtureMap = $this->getProperty($this->fixtureManager, '_fixtureMap');

        $this->assertFalse($this->fixtureManager->isFixtureSetup('test', $fixtureMap['Articles']));
        $this->assertFalse($this->fixtureManager->isFixtureSetup('test', $fixtureMap['Comments']));

        $this->loadAllFixtures();

        $this->assertTrue($this->fixtureManager->isFixtureSetup('test', $fixtureMap['Articles']));
        $this->assertTrue($this->fixtureManager->isFixtureSetup('test', $fixtureMap['Comments']));
    }
}
