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
 * An options parser for helpers that generate html code
 */
trait OptionsParserTrait
{
    /**
     * Internal method to set a value
     * @param string $key Key value
     * @param mixed $value Value
     * @param array $options Options
     * @return array Options
     * @uses _toString()
     */
    protected function _setValue($key, $value, array $options)
    {
        $options[$key] = $this->_toString($value);

        return $options;
    }

    /**
     * Turns a string into an array
     * @param mixed $value String
     * @return array
     */
    protected function _toArray($value)
    {
        if (!is_string($value)) {
            return $value;
        }

        return array_unique(preg_split('/\s+/', $value, -1, PREG_SPLIT_NO_EMPTY));
    }

    /**
     * Turns an array into a string
     * @param mixed $value Array
     * @return string
     */
    protected function _toString($value)
    {
        if (!is_array($value)) {
            return $value;
        }

        return implode(' ', array_unique($value));
    }

    /**
     * Adds button classes.
     *
     * Classes can be passed as string or array, with or without the `btn-`
     *  prefix.
     *
     * Example:
     * <code>
     * $this->addButtonClasses('primary lg', $options);
     * </code>
     *
     * Or:
     * <code>
     * $this->addButtonClasses(['btn-primary', 'lg'], $options);
     * </code>
     * @param array $options Options
     * @param string|array $classes Classes (eg. `default`, `primary`,
     *  `success`, etc), with or without the `btn-` prefix
     * @return array Options
     * @uses _toArray()
     * @uses optionsValues()
     */
    public function addButtonClasses(array $options, $classes = 'btn-default')
    {
        //If a valid class already exists, it just adds the `btn` class
        if (!empty($options['class']) && preg_match(
            '/btn\-?(default|primary|success|info|warning|danger|lg|sm|xs|block)/',
            $options['class']
        )) {
            return $this->optionsValues(['class' => 'btn'], $options);
        }

        $classes = $this->_toArray($classes);

        //Filters invalid classes and adds the `btn-` prefix to each class
        $classes = array_filter(array_map(function ($class) {
            //Filters invalid classes
            if (!preg_match('/^(btn\-)?(default|primary|success|info|warning|danger|lg|sm|xs|block)$/', $class)) {
                return false;
            }

            //Adds the `btn-` prefix to each class
            if (substr($class, 0, 4) !== 'btn-') {
                $class = sprintf('btn-%s', $class);
            }

            return $class;
        }, $classes));

        //Prepend the `btn` class
        array_unshift($classes, 'btn');

        return $this->optionsValues(['class' => $classes], $options);
    }

    /**
     * Adds default values.
     *
     * Example:
     * <code>
     * $this->optionsDefaults([
     *  'class' => 'this-is-my-class',
     *  'data-value => ['first-value', 'second-value'],
     * ], $options);
     * </code>
     *
     * To provide backward compatibility, this function can accept three
     * arguments (value key, value and options). Example:
     * <code>
     * $this->optionsDefaults('class', 'this-is-my-class', $options);
     * $this->optionsDefaults('data-value, [
     *  'first-value',
     *  'second-value',
     * ], $options);
     * </code>
     * @param array $values Values (array of key values and values)
     * @param array $options Options
     * @return array Options
     * @uses _setValue()
     */
    public function optionsDefaults($values, $options)
    {
        //If called with three arguments, the first is the key, the second is
        //  the value and the third are the options
        if (func_num_args() === 3 && is_array(func_get_arg(2))) {
            $values = [func_get_arg(0) => func_get_arg(1)];
            $options = func_get_arg(2);
        }

        foreach ($values as $key => $value) {
            if (!isset($options[$key])) {
                $options = $this->_setValue($key, $value, $options);
            }
        }

        return $options;
    }

    /**
     * Adds values.
     *
     * Example:
     * <code>
     * $this->optionsValues([
     *  'class' => 'this-is-my-class',
     *  'data-value => ['first-value', 'second-value'],
     * ], $options);
     * </code>
     *
     * To provide backward compatibility, this function can accept two
     * arguments (value key and value). Example:
     * <code>
     * $this->optionsValues('class','this-is-my-class', $options);
     * $this->optionsValues('data-value, [
     *  'first-value',
     *  'second-value',
     * ], $options);
     * </code>
     * @param array $values Values (array of key values and values)
     * @param array $options Options
     * @return array Options
     * @uses _setValue()
     * @uses _toArray()
     * @uses _toString()
     */
    public function optionsValues($values, $options)
    {
        //If called with three arguments, the first is the key, the second is
        //  the value and the third are the options
        if (func_num_args() === 3 && is_array(func_get_arg(2))) {
            $values = [func_get_arg(0) => func_get_arg(1)];
            $options = func_get_arg(2);
        }

        foreach ($values as $key => $value) {
            //Turns value into a string
            $value = $this->_toString($value);

            if (isset($options[$key])) {
                //Chains new value to the existing value
                $value = $options[$key] . ' ' . $value;

                //Turns first into an array and finally into a string again.
                //Turning into array will also remove duplicates
                $options[$key] = $this->_toString($this->_toArray($value));
            } else {
                $options = $this->_setValue($key, $value, $options);
            }
        }

        return $options;
    }
}
