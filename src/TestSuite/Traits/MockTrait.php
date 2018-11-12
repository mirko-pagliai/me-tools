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
 * @since       2.17.5
 */
namespace MeTools\TestSuite\Traits;

use Cake\Controller\ComponentRegistry;
use Cake\Controller\Controller;
use Cake\Datasource\ConnectionManager;
use Cake\ORM\Entity;
use Cake\Utility\Inflector;

/**
 * A mock trait
 */
trait MockTrait
{
    /**
     * Gets the alias for a controller
     * @param string $className Controller class name
     * @return string
     */
    protected function getControllerAlias($className)
    {
        $parts = explode('\\', $className);

        return substr(array_pop($parts), 0, -10);
    }

    /**
     * Mocks a component
     * @param string $className Component class name
     * @param array|null $methods The list of methods to mock
     * @return \PHPUnit\Framework\MockObject\MockObject
     */
    protected function getMockForComponent($className, $methods = [])
    {
        return $this->getMockBuilder($className)
            ->setConstructorArgs([new ComponentRegistry(new Controller)])
            ->setMethods($methods)
            ->getMock();
    }

    /**
     * Mocks a controller
     * @param string $className Controller class name
     * @param array|null $methods The list of methods to mock
     * @param string $alias Controller alias
     * @return \PHPUnit\Framework\MockObject\MockObject
     * @uses getControllerAlias()
     */
    protected function getMockForController($className, $methods = [], $alias = null)
    {
        if (!class_exists($className)) {
            $this->fail('Class `' . $className . '` does not exist');
        }

        $alias = $alias ?: $this->getControllerAlias($className);

        return $this->getMockBuilder($className)
            ->setConstructorArgs([null, null, $alias])
            ->setMethods($methods)
            ->getMock();
    }

    /**
     * Mocks a table
     * @param string $className Table class name
     * @param array|null $methods The list of methods to mock
     * @return \PHPUnit\Framework\MockObject\MockObject
     */
    protected function getMockForTable($className, $methods = [])
    {
        $parts = explode('\\', $className);
        $alias = substr(array_pop($parts), 0, -5);
        $connection = ConnectionManager::get($className::defaultConnectionName());

        $table = $this->getMockBuilder($className)
            ->setMethods($methods)
            ->setConstructorArgs([compact('alias', 'connection', 'className')])
            ->getMock();

        $entityAlias = Inflector::classify(Inflector::underscore($alias));
        $entityClass = implode('\\', array_slice($parts, 0, -1)) . '\\Entity\\' . $entityAlias;

        if ($table->getEntityClass() === Entity::class && class_exists($entityClass)) {
            $table->setEntityClass($entityClass);
        }

        $this->getTableLocator()->set($alias, $table);

        return $table;
    }
}
