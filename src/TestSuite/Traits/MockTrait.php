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
use Cake\View\View;

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
     * Mocks an helper
     * @param string $className Helper class name
     * @param array|null $methods The list of methods to mock
     * @return \PHPUnit\Framework\MockObject\MockObject
     */
    protected function getMockForHelper($className, $methods = [])
    {
        return $this->getMockBuilder($className)
            ->setMethods($methods)
            ->setConstructorArgs([new View])
            ->getMock();
    }

    /**
     * Mocks a shell
     * @param string $className Shell class name
     * @param array|null $methods The list of methods to mock
     * @return \PHPUnit\Framework\MockObject\MockObject
     */
    protected function getMockForShell($className, $methods = ['_stop', 'in'])
    {
        return $this->getMockBuilder($className)
            ->setMethods($methods)
            ->getMock();
    }

    /**
     * Mocks a table
     * @param string $className Table class name
     * @param array|null $methods The list of methods to mock
     * @return \PHPUnit\Framework\MockObject\MockObject
     * @todo remove with CakePHP 3.7, use instead getMockForModel()
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

    /**
     * Gets the classname for which a test is being performed, starting from the
     *  test class name.
     *
     * Example: class `MyPlugin\Test\TestCase\Controller\PagesControllerTest`
     *  will return the string `MyPlugin\Controller\PagesController`.
     * @param object $testClass A test class
     * @return string The class name for which a test is being performed
     * @since 2.18.0
     */
    protected function getOriginClassName($testClass)
    {
        $parts = explode('\\', get_class($testClass));
        array_splice($parts, 1, 2, []);
        $parts[] = substr(array_pop($parts), 0, -4);

        return implode('\\', $parts);
    }
}
