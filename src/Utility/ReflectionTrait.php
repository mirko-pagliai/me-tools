<?php
/**
 * This file is part of MeTools.
 *
 * MeTools is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * MeTools is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with MeTools.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @author      Mirko Pagliai <mirko.pagliai@gmail.com>
 * @copyright   Copyright (c) 2016, Mirko Pagliai for Nova Atlantis Ltd
 * @license     http://www.gnu.org/licenses/agpl.txt AGPL License
 * @link        http://git.novatlantis.it Nova Atlantis Ltd
 */
namespace MeTools\Utility;

/**
 * A Reflection trait
 */
trait ReflectionTrait
{
    /**
     * Invokes a method
     * @param object $object Instantiated object that we will run method on
     * @param string $methodName Method name to call
     * @param array $parameters Array of parameters to pass into method
     * @return mixed Method return
     */
    public function invokeMethod(&$object, $methodName, array $parameters = [])
    {
        $reflector = new \ReflectionMethod(get_class($object), $methodName);
        $reflector->setAccessible(true);

        return $reflector->invokeArgs($object, $parameters);
    }

    /**
     * Gets a property value
     * @param object $object Instantiated object that we will run method on
     * @param string $propertyName Property name
     * @return mixed Property value
     */
    public function getProperty(&$object, $propertyName)
    {
        $reflector = new \ReflectionProperty(get_class($object), $propertyName);
        $reflector->setAccessible(true);

        return $reflector->getValue($object);
    }

    /**
     * Sets a property value
     * @param object $object Instantiated object that we will run method on
     * @param string $propertyName Property name
     * @param mixed $propertyValue Property value you want to set
     * @return void
     */
    public function setProperty(&$object, $propertyName, $propertyValue)
    {
        $reflector = new \ReflectionProperty(get_class($object), $propertyName);
        $reflector->setAccessible(true);
        $reflector->setValue($object, $propertyValue);
    }
}
