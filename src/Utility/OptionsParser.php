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
 * An options parser
 */
class OptionsParser
{
    /**
     * Options
     * @var array
     */
    protected $options;

    /**
     * Construct
     * @param array $options Options to parse
     * @return \MeTools\Utility\OptionsParser
     * @uses $options
     */
    public function __construct(array $options = [])
    {
        $this->options = $options;

        return $this;
    }

    /**
     * Internal method to set a value
     * @param string $key Key value
     * @param mixed $value Value
     * @return void
     * @uses $options
     * @uses _toString()
     */
    protected function _setValue($key, $value)
    {
        $this->options[$key] = $this->_toString($value);
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

        return array_unique(preg_split('/\s/', $value, -1, PREG_SPLIT_NO_EMPTY));
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
     * Adds values.
     *
     * Example:
     * <code>
     * $this->add([
     *  'class' => 'this-is-my-class',
     *  'data-value => ['first-value', 'second-value'],
     * ]);
     * </code>
     *
     * To provide backward compatibility, this function can accept two
     * arguments (value key and value). Example:
     * <code>
     * $this->add('class','this-is-my-class');
     * $this->add('data-value, ['first-value', 'second-value']);
     * </code>
     * @param array $values Values
     * @return $this
     * @uses $options
     * @uses _setValue()
     * @uses _toArray()
     * @uses _toString()
     */
    public function add($values)
    {
        //If called two arguments, the first is the key, the second is the value
        if (func_num_args() === 2) {
            $values = [func_get_arg(0) => func_get_arg(1)];
        }

        foreach ($values as $key => $value) {
            //Turns value into a string
            $value = $this->_toString($value);

            if (isset($this->options[$key])) {
                //Chains new value to the existing value
                $value = $this->options[$key] . ' ' . $value;

                //Turns first into an array and finally into a string again.
                //Turning into array will also remove duplicates
                $this->options[$key] = $this->_toString($this->_toArray($value));
            } else {
                $this->_setValue($key, $value);
            }
        }

        return $this;
    }

    /**
     * Adds button classes.
     *
     * Classes can be passed as string or array, with or without the `btn-`
     *  prefix.
     *
     * Example:
     * <code>
     * $this->addButtonClasses('primary lg');
     * </code>
     *
     * Or:
     * <code>
     * $this->addButtonClasses(['btn-primary', ['lg']);
     * </code>
     * @param string|array $classes Classes (eg. `default`, `primary`,
     *  `success`, etc), with or without the `btn-` prefix
     * @return void
     * @uses _toArray()
     * @uses add()
     */
    public function addButtonClasses($classes = 'btn-default')
    {
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

        $this->add(['class' => $classes]);
    }

    /**
     * Adds default values.
     *
     * Example:
     * <code>
     * $this->addDefault([
     *  'class' => 'this-is-my-class',
     *  'data-value => ['first-value', 'second-value'],
     * ]);
     * </code>
     *
     * To provide backward compatibility, this function can accept two
     * arguments (value key and value). Example:
     * <code>
     * $this->addDefault('class','this-is-my-class');
     * $this->addDefault('data-value, ['first-value', 'second-value']);
     * </code>
     * @param array $values Values
     * @return \MeTools\Utility\OptionsParser
     * @uses $options
     * @uses _setValue()
     */
    public function addDefaults($values)
    {
        //If called two arguments, the first is the key, the second is the value
        if (func_num_args() === 2) {
            $values = [func_get_arg(0) => func_get_arg(1)];
        }

        foreach ($values as $key => $value) {
            if (!isset($this->options[$key])) {
                $this->_setValue($key, $value);
            }
        }

        return $this;
    }

    /**
     * Gets a value from options
     * @param string $key Key value
     * @return string
     * @uses $options
     */
    public function get($key)
    {
        if (!isset($this->options[$key])) {
            return null;
        }

        return $this->options[$key];
    }

    /**
     * Retunrs options as array
     * @return array
     * @uses $options
     */
    public function toArray()
    {
        return $this->options;
    }
}
